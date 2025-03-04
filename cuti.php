<?php
include 'config.php';
redirect_if_not_authenticated();

// Handle leave request submission (only for non-admins)
if (!is_admin() && $_SERVER['REQUEST_METHOD'] == 'POST' && verify_csrf_token($_POST['csrf_token'])) {
    try {
        $stmt = $pdo->prepare("INSERT INTO cuti_sakit (nip, jenis, tanggal_mulai, tanggal_selesai, alasan) 
                             VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $_SESSION['nip'],
            sanitize($_POST['jenis']),
            sanitize($_POST['tanggal_mulai']),
            sanitize($_POST['tanggal_selesai']),
            sanitize($_POST['alasan'])
        ]);
        $_SESSION['success'] = "Cuti submitted successfully";
        header("Location: cuti.php");
        exit();
    } catch(PDOException $e) {
        error_log("Cuti error: " . $e->getMessage());
        $_SESSION['error'] = "Failed to submit cuti";
    }
}

// Handle leave approval/rejection by admin
if (is_admin()) {
    if (isset($_GET['approve_cuti'])) {
        try {
            $stmt = $pdo->prepare("UPDATE cuti_sakit SET status = 'Accepted' WHERE id = ?");
            $stmt->execute([sanitize($_GET['approve_cuti'])]);
            $_SESSION['success'] = "Cuti approved successfully.";
            header("Location: cuti.php");
            exit();
        } catch(PDOException $e) {
            $_SESSION['error'] = "Failed to approve cuti.";
        }
    } elseif (isset($_GET['reject_cuti'])) {
        try {
            $stmt = $pdo->prepare("UPDATE cuti_sakit SET status = 'Rejected' WHERE id = ?");
            $stmt->execute([sanitize($_GET['reject_cuti'])]);
            $_SESSION['success'] = "Cuti rejected successfully.";
            header("Location: cuti.php");
            exit();
        } catch(PDOException $e) {
            $_SESSION['error'] = "Failed to reject cuti.";
        }
    }
}

// Fetch leave requests: all for admins, user's own for non-admins
try {
    if (is_admin()) {
        $stmt = $pdo->query("SELECT c.*, p.nama FROM cuti_sakit c JOIN pegawai p ON c.nip = p.nip");
        $cuti = $stmt->fetchAll();
    } else {
        $stmt = $pdo->prepare("SELECT * FROM cuti_sakit WHERE nip = ?");
        $stmt->execute([$_SESSION['nip']]);
        $cuti = $stmt->fetchAll();
    }
} catch(PDOException $e) {
    error_log("Cuti fetch error: " . $e->getMessage());
    $cuti = [];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cuti</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        <div class="card">
            <h2>Pengajuan Cuti</h2>
            
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error"><?= $_SESSION['error'] ?></div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <!-- Show the form only to non-admins -->
            <?php if (!is_admin()): ?>
            <form method="post">
                <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                <div class="form-group">
                    <label>Jenis Cuti</label>
                    <select name="jenis" class="form-control" required>
                        <option value="cuti">Cuti</option>
                        <option value="sakit">Sakit</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Alasan</label>
                    <textarea name="alasan" class="form-control" rows="4" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
            <?php endif; ?>

            <h3>History Cuti</h3>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <?php if (is_admin()): ?>
                                <th>Nama</th>
                            <?php endif; ?>
                            <th>Tanggal</th>
                            <th>Jenis</th>
                            <th>Status</th>
                            <?php if (is_admin()): ?>
                                <th>Aksi</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cuti as $c): ?>
                        <tr>
                            <?php if (is_admin()): ?>
                                <td><?= htmlspecialchars($c['nama']) ?></td>
                            <?php endif; ?>
                            <td><?= date('d M Y', strtotime($c['tanggal_mulai'])) ?> - <?= date('d M Y', strtotime($c['tanggal_selesai'])) ?></td>
                            <td><?= ucfirst($c['jenis']) ?></td>
                            <td><span class="status-badge status-<?= strtolower($c['status'] === 'Accepted' ? 'ok' : $c['status']) ?>"><?= $c['status'] ?></span></td>
                            <?php if (is_admin() && $c['status'] === 'Pending'): ?>
                                <td>
                                    <a href="cuti.php?approve_cuti=<?= $c['id'] ?>" class="btn btn-primary">Accept</a>
                                    <a href="cuti.php?reject_cuti=<?= $c['id'] ?>" class="btn btn-danger">Reject</a>
                                </td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>