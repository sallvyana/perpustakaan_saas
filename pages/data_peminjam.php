<?php
require '../includes/session.php';
require '../includes/db.php';

// Hapus data peminjaman
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    mysqli_query($conn, "DELETE FROM peminjam WHERE id=$id");
    header("Location: data_peminjam.php");
    exit;
}

// Kembalikan buku
if (isset($_GET['kembalikan'])) {
    $id = intval($_GET['kembalikan']);
    $data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM peminjam WHERE id=$id"));
    if ($data) {
        $tgl_kembali = date('Y-m-d');

        mysqli_query($conn, "INSERT INTO riwayat (id_user, id_buku, tanggal_pinjam, tanggal_kembali) 
            VALUES (
                {$data['id_user']}, 
                {$data['id_buku']}, 
                '{$data['tanggal_pinjam']}', 
                '$tgl_kembali'
            )");

        mysqli_query($conn, "UPDATE buku SET stok = stok + 1 WHERE id = {$data['id_buku']}");

        mysqli_query($conn, "DELETE FROM peminjam WHERE id=$id");

        header("Location: data_peminjam.php");
        exit;
    }
}

// Pencarian dan filter
$search = $_GET['search'] ?? '';
$where = !empty($search) ? "WHERE users.nama LIKE '%$search%' OR buku.judul LIKE '%$search%'" : '';

// Pagination
$batas = 10;
$halaman = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
$mulai = ($halaman - 1) * $batas;

$total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM peminjam 
    JOIN users ON peminjam.id_user = users.id 
    JOIN buku ON peminjam.id_buku = buku.id 
    $where"))['total'];
$pages = ceil($total / $batas);

$query = mysqli_query($conn, "
    SELECT peminjam.*, users.nama AS nama, users.keterangan AS keterangan, buku.judul AS buku 
    FROM peminjam 
    JOIN users ON peminjam.id_user = users.id 
    JOIN buku ON peminjam.id_buku = buku.id 
    $where 
    ORDER BY peminjam.id DESC 
    LIMIT $mulai, $batas
");
$no = $mulai + 1;

// Data untuk diagram
$diagram_data = mysqli_query($conn, "
    SELECT buku.judul AS buku, COUNT(*) AS jumlah 
    FROM peminjam 
    JOIN buku ON peminjam.id_buku = buku.id 
    GROUP BY peminjam.id_buku
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Peminjaman</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            z-index: 1000;
            transition: transform 0.3s ease;
        }

        .sidebar.hidden {
            transform: translateX(-100%);
        }

        .menu-toggle {
            background-color: #0288D1;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .menu-toggle:hover {
            background-color: #03A9F4;
        }

        .content {
            margin-left: 0;
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

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            gap: 10px;
        }

        .search-bar {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .search-bar input {
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
        }

        .add-button {
            background-color: #0288D1;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
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

        .chart-container {
            margin-top: 40px;
            text-align: center;
        }

        canvas {
            max-width: 600px;
            margin: 0 auto;
        }

        .toggle-view {
            text-align: center;
            margin-top: 20px;
        }

        .toggle-view button {
            background-color: #0288D1;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            margin: 5px;
        }

        .toggle-view button.active {
            background-color: #01579B;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<?php require '../includes/sidebar.php'; ?>

<div class="content">
    <h1>Data Peminjaman</h1>

    <div class="top-bar">
        <button class="menu-toggle" onclick="toggleMenu()">☰ Menu</button>
        <form method="GET" class="search-bar">
            <input type="text" name="search" placeholder="Cari nama atau buku" value="<?= htmlspecialchars($search) ?>">
            <button type="submit">Cari</button>
        </form>
        <a href="pinjam_buku.php" class="add-button">Pinjam Buku</a>
    </div>

    <div id="tableContainer">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Keterangan</th>
                    <th>Buku</th>
                    <th>Tgl Pinjam</th>
                    <th>Tgl Kembali</th>
                    <?php if ($_SESSION['role'] == 'admin'): ?><th>Aksi</th><?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($query)): ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= htmlspecialchars($row['nama']); ?></td>
                        <td><?= htmlspecialchars($row['keterangan'] ?? 'Tidak Ada Keterangan'); ?></td>
                        <td><?= htmlspecialchars($row['buku']); ?></td>
                        <td><?= htmlspecialchars($row['tanggal_pinjam']); ?></td>
                        <td><?= htmlspecialchars($row['tanggal_kembali'] ?? '-'); ?></td>
                        <?php if ($_SESSION['role'] == 'admin'): ?>
                            <td>
                                <a href="/edit & hapus/edit_peminjam.php?id=<?= $row['id']; ?>" class="btn btn-edit">Edit</a>
                                <a href="data_peminjam.php?kembalikan=<?= $row['id']; ?>" class="btn btn-kembalikan" onclick="return confirm('Sudah dikembalikan?')">Kembalikan</a>
                                <a href="data_peminjam.php?hapus=<?= $row['id']; ?>" class="btn btn-hapus" onclick="return confirm('Hapus data ini?')">Hapus</a>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    function toggleMenu() {
        const sidebar = document.querySelector('.sidebar');
        sidebar.classList.toggle('hidden');
    }
</script>
</body>
</html>
