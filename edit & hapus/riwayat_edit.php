<?php
require '../includes/session.php';
require '../includes/db.php';

$id = intval($_GET['id']);
$page = $_GET['page'] ?? 1;
$search = $_GET['search'] ?? '';

$data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM riwayat WHERE id = $id"));
if (!$data) {
    echo "Data tidak ditemukan.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $buku = mysqli_real_escape_string($conn, $_POST['buku']);
    $tgl_pinjam = $_POST['tanggal_pinjam'];
    $tgl_kembali = $_POST['tanggal_kembali'];

    mysqli_query($conn, "UPDATE riwayat SET 
        nama = '$nama', 
        buku = '$buku', 
        tanggal_pinjam = '$tgl_pinjam', 
        tanggal_kembali = '$tgl_kembali' 
        WHERE id = $id");

    // Redirect kembali ke halaman data sesuai search & page sebelumnya
    header("Location: /pages/riwayat.php?page=$page&search=$search&msg=edit");
    exit;
}
?>

<h2>Edit Riwayat</h2>
<form method="POST">
    <input type="text" name="nama" value="<?= htmlspecialchars($data['nama']) ?>" required>
    <input type="text" name="buku" value="<?= htmlspecialchars($data['buku']) ?>" required>
    <input type="date" name="tanggal_pinjam" value="<?= $data['tanggal_pinjam'] ?>" required>
    <input type="date" name="tanggal_kembali" value="<?= $data['tanggal_kembali'] ?>">
    <button type="submit">Update</button>
</form>
<a href="/pages/riwayat.php?page=<?= $page ?>&search=<?= $search ?>">← Kembali</a>
