<?php
include 'config.php';
redirect_if_not_authenticated();

if (!is_admin()) {
    header("Location: dashboard.php");
    exit();
}

$nip = sanitize($_GET['nip']);

try {
    $stmt = $pdo->prepare("SELECT * FROM pegawai WHERE nip = ?");
    $stmt->execute([$nip]);
    $user = $stmt->fetch();
    if (!$user) {
        $_SESSION['error'] = "User not found.";
        header("Location: update.php");
        exit();
    }
} catch(PDOException $e) {
    error_log("Fetch user error: " . $e->getMessage());
    $_SESSION['error'] = "Failed to fetch user data.";
    header("Location: update.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && verify_csrf_token($_POST['csrf_token'])) {
    $nama = sanitize($_POST['nama']);
    $email = sanitize($_POST['email']);
    $jabatan = sanitize($_POST['jabatan']);
    
    try {
        $stmt = $pdo->prepare("UPDATE pegawai SET nama = ?, email = ?, jabatan = ? WHERE nip = ?");
        $stmt->execute([$nama, $email, $jabatan, $nip]);
        $_SESSION['success'] = "Data pegawai berhasil diupdate!";
        header("Location: update.php");
        exit();
    } catch(PDOException $e) {
        $_SESSION['error'] = "Gagal mengupdate data pegawai.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pegawai</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        <div class="card">
            <h2>Edit Data Pegawai</h2>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error"><?= $_SESSION['error'] ?></div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
            
            <form method="post">
                <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                <div class="form-group">
                    <label>NIP:</label>
                    <input type="text" value="<?= $user['nip'] ?>" disabled class="form-control">
                </div>
                <div class="form-group">
                    <label>Nama:</label>
                    <input type="text" name="nama" value="<?= $user['nama'] ?>" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Jabatan:</label>
                    <input type="text" name="jabatan" value="<?= $user['jabatan'] ?>" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" name="email" value="<?= $user['email'] ?>" required class="form-control">
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    </div>
</body>
</html>