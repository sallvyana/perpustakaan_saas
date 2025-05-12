<?php
$role = $_SESSION['role'];
?>

<div style="width: 200px; float: left;">
    <h3>Menu</h3>
    <ul>
        <li><a href="../pages/dashboard.php">Dashboard</a></li>
        <li><a href="../pages/buku.php">Data Buku</a></li>
        <li><a href="../pages/peminjam.php">Data Peminjam</a></li>
        <li><a href="../pages/riwayat.php">Riwayat Peminjaman</a></li>
        <?php if ($role == 'admin'): ?>
            <li><a href="../pages/users.php">Kelola Pengguna</a></li>
        <?php endif; ?>
        <li><a href="../auth/logout.php">Logout</a></li>
    </ul>
</div>
