<?php
require '../includes/session.php';
require '../includes/db.php';
include '../includes/sidebar.php';

// Hapus data jika ada parameter ?hapus=id
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    mysqli_query($conn, "DELETE FROM riwayat WHERE id = $id");
    header("Location: riwayat.php?msg=hapus");
    exit;
}

$search = $_GET['search'] ?? '';
$page = $_GET['page'] ?? 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Hitung total data
$totalQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM riwayat WHERE nama LIKE '%$search%' OR buku LIKE '%$search%'");
$totalData = mysqli_fetch_assoc($totalQuery)['total'];
$totalPage = ceil($totalData / $limit);

// Ambil data
$query = mysqli_query($conn, "SELECT * FROM riwayat 
    WHERE nama LIKE '%$search%' OR buku LIKE '%$search%'
    ORDER BY id DESC 
    LIMIT $limit OFFSET $offset");
?>

<div style="margin-left: 220px;">
    <h2>Riwayat Peminjaman Buku</h2>

    <form method="GET" style="margin-bottom: 10px;">
        <input type="text" name="search" placeholder="Cari nama/buku..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Cari</button>
    </form>

<table border="1" cellpadding="8" cellspacing="0" width="100%">
    <tr style="background-color: #f2f2f2;">
        <th>No</th>
        <th>Nama</th>
        <th>Buku</th>
        <th>Tgl Pinjam</th>
        <th>Tgl Kembali</th>
        <?php if ($_SESSION['role'] == 'admin') echo '<th>Aksi</th>'; ?>
    </tr>
    <?php $no = $offset + 1; ?>
    <?php while ($row = mysqli_fetch_assoc($query)): ?>
    <tr>
        <td><?= $no++ ?></td>
        <td><?= htmlspecialchars($row['nama']) ?></td>
        <td><?= htmlspecialchars($row['buku']) ?></td>
        <td><?= $row['tanggal_pinjam'] ?></td>
        <td><?= $row['tanggal_kembali'] ?: '-' ?></td>
        <?php if ($_SESSION['role'] == 'admin'): ?>
        <td>
            <a href="riwayat_edit.php?id=<?= $row['id'] ?>&page=<?= $page ?>&search=<?= $search ?>">Edit</a> |
            <a href="riwayat.php?hapus=<?= $row['id'] ?>" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
        </td>
        <?php endif; ?>
    </tr>
    <?php endwhile; ?>
</table>


    <!-- Pagination -->
    <div style="margin-top:10px;">
        <?php for ($i = 1; $i <= $totalPage; $i++): ?>
            <a href="?search=<?= $search ?>&page=<?= $i ?>"><?= $i ?></a>
        <?php endfor; ?>
    </div>
</div>
