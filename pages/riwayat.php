<?php
require '../includes/session.php';
require '../includes/db.php';

// Hapus data riwayat
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    mysqli_query($conn, "DELETE FROM riwayat WHERE id = $id");
    header("Location: riwayat.php?msg=hapus");
    exit;
}

// Pencarian dan pagination
$search = $_GET['search'] ?? '';
$page = $_GET['page'] ?? 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Hitung total data
$totalQuery = mysqli_query($conn, "
    SELECT COUNT(*) as total 
    FROM riwayat 
    JOIN users ON riwayat.id_user = users.id 
    JOIN buku ON riwayat.id_buku = buku.id 
    WHERE users.nama LIKE '%$search%' OR buku.judul LIKE '%$search%'
");
$totalData = mysqli_fetch_assoc($totalQuery)['total'];
$totalPage = ceil($totalData / $limit);

// Ambil data riwayat
$query = mysqli_query($conn, "
    SELECT riwayat.*, users.nama AS nama, buku.judul AS buku 
    FROM riwayat 
    JOIN users ON riwayat.id_user = users.id 
    JOIN buku ON riwayat.id_buku = buku.id 
    WHERE users.nama LIKE '%$search%' OR buku.judul LIKE '%$search%'
    ORDER BY riwayat.id DESC 
    LIMIT $limit OFFSET $offset
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Peminjaman</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #E0F7FA;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 220px;
            height: 100%;
            background-color: #0288D1;
            color: white;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .sidebar.hidden {
            transform: translateX(-100%);
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
            background-color: #03A9F4;
        }

        .menu-toggle {
            position: fixed;
            top: 20px;
            left: 20px;
            background-color: #0288D1;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            z-index: 1000;
        }

        .menu-toggle:hover {
            background-color: #03A9F4;
        }

        .content {
            margin-left: 240px;
            padding: 20px;
            transition: margin-left 0.3s ease;
        }

        .content.full {
            margin-left: 20px;
        }

        h1 {
            color: #01579B;
            font-size: 28px;
            text-align: center;
            margin-bottom: 20px;
        }

        .search-bar {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .search-bar input {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 300px;
        }

        .search-bar button {
            background-color: #0288D1;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: white;
        }

        table th {
            background-color: #0288D1;
            color: white;
            padding: 10px;
        }

        table td {
            padding: 10px;
            text-align: center;
        }

        table tr:nth-child(even) {
            background-color: #E1F5FE;
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
<button class="menu-toggle" onclick="toggleMenu()">☰</button>

<div class="sidebar hidden">
    <h3>Menu</h3>
    <ul>
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="data_buku.php">Data Buku</a></li>
        <li><a href="data_pengguna.php">Data Pengguna</a></li>
        <li><a href="data_peminjam.php">Data Peminjam</a></li>
        <li><a href="riwayat.php">Riwayat</a></li>
    </ul>
</div>

<div class="content full">
    <h1>Riwayat Peminjaman Buku</h1>

    <form method="GET" class="search-bar">
        <input type="text" name="search" placeholder="Cari nama atau buku..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Cari</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Buku</th>
                <th>Tgl Pinjam</th>
                <th>Tgl Kembali</th>
                <?php if ($_SESSION['role'] == 'admin'): ?><th>Aksi</th><?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php $no = $offset + 1; ?>
            <?php while ($row = mysqli_fetch_assoc($query)): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($row['nama']) ?></td>
                    <td><?= htmlspecialchars($row['buku']) ?></td>
                    <td><?= htmlspecialchars($row['tanggal_pinjam']) ?></td>
                    <td><?= htmlspecialchars($row['tanggal_kembali'] ?: '-') ?></td>
                    <?php if ($_SESSION['role'] == 'admin'): ?>
                        <td>
                            <a href="riwayat_edit.php?id=<?= $row['id'] ?>&page=<?= $page ?>&search=<?= $search ?>">Edit</a> |
                            <a href="riwayat.php?hapus=<?= $row['id'] ?>" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div class="pagination">
        <?php for ($i = 1; $i <= $totalPage; $i++): ?>
            <a href="?search=<?= $search ?>&page=<?= $i ?>" class="<?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
    </div>
</div>

<script>
    function toggleMenu() {
        const sidebar = document.querySelector('.sidebar');
        const content = document.querySelector('.content');
        sidebar.classList.toggle('hidden');
        content.classList.toggle('full');
    }
</script>
</body>
</html>
