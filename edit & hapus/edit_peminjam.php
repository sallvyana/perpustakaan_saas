<?php
require '../includes/session.php';
require '../includes/db.php';

$id = intval($_GET['id']);
$data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM peminjam WHERE id=$id"));

if (!$data) {
    echo "Data tidak ditemukan.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $kelas = $_POST['kelas'];
    $buku = $_POST['buku'];
    $tanggal_pinjam = $_POST['tanggal_pinjam'];

    mysqli_query($conn, "UPDATE peminjam SET nama='$nama', kelas='$kelas', buku='$buku', tanggal_pinjam='$tanggal_pinjam' WHERE id=$id");
    header("Location: /pages/peminjam.php");
    exit;
}
?>

<h2>Edit Data Peminjam</h2>
<form method="POST">
    <input type="text" name="nama" value="<?= htmlspecialchars($data['nama']); ?>" required>
    <input type="text" name="kelas" value="<?= htmlspecialchars($data['kelas']); ?>" required>
    <input type="text" name="buku" value="<?= htmlspecialchars($data['buku']); ?>" required>
    <input type="date" name="tanggal_pinjam" value="<?= $data['tanggal_pinjam']; ?>" required>
    <button type="submit">Simpan</button>
</form>
