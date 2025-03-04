<?php
include 'config.php';

if (is_authenticated()) {
    header("Location: dashboard.php");
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die("CSRF token validation failed");
    }

    $nip = sanitize($_POST['nip']);
    $password = $_POST['password'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM pegawai WHERE nip = ?");
        $stmt->execute([$nip]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id();
            $_SESSION['logged_in'] = true;
            $_SESSION['nip'] = $user['nip'];
            $_SESSION['user_role'] = $user['role'];
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid credentials";
        }
    } catch(PDOException $e) {
        error_log("Login error: " . $e->getMessage());
        $error = "Authentication failed";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <?php if ($error): ?>
            <div class="alert alert-error"><?= $error ?></div>
        <?php endif; ?>

        <form method="post">
            <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
            <div class="form-group">
                <label for="nip">NIP:</label>
                <input type="text" name="nip" required class="form-control">
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" required class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>

        <div class="register-link">
            <p>Belum punya akun? <a href="registrasi.php">Daftar Sekarang</a></p>
        </div>
    </div>
</body>
</html>