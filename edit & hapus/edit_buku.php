<?php
// Mulai session dan koneksi database
session_start();
include '../includes/db.php';

// Cek apakah parameter id ada
if (!isset($_GET['id'])) {
    echo "ID buku tidak ditemukan.";
    exit;
}

$id = intval($_GET['id']);

// Ambil data buku berdasarkan id
$result = mysqli_query($conn, "SELECT * FROM buku WHERE id = $id");
if (mysqli_num_rows($result) == 0) {
    echo "Data buku tidak ditemukan.";
    exit;
}

$buku = mysqli_fetch_assoc($result);

$error = '';

// Proses form jika metode POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data POST dengan fallback kosong
    $judul = $_POST['judul'] ?? '';
    $kategori = $_POST['kategori'] ?? '';
    $penulis = $_POST['penulis'] ?? '';
    $tahun = $_POST['tahun'] ?? '';
    $stok = $_POST['stok'] ?? '';

    // Validasi input
    if (trim($judul) === '' || trim($kategori) === '' || trim($penulis) === '') {
        $error = "Judul, kategori, dan penulis wajib diisi.";
    } elseif (!is_numeric($tahun) || !is_numeric($stok) || $tahun === '' || $stok === '') {
        $error = "Tahun dan stok harus diisi dengan angka yang valid.";
    } else {
        // Sanitasi input
        $judul = mysqli_real_escape_string($conn, $judul);
        $kategori = mysqli_real_escape_string($conn, $kategori);
        $penulis = mysqli_real_escape_string($conn, $penulis);
        $tahun = intval($tahun);
        $stok = intval($stok);

        // Query update
        $update = mysqli_query($conn, "UPDATE buku SET 
            judul = '$judul',
            kategori = '$kategori',
            penulis = '$penulis',
            tahun = $tahun,
            stok = $stok
            WHERE id = $id");

        if ($update) {
            // Redirect ke halaman daftar buku (pastikan path sesuai)
            header("Location: /pages/buku.php");
            exit;
        } else {
            $error = "Gagal mengupdate data: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Buku</title>
</head>
<body>
    <h2>Edit Buku</h2>

    <?php if ($error): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="post">
        <label>Judul:</label><br>
        <input type="text" name="judul" value="<?= htmlspecialchars($buku['judul']) ?>" required><br><br>

        <label>Kategori:</label><br>
        <input type="text" name="kategori" value="<?= htmlspecialchars($buku['kategori']) ?>" required><br><br>

        <label>Penulis:</label><br>
        <input type="text" name="penulis" value="<?= htmlspecialchars($buku['penulis']) ?>" required><br><br>

        <label>Tahun Terbit:</label><br>
        <input type="number" name="tahun" value="<?= htmlspecialchars($buku['tahun']) ?>" required><br><br>

        <label>Jumlah Stok:</label><br>
        <input type="number" name="stok" value="<?= htmlspecialchars($buku['stok']) ?>" required><br><br>

        <button type="submit">Update</button>
        <button type="button" onclick="window.location.href='/pages/buku.php'" style="margin-left: 10px;">Batal</button>

    </form>
</body>
</html>
