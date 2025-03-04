<?php
include 'config.php';
redirect_if_not_authenticated();

if (!is_admin() && $_SERVER['REQUEST_METHOD'] == 'POST' && verify_csrf_token($_POST['csrf_token'])) {
    $nip = $_SESSION['nip'];
    $tanggal = sanitize($_POST['tanggal']);
    $status = sanitize($_POST['status']);

    try {
        $stmt = $pdo->prepare("INSERT INTO kehadiran (nip, tanggal, status) VALUES (?, ?, ?)");
        $stmt->execute([$nip, $tanggal, $status]);
        $_SESSION['success'] = "Absensi berhasil ditambahkan!";
    } catch(PDOException $e) {
        error_log("Kehadiran error: " . $e->getMessage());
        $_SESSION['error'] = "Gagal menyimpan data kehadiran.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        <div class="card dashboard-content">
            <h1>Selamat Datang</h1>
            <p>Selamat datang di dashboard, pilih menu yang sesuai di atas.</p>
        </div>
        
        <?php if (!is_admin()): ?>
        <div class="card">
            <h2>Absensi Harian</h2>
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error"><?= $_SESSION['error'] ?></div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
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
        </div>
        <?php endif; ?>
        
        <div class="center-content">
            <p><strong>Politeknik Siber dan Sandi Negara</strong></p>
            <img src="https://afterschool.id/wp-content/uploads/2023/01/logo-poltekssn-transparent.png" alt="Logo Poltek SSN">
        </div>
    </div>
</body>
</html>