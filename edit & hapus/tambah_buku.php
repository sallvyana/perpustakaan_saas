<?php
require '../includes/session.php';
require '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = $_POST['judul'];
    $kategori = $_POST['kategori'];
    $penulis = $_POST['penulis'];
    $tahun = $_POST['tahun'];
    $stok = $_POST['stok'];

    mysqli_query($conn, "INSERT INTO buku (judul, kategori, penulis, tahun, stok) VALUES ('$judul', '$kategori', '$penulis', '$tahun', '$stok')");
    header("Location: /pages/buku.php");
    exit;
}
?>

<div style="margin-left: 20px;">
    <h2>Tambah Buku</h2>
    <form method="POST">
        <input type="text" name="judul" placeholder="Judul" required><br><br>
        <input type="text" name="kategori" placeholder="Kategori" required><br><br>
        <input type="text" name="penulis" placeholder="Penulis" required><br><br>
        <input type="number" name="tahun" placeholder="Tahun" required><br><br>
        <input type="number" name="stok" placeholder="Stok" required><br><br>
        <button type="submit">Kirim</button>
        <a href="/pages/buku.php"><button type="button">Kembali</button></a>
    </form>
</div>
