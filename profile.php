<?php
include 'config.php';
redirect_if_not_authenticated();

try {
    $stmt = $pdo->prepare("SELECT * FROM pegawai WHERE nip = ?");
    $stmt->execute([$_SESSION['nip']]);
    $user = $stmt->fetch();
} catch(PDOException $e) {
    error_log("Profile fetch error: " . $e->getMessage());
    $user = null;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        <div class="card">
            <h2>User Profile</h2>
            <?php if ($user): ?>
                <div class="profile-info">
                    <p><strong>NIP:</strong> <?= $user['nip'] ?></p>
                    <p><strong>Name:</strong> <?= $user['nama'] ?></p>
                    <p><strong>Jabatan:</strong> <?= $user['jabatan'] ?></p>
                    <p><strong>Email:</strong> <?= $user['email'] ?></p>
                </div>
            <?php else: ?>
                <p>Profile not found.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>