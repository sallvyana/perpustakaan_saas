<?php
require '../includes/session.php';
require '../includes/db.php';

// Hapus data buku (admin only)
if (isset($_GET['hapus']) && $_SESSION['role'] == 'admin') {
    $id = (int) $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM buku WHERE id = $id");
    header("Location: buku.php");
    exit;
}

// Pencarian dan filter
$search = $_GET['search'] ?? '';
$kategori_filter = $_GET['kategori'] ?? '';
$where = "WHERE 1";

if ($search) {
    $s = mysqli_real_escape_string($conn, $search);
    $where .= " AND (judul LIKE '%$s%' OR penulis LIKE '%$s%')";
}
if ($kategori_filter) {
    $k = mysqli_real_escape_string($conn, $kategori_filter);
    $where .= " AND kategori = '$k'";
}

// Pagination
$batas = 10;
$halaman = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
$mulai = ($halaman > 1) ? ($halaman * $batas) - $batas : 0;

$total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM buku $where"))['total'];
$pages = ceil($total / $batas);

$query = mysqli_query($conn, "SELECT * FROM buku $where LIMIT $mulai, $batas");
$no = $mulai + 1;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Buku</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #E0F7FA; /* Latar belakang nuansa pantai */
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 220px;
            height: 100%;
            background-color: #0288D1; /* Warna biru laut */
            color: white;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        .sidebar h3 {
            margin: 0 0 20px;
            font-size: 20px;
            text-align: center;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar ul li {
            margin-bottom: 15px;
        }

        .sidebar ul li a {
            color: white;
            text-decoration: none;
            font-size: 16px;
            display: block;
            padding: 10px;
            border-radius: 5px;
            transition: background-color 0.2s;
        }

        .sidebar ul li a:hover {
            background-color: #03A9F4; /* Warna hover lebih terang */
        }

        .content {
            margin-left: 220px;
            padding: 20px;
            background-color: #E0F7FA;
            min-height: 100vh;
        }

        h1 {
            color: #01579B;
            font-size: 28px;
            text-align: center;
            margin-bottom: 20px;
        }

        .search-container {
            text-align: center;
            margin-bottom: 20px;
        }

        .search-bar {
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .search-bar input, .search-bar select {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .search-bar button {
            background-color: #0288D1;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .search-bar button:hover {
            background-color: #03A9F4;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: white;
        }

        table th {
            background-color: #0288D1; /* Warna biru laut */
            color: white;
            padding: 10px;
        }

        table td {
            padding: 10px;
            text-align: center;
        }

        table tr:nth-child(even) {
            background-color: #E1F5FE; /* Warna biru muda */
        }

        .action-buttons a {
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 5px;
            color: white;
            margin: 0 5px;
        }

        .action-buttons .edit {
            background-color: #00A9FF;
        }

        .action-buttons .delete {
            background-color: #FF6B6B;
        }

        .action-buttons a:hover {
            opacity: 0.8;
        }

        .add-button {
            display: inline-block;
            background-color: #0288D1;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
            margin-top: 20px;
        }

        .add-button:hover {
            background-color: #03A9F4;
        }

        .pagination {
            margin-top: 15px;
            text-align: center;
        }

        .pagination a {
            margin-right: 5px;
            text-decoration: none;
            color: #0288D1;
            padding: 5px 10px;
            border: 1px solid #0288D1;
            border-radius: 5px;
        }

        .pagination a.active {
            font-weight: bold;
            background-color: #0288D1;
            color: white;
        }
    </style>
</head>
<body>
<div class="sidebar">
    <h3>Menu</h3>
    <ul>
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="data_buku.php">Data Buku</a></li>
        <li><a href="data_siswa.php">Data Siswa</a></li>
        <li><a href="data_peminjam.php">Data Peminjam</a></li>
        <li><a href="riwayat.php">Riwayat</a></li>
        <li><a href="kelola_pengguna.php">Kelola Pengguna</a></li>
    </ul>
</div>

<div class="content">
    <h1>Data Buku</h1>

    <?php if ($_SESSION['role'] == 'admin'): ?>
        <a href="/edit & hapus/tambah_buku.php" class="add-button">Tambah Buku</a>
    <?php endif; ?>

    <div class="search-container">
        <form method="GET" class="search-bar">
            <input type="text" name="search" placeholder="Cari judul atau penulis" value="<?= htmlspecialchars($search) ?>">
            <select name="kategori">
                <option value="">Semua Kategori</option>
                <?php
                $kategori_result = mysqli_query($conn, "SELECT DISTINCT kategori FROM buku");
                while ($row = mysqli_fetch_assoc($kategori_result)) {
                    $selected = ($kategori_filter == $row['kategori']) ? 'selected' : '';
                    echo "<option value='{$row['kategori']}' $selected>{$row['kategori']}</option>";
                }
                ?>
            </select>
            <button type="submit">Filter</button>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Judul</th>
                <th>Kategori</th>
                <th>Penulis</th>
                <th>Tahun</th>
                <th>Stok</th>
                <?php if ($_SESSION['role'] == 'admin'): ?><th>Aksi</th><?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($query)): ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= htmlspecialchars($row['judul']); ?></td>
                    <td><?= htmlspecialchars($row['kategori']); ?></td>
                    <td><?= htmlspecialchars($row['penulis']); ?></td>
                    <td><?= htmlspecialchars($row['tahun']); ?></td>
                    <td><?= htmlspecialchars($row['stok']); ?></td>
                    <?php if ($_SESSION['role'] == 'admin'): ?>
                        <td class="action-buttons">
                            <a href="/edit & hapus/edit_buku.php?id=<?= $row['id']; ?>" class="edit">Edit</a>
                            <a href="buku.php?hapus=<?= $row['id']; ?>" class="delete" onclick="return confirm('Hapus buku ini?')">Hapus</a>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div class="pagination">
        <?php for ($i = 1; $i <= $pages; $i++): ?>
            <a href="?halaman=<?= $i ?>&search=<?= urlencode($search) ?>&kategori=<?= urlencode($kategori_filter) ?>" 
                class="<?= ($halaman == $i ? 'active' : '') ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>
    </div>
</div>
</body>
</html>
