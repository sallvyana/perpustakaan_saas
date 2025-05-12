<?php
require '../includes/session.php';
require '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $kelas = $_POST['kelas'];
    $id_buku = intval($_POST['buku']);
    $tanggal_pinjam = $_POST['tanggal_pinjam'];

    $buku_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM buku WHERE id = $id_buku"));
    if ($buku_data && $buku_data['stok'] > 0) {
        $judul_buku = $buku_data['judul'];
        mysqli_query($conn, "INSERT INTO peminjam (nama, kelas, buku, tanggal_pinjam) VALUES ('$nama', '$kelas', '$judul_buku', '$tanggal_pinjam')");
        mysqli_query($conn, "UPDATE buku SET stok = stok - 1 WHERE id = $id_buku");
        header("Location: /pages/peminjam.php");
        exit;
    } else {
        $error = "Stok buku habis atau tidak ditemukan!";
    }
}
?>

<h2>Tambah Data Peminjam</h2>
<?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
<form method="POST">
    <input type="text" name="nama" placeholder="Nama" required>
    <input type="text" name="kelas" placeholder="Kelas" required>
    <select name="buku" required>
        <option value="">-- Pilih Buku --</option>
        <?php
        $result = mysqli_query($conn, "SELECT * FROM buku WHERE stok > 0");
        while ($b = mysqli_fetch_assoc($result)):
        ?>
            <option value="<?= $b['id']; ?>"><?= htmlspecialchars($b['judul']); ?></option>
        <?php endwhile; ?>
    </select>
    <input type="date" name="tanggal_pinjam" required>
    <button type="submit">Tambah</button>
</form>
