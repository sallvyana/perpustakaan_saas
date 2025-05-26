<?php
require '../includes/session.php';
require '../includes/db.php';
include '../includes/sidebar.php';
?>

<div style="margin-left: 220px; padding: 20px;">
    <h2>Selamat Datang, <?= $_SESSION['nama']; ?>!</h2>

    <?php if ($_SESSION['role'] == 'admin'): ?>
        <p><strong>Anda login sebagai <span style="color:green;">Admin</span>.</strong></p>
        <p>Selamat datang di halaman pengelolaan perpustakaan digital <b>STEMBAYO e-Library</b>.</p>

        <h3>Statistik Singkat:</h3>
        <ul>
            <li>Total Buku: <?= mysqli_num_rows(mysqli_query($conn, "SELECT * FROM buku")); ?></li>
            <li>Total Peminjam: <?= mysqli_num_rows(mysqli_query($conn, "SELECT * FROM peminjam")); ?></li>
            <li>Total Transaksi: <?= mysqli_num_rows(mysqli_query($conn, "SELECT * FROM riwayat")); ?></li>
        </ul>

    <?php else: ?>
        <p><strong>Anda login sebagai <span style="color:blue;">Siswa</span>.</strong></p>
        <p>Selamat datang di <b>STEMBAYO e-Library</b>! Website ini menyediakan informasi buku dan riwayat peminjaman.</p>
        <p>Jika ingin meminjam buku, hubungi petugas perpustakaan.</p>
    <?php endif; ?>
</div>
