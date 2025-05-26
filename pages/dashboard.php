<?php 
require '../includes/session.php'; 
require '../includes/db.php'; 
require '../includes/sidebar.php';

// Proses pinjam buku
if (isset($_POST['pinjam'])) {
    $id_buku = intval($_POST['id_buku']);
    $id_siswa = ($_SESSION['role'] == 'admin') ? intval($_POST['id_siswa']) : $_SESSION['id'];
    $tanggal_pinjam = date('Y-m-d');

    // Ambil data buku
    $buku_query = mysqli_query($conn, "SELECT * FROM buku WHERE id = $id_buku");
    $buku = mysqli_fetch_assoc($buku_query);

    if ($buku && $buku['stok'] > 0) {
        // Kurangi stok buku
        mysqli_query($conn, "UPDATE buku SET stok = stok - 1 WHERE id = $id_buku");

        // Tambahkan ke data peminjaman
        mysqli_query($conn, "INSERT INTO peminjam (id_buku, id_siswa, tanggal_pinjam) VALUES ($id_buku, $id_siswa, '$tanggal_pinjam')");

        // Tambahkan ke riwayat
        mysqli_query($conn, "INSERT INTO riwayat (nama, buku, tanggal_pinjam) VALUES (
            (SELECT nama FROM users WHERE id = $id_siswa),
            (SELECT judul FROM buku WHERE id = $id_buku),
            '$tanggal_pinjam'
        )");

        header("Location: dashboard.php?msg=success");
        exit;
    } else {
        header("Location: dashboard.php?msg=error");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #E0F7FA; /* Latar belakang nuansa pantai */
        }

        .menu-toggle {
            background-color: #0288D1;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.2s;
            margin-right: 20px;
        }

        .menu-toggle:hover {
            background-color: #03A9F4;
        }

        .top-bar {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }

        .content {
            padding: 20px;
            background-color: #E0F7FA;
            min-height: 100vh;
            text-align: center;
        }

        h1 {
            color: #01579B;
            font-size: 28px;
            margin-bottom: 10px;
            display: inline-block;
        }

        h2 {
            color: #0277BD;
            font-size: 22px;
            margin-bottom: 20px;
        }

        .stats-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-box {
            background-color: #0288D1; /* Warna biru laut */
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            flex: 1 1 calc(33.333% - 20px); /* Responsif: 3 kolom */
            max-width: 300px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .stat-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.2);
        }

        .stat-box h3 {
            margin: 0;
            font-size: 20px;
        }

        .stat-box p {
            margin: 10px 0 0;
            font-size: 26px;
            font-weight: bold;
        }

        .search-container {
            margin-top: 30px;
        }

        .search-bar {
            display: inline-flex;
            align-items: center;
            margin-bottom: 20px;
            gap: 10px;
        }

        .search-bar label {
            font-size: 16px;
            color: #01579B;
        }

        .search-bar input {
            width: 300px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .search-bar button {
            background-color: #0288D1; /* Warna tombol biru */
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .search-bar button:hover {
            background-color: #03A9F4; /* Warna hover lebih terang */
        }

        .pinjam-container {
            margin-top: 30px;
            text-align: center;
        }

        .pinjam-container form {
            display: inline-block;
            text-align: left;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .pinjam-container label {
            display: block;
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .pinjam-container button {
            background-color: #0288D1;
            color: white;
            border: none;
            cursor: pointer;
        }

        .pinjam-container button:hover {
            background-color: #03A9F4;
        }

        /* Gaya untuk tombol Pinjam Buku */
        .btn-pinjam {
            display: inline-block;
            background-color: #0288D1;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .btn-pinjam:hover {
            background-color: #0277BD;
        }
    </style>
</head>
<body>

<div class="top-bar">
    <button class="menu-toggle" onclick="toggleMenu()">☰ Menu</button>
</div>

<div class="content">
    <h1>Selamat Datang di Perpustakaan</h1>
    <p>Temukan buku dan informasi siswa di sini.</p>

    <h2>Statistik Perpustakaan</h2>
    <div class="stats-container">
        <div class="stat-box">
            <h3>Total Buku</h3>
            <p><?= mysqli_num_rows(mysqli_query($conn, "SELECT * FROM buku")); ?></p>
        </div>
        <div class="stat-box">
            <h3>Total Peminjaman</h3>
            <p><?= mysqli_num_rows(mysqli_query($conn, "SELECT * FROM peminjam")); ?></p>
        </div>
        <div class="stat-box">
            <h3>Total Siswa</h3>
            <p><?= mysqli_num_rows(mysqli_query($conn, "SELECT * FROM users WHERE role='siswa'")); ?></p>
        </div>
    </div>

    <div class="search-container">
        <div class="search-bar">
            <label for="search-buku">Cari Buku</label>
            <form method="GET" action="data_buku.php" style="display: inline;">
                <input type="text" id="search-buku" name="search_buku" placeholder="Masukkan judul atau pengarang buku">
                <button type="submit">Cari</button>
            </form>
        </div>
        <div class="search-bar">
            <label for="search-siswa">Cari Siswa</label>
            <form method="GET" action="data_pengguna.php" style="display: inline;">
                <input type="text" id="search-siswa" name="search_siswa" placeholder="Masukkan nama siswa">
                <button type="submit">Cari</button>
            </form>
        </div>
    </div>

    <div class="pinjam-container">
        <h2>Pinjam Buku</h2>
        <a href="pinjam_buku.php" class="btn-pinjam">Pinjam Buku</a>
    </div>
</div>

<script>
    function toggleMenu() {
        alert("Sidebar functionality has been removed.");
    }
</script>

</body>
</html>
