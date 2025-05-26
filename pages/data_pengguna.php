<?php 
require '../includes/session.php'; 
require '../includes/db.php'; 

// Pencarian pengguna
$search = $_GET['search_pengguna'] ?? '';
$query = "SELECT id, nama, kode, keterangan FROM users WHERE nama LIKE '%$search%' OR kode LIKE '%$search%' OR keterangan LIKE '%$search%'";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pengguna</title>
    <style>
        /* General Styles */
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #E0F7FA;
        }

        header {
            background-color: #0288D1;
            color: white;
            padding: 30px 20px; /* Tambahkan jarak lebih besar */
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 25px; /* Tambahkan margin bawah */
        }

        /* Sidebar Styles */
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

        /* Menu Toggle Button */
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

        /* Content Styles */
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
            margin-bottom: 30px;
        }

        /* Top Bar Styles */
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 20px;
            gap: 10px;
        }

        .search-bar {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
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

        .add-button {
            background-color: #0288D1;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.2s;
        }

        .add-button:hover {
            background-color: #03A9F4;
        }

        /* Table Styles */
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

        /* Action Buttons */
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
    </style>
</head>
<body>

<!-- Sidebar -->
<?php require '../includes/sidebar.php'; ?>

<div class="content">
    <h1>Data Pengguna</h1>

    <div class="top-bar">
        <button class="menu-toggle" onclick="toggleMenu()">☰ Menu</button>
        <form method="GET" class="search-bar">
            <input type="text" name="search_pengguna" placeholder="Cari pengguna..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit">Cari</button>
        </form>
        <a href="tambah_pengguna.php" class="add-button">Tambah Pengguna</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>Kode</th>
                <th>Keterangan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['nama']) ?></td>
                    <td><?= htmlspecialchars($row['kode']) ?></td>
                    <td><?= htmlspecialchars($row['keterangan']) ?></td>
                    <td class="action-buttons">
                        <a href="edit_pengguna.php?id=<?= $row['id'] ?>" class="edit">Edit</a>
                        <a href="hapus_pengguna.php?id=<?= $row['id'] ?>" class="delete" onclick="return confirm('Yakin ingin menghapus pengguna ini?')">Hapus</a>
                        <a href="riwayat_siswa.php?id=<?= $row['id'] ?>" class="riwayat">Riwayat</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script>
    function toggleMenu() {
        const sidebar = document.querySelector('.sidebar');
        sidebar.classList.toggle('hidden');
    }
</script>
</body>
</html>