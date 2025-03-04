<?php
include 'config.php';
redirect_if_not_authenticated();

if (!is_admin()) {
    header("Location: dashboard.php");
    exit();
}

try {
    $stmt = $pdo->query("SELECT * FROM pegawai");
    $users = $stmt->fetchAll();
} catch(PDOException $e) {
    error_log("Fetch users error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Data</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        <div class="card">
            <h2>Update Data Pegawai</h2>
            
            <h3>Data Pegawai</h3>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>NIP</th>
                            <th>Nama</th>
                            <th>Jabatan</th>
                            <th>Email</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($users as $user): ?>
                        <tr>
                            <td><?= $user['nip'] ?></td>
                            <td><?= $user['nama'] ?></td>
                            <td><?= $user['jabatan'] ?></td>
                            <td><?= $user['email'] ?></td>
                            <td>
                                <a href="edit_user.php?nip=<?= $user['nip'] ?>" class="btn btn-primary">Edit</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>