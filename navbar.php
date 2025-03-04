<?php if (is_authenticated()): ?>
<nav class="navbar">
    <ul class="navbar-nav">
        <li><a class="nav-link" href="dashboard.php">Dashboard</a></li>
        <?php if (is_admin()): ?>
            <li><a class="nav-link" href="kehadiran.php">Kehadiran</a></li>
            <li><a class="nav-link" href="update.php">Update Data</a></li>
        <?php endif; ?>
        <li><a class="nav-link" href="cuti.php">Cuti</a></li>
        <li><a class="nav-link" href="logout.php">Logout</a></li>
        <li><a class="nav-link" href="profile.php">Profile</a></li>
    </ul>
</nav>
<?php endif; ?>