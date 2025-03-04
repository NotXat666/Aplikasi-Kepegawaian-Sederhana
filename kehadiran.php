<?php
include 'config.php';
redirect_if_not_authenticated();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && verify_csrf_token($_POST['csrf_token'])) {
    $nip = $_SESSION['nip'];
    $tanggal = sanitize($_POST['tanggal']);
    $status = sanitize($_POST['status']);

    try {
        $stmt = $pdo->prepare("INSERT INTO kehadiran (nip, tanggal, status) VALUES (?, ?, ?)");
        $stmt->execute([$nip, $tanggal, $status]);
        $_SESSION['success'] = "Absensi berhasil ditambahkan!";
        header("Location: kehadiran.php");
        exit();
    } catch(PDOException $e) {
        error_log("Kehadiran error: " . $e->getMessage());
        $_SESSION['error'] = "Gagal menyimpan data kehadiran.";
    }
}

try {
    $kehadiran = [];
    if (is_admin()) {
        $stmt = $pdo->query("SELECT * FROM kehadiran");
        $kehadiran = $stmt->fetchAll();
    }
} catch(PDOException $e) {
    error_log("Kehadiran fetch error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Kehadiran</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        <div class="card">
            <h2>Rekap Kehadiran</h2>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error"><?= $_SESSION['error'] ?></div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
            
            <?php if (!is_admin()): ?>
            <form method="post">
                <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                <div class="form-group">
                    <label>Tanggal:</label>
                    <input type="date" name="tanggal" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Status:</label>
                    <select name="status" required class="form-control">
                        <option value="hadir">Hadir</option>
                        <option value="izin">Izin</option>
                        <option value="alfa">Alfa</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </form>
            <?php endif; ?>

            <?php if (is_admin()): ?>
            <h3>Data Kehadiran</h3>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>NIP</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($kehadiran as $k): ?>
                        <tr>
                            <td><?= $k['nip'] ?></td>
                            <td><?= $k['tanggal'] ?></td>
                            <td><?= $k['status'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>