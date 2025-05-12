<?php
require '../includes/session.php';
require '../includes/db.php';
include '../includes/sidebar.php';

// Hapus
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    mysqli_query($conn, "DELETE FROM peminjam WHERE id=$id");
    header("Location: peminjam.php");
    exit;
}

// Kembalikan
if (isset($_GET['kembalikan'])) {
    $id = intval($_GET['kembalikan']);
    $data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM peminjam WHERE id=$id"));
    if ($data) {
        // Insert ke riwayat
        $nama = $data['nama'];
        $buku = $data['buku'];
        $tgl_pinjam = $data['tanggal_pinjam'];
        $tgl_kembali = date('Y-m-d');

        mysqli_query($conn, "INSERT INTO riwayat (nama, buku, tanggal_pinjam, tanggal_kembali) 
            VALUES ('$nama', '$buku', '$tgl_pinjam', '$tgl_kembali')");

        // Tambah stok buku kembali
        mysqli_query($conn, "UPDATE buku SET stok = stok + 1 WHERE judul = '$buku'");

        // Hapus dari peminjam
        mysqli_query($conn, "DELETE FROM peminjam WHERE id=$id");

        header("Location: peminjam.php");
        exit;
    }
}

?>

<div style="margin-left: 220px;">
    <h2>Data Peminjam</h2>

    <?php if ($_SESSION['role'] == 'admin'): ?>
        <a href="/edit & hapus/tambah_peminjam.php"><button>Tambah Peminjam</button></a>
    <?php endif; ?>

    <!-- SEARCH -->
    <form method="GET" style="margin: 10px 0;">
        <input type="text" name="search" placeholder="Cari nama atau buku" value="<?= $_GET['search'] ?? '' ?>">
        <button type="submit">Cari</button>
    </form>

    <!-- TABEL -->
    <table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <th>No</th><th>Nama</th><th>Kelas</th><th>Buku</th><th>Tgl Pinjam</th>
            <?php if ($_SESSION['role'] == 'admin'): ?><th>Aksi</th><?php endif; ?>
        </tr>

        <?php
        $batas = 10;
        $halaman = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
        $mulai = ($halaman - 1) * $batas;

        $search = $_GET['search'] ?? '';
        $where = !empty($search) ? "WHERE nama LIKE '%$search%' OR buku LIKE '%$search%'" : '';

        $total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM peminjam $where"))['total'];
        $pages = ceil($total / $batas);

        $query = mysqli_query($conn, "SELECT * FROM peminjam $where ORDER BY id DESC LIMIT $mulai, $batas");
        $no = $mulai + 1;

        while ($row = mysqli_fetch_assoc($query)):
        ?>
        <tr>
            <td><?= $no++; ?></td>
            <td><?= htmlspecialchars($row['nama']); ?></td>
            <td><?= htmlspecialchars($row['kelas']); ?></td>
            <td><?= htmlspecialchars($row['buku']); ?></td>
            <td><?= htmlspecialchars($row['tanggal_pinjam']); ?></td>
            <?php if ($_SESSION['role'] == 'admin'): ?>
            <td>
                <a href="/edit & hapus/edit_peminjam.php?id=<?= $row['id']; ?>">Edit</a> |
                <a href="peminjam.php?kembalikan=<?= $row['id']; ?>" onclick="return confirm('Sudah dikembalikan?')">Kembalikan</a> |
                <a href="peminjam.php?hapus=<?= $row['id']; ?>" onclick="return confirm('Hapus data ini?')">Hapus</a>
            </td>
            <?php endif; ?>
        </tr>
        <?php endwhile; ?>
    </table>

    <!-- PAGINATION -->
    <div style="margin-top: 15px;">
        <?php for ($i = 1; $i <= $pages; $i++): ?>
            <a href="?halaman=<?= $i ?>&search=<?= urlencode($search) ?>" 
                style="margin-right: 5px; <?= ($halaman == $i ? 'font-weight: bold;' : '') ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>
    </div>
</div>
