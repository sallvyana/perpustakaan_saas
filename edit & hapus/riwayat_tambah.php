<?php
require '../includes/session.php';
require '../includes/db.php';

// Cek apakah user bukan admin
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../pages/riwayat.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_peminjam = intval($_POST['id_peminjam']);
    $id_buku = intval($_POST['id_buku']);
    $tanggal_pinjam = $_POST['tanggal_pinjam'];
    $tanggal_kembali = $_POST['tanggal_kembali'];

    // Ambil data nama dan judul buku
    $peminjam = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama FROM peminjam WHERE id = $id_peminjam"));
    $buku = mysqli_fetch_assoc(mysqli_query($conn, "SELECT judul FROM buku WHERE id = $id_buku"));

    if ($peminjam && $buku) {
        $nama = mysqli_real_escape_string($conn, $peminjam['nama']);
        $judul = mysqli_real_escape_string($conn, $buku['judul']);

        // Tambah ke riwayat dan kurangi stok buku
        mysqli_query($conn, "INSERT INTO riwayat (nama, buku, tanggal_pinjam, tanggal_kembali) 
            VALUES ('$nama', '$judul', '$tanggal_pinjam', '$tanggal_kembali')");

        mysqli_query($conn, "UPDATE buku SET stok = stok - 1 WHERE id = $id_buku");

        // Redirect ke halaman riwayat
        header("Location: ../pages/riwayat.php?msg=sukses");
        exit;
    }
}
?>

<div style="margin-left: 2opx;">
    <h2>Tambah Riwayat Peminjaman</h2>

    <form method="POST" style="margin-top: 20px;">
        <label>Peminjam:</label><br>
        <select name="id_peminjam" required>
            <option value="">-- Pilih Peminjam --</option>
            <?php
            $res = mysqli_query($conn, "SELECT * FROM peminjam");
            while ($row = mysqli_fetch_assoc($res)) {
                echo "<option value='{$row['id']}'>{$row['nama']} ({$row['kelas']})</option>";
            }
            ?>
        </select><br><br>

        <label>Buku:</label><br>
        <select name="id_buku" required>
            <option value="">-- Pilih Buku --</option>
            <?php
            $res = mysqli_query($conn, "SELECT * FROM buku WHERE stok > 0");
            while ($row = mysqli_fetch_assoc($res)) {
                echo "<option value='{$row['id']}'>{$row['judul']}</option>";
            }
            ?>
        </select><br><br>

        <label>Tanggal Pinjam:</label><br>
        <input type="date" name="tanggal_pinjam" required><br><br>

        <label>Tanggal Kembali:</label><br>
        <input type="date" name="tanggal_kembali"><br><br>

        <button type="submit">Simpan</button>
        <a href="../pages/riwayat.php"><button type="button">Kembali</button></a>
    </form>
</div>
