<?php 
require '../includes/session.php'; 
require '../includes/db.php'; 

// Pencarian siswa
$search = $_GET['search_siswa'] ?? '';
$query = "SELECT * FROM users WHERE role='siswa' AND nama LIKE '%$search%'";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Siswa</title>
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
            background-color: #03A9F4; /* Warna hover lebih terang */
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
            margin-left: 220px;
            padding: 20px;
            background-color: #E0F7FA;
            min-height: 100vh;
            transition: margin-left 0.3s ease;
        }

        .content.full {
            margin-left: 0;
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

        .search-bar input {
            width: 300px;
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

        .action-buttons .riwayat {
            background-color: #32CD32;
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

        @media (max-width: 768px) {
            .content {
                margin-left: 0;
            }

            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.hidden {
                transform: translateX(0);
            }
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
        <li><a href="data_siswa.php">Data Siswa</a></li>
        <li><a href="data_peminjam.php">Data Peminjam</a></li>
        <li><a href="riwayat.php">Riwayat</a></li>
        <li><a href="kelola_pengguna.php">Kelola Pengguna</a></li>
    </ul>
</div>

<div class="content full">
    <h1>Data Siswa</h1>

    <div class="search-container">
        <form method="GET" class="search-bar">
            <input type="text" name="search_siswa" placeholder="Cari siswa..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit">Cari</button>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>Username</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['nama']) ?></td>
                    <td><?= htmlspecialchars($row['username']) ?></td>
                    <td class="action-buttons">
                        <a href="edit_siswa.php?id=<?= $row['id'] ?>" class="edit">Edit</a>
                        <a href="hapus_siswa.php?id=<?= $row['id'] ?>" class="delete" onclick="return confirm('Yakin ingin menghapus siswa ini?')">Hapus</a>
                        <a href="riwayat_siswa.php?id=<?= $row['id'] ?>" class="riwayat">Riwayat</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <a href="tambah_siswa.php" class="add-button">Tambah Siswa</a>
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