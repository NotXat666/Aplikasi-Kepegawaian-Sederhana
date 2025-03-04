<?php
include 'config.php';

if($_SERVER['REQUEST_METHOD'] == 'POST' && verify_csrf_token($_POST['csrf_token'])) {
    $nip = sanitize($_POST['nip']);
    $nama = sanitize($_POST['nama']);
    $jabatan = sanitize($_POST['jabatan']);
    $email = sanitize($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = 'user';

    try {
        $stmt = $pdo->prepare("INSERT INTO pegawai (nip, nama, jabatan, email, password, role) VALUES (?,?,?,?,?,?)");
        $stmt->execute([$nip, $nama, $jabatan, $email, $password, $role]);
        $success = "Pegawai berhasil diregistrasi!";
    } catch(PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Pegawai</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="card">
            <h2>Registrasi Pegawai Baru</h2>
            <?php 
            if(isset($success)) echo "<div class='alert alert-success'>$success</div>";
            if(isset($error)) echo "<div class='alert alert-error'>$error</div>";
            ?>
            <form method="post">
                <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                <div class="form-group">
                    <label>NIP:</label>
                    <input type="text" name="nip" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Nama:</label>
                    <input type="text" name="nama" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Jabatan:</label>
                    <input type="text" name="jabatan" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" name="email" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Password:</label>
                    <input type="password" name="password" required class="form-control">
                </div>
                <button type="submit" class="btn btn-primary">Daftarkan</button>
            </form>

            <div class="back-to-login">
                <a href="index.php" class="btn btn-secondary">Kembali ke Halaman Login</a>
            </div>
        </div>
    </div>
</body>
</html>