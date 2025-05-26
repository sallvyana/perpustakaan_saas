<?php
require '../includes/session.php';
require '../includes/db.php';

// Proses pinjam buku
if (isset($_POST['pinjam'])) {
    $id_buku = intval($_POST['id_buku']);
    $id_user = $_SESSION['id']; // Menggunakan id_users dari sesi login
    $tanggal_pinjam = date('Y-m-d');

    // Ambil data buku
    $buku_query = mysqli_query($conn, "SELECT * FROM buku WHERE id = $id_buku");
    $buku = mysqli_fetch_assoc($buku_query);

    if ($buku && $buku['stok'] > 0) {
        // Kurangi stok buku
        mysqli_query($conn, "UPDATE buku SET stok = stok - 1 WHERE id = $id_buku");

        // Tambahkan ke data peminjaman
        mysqli_query($conn, "INSERT INTO peminjam (id_buku, id_user, tanggal_pinjam) VALUES ($id_buku, $id_user, '$tanggal_pinjam')");

        // Tambahkan ke riwayat
        mysqli_query($conn, "INSERT INTO riwayat (id_user, id_buku, tanggal_pinjam) VALUES ($id_user, $id_buku, '$tanggal_pinjam')");

        header("Location: pinjam_buku.php?msg=success");
        exit;
    } else {
        header("Location: pinjam_buku.php?msg=error");
        exit;
    }
}

// Pencarian buku
$search = $_GET['search'] ?? '';
$kategori = $_GET['kategori'] ?? '';
$where = "stok > 0"; // Hanya buku dengan stok lebih dari 0

if (!empty($search)) {
    $s = mysqli_real_escape_string($conn, $search);
    $where .= " AND (judul LIKE '%$s%' OR penulis LIKE '%$s%')";
}

if (!empty($kategori)) {
    $k = mysqli_real_escape_string($conn, $kategori);
    $where .= " AND kategori = '$k'";
}

$buku_query = mysqli_query($conn, "SELECT * FROM buku WHERE $where");

// Ambil daftar kategori untuk dropdown
$kategori_query = mysqli_query($conn, "SELECT DISTINCT kategori FROM buku");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pinjam Buku</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #E0F7FA;
        }

        header {
            background-color: #0288D1;
            color: white;
            padding: 15px 20px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            position: relative;
        }

        .menu {
            position: absolute;
            top: 15px;
            left: 20px;
        }

        .menu a {
            color: white;
            text-decoration: none;
            font-size: 16px;
            padding: 5px 10px;
            border: 1px solid white;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .menu a:hover {
            background-color: white;
            color: #0288D1;
        }

        .search-bar {
            margin: 20px auto;
            text-align: center;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }

        .search-bar input, .search-bar select {
            padding: 10px;
            width: 300px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .search-bar button {
            padding: 10px 15px;
            background-color: #0288D1;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .search-bar button:hover {
            background-color: #03A9F4;
        }

        .result-container {
            margin-top: 20px;
            text-align: center;
        }

        .result-container table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .result-container th, .result-container td {
            padding: 15px;
            border: 1px solid #ddd;
            text-align: center;
        }

        .result-container th {
            background-color: #0288D1;
            color: white;
            font-weight: bold;
        }

        .result-container tr:nth-child(even) {
            background-color: #E3F2FD;
        }

        .result-container tr:nth-child(odd) {
            background-color: #FFFFFF;
        }

        .result-container tr:hover {
            background-color: #BBDEFB;
        }

        .result-container button {
            padding: 8px 12px;
            background-color: #43A047;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .result-container button:hover {
            background-color: #388E3C;
        }

        .message {
            margin: 20px auto;
            text-align: center;
            font-size: 16px;
            font-weight: bold;
        }
    </style>
</head>
<body>
<header>
    <div class="menu">
        <a href="dashboard.php">Home</a>
    </div>
    Pinjam Buku
</header>

<div class="search-bar">
    <?php if (isset($_GET['msg'])): ?>
        <div class="message">
            <?php if ($_GET['msg'] == 'success'): ?>
                <p style="color: green;">Peminjaman berhasil!</p>
            <?php elseif ($_GET['msg'] == 'error'): ?>
                <p style="color: red;">Peminjaman gagal! Stok buku tidak mencukupi.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <form method="GET" style="display: flex; align-items: center; gap: 10px;">
        <input type="text" name="search" placeholder="Cari judul atau penulis" value="<?= htmlspecialchars($search) ?>">
        <select name="kategori">
            <option value="">-- Semua Kategori --</option>
            <?php while ($kategori_row = mysqli_fetch_assoc($kategori_query)): ?>
                <option value="<?= htmlspecialchars($kategori_row['kategori']); ?>" <?= ($kategori == $kategori_row['kategori']) ? 'selected' : ''; ?>>
                    <?= htmlspecialchars($kategori_row['kategori']); ?>
                </option>
            <?php endwhile; ?>
        </select>
        <button type="submit">Cari</button>
    </form>
</div>

<div class="result-container">
    <?php if (mysqli_num_rows($buku_query) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Judul</th>
                    <th>Penulis</th>
                    <th>Kategori</th>
                    <th>Stok</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($buku = mysqli_fetch_assoc($buku_query)): ?>
                    <tr>
                        <td><?= htmlspecialchars($buku['judul']); ?></td>
                        <td><?= htmlspecialchars($buku['penulis']); ?></td>
                        <td><?= htmlspecialchars($buku['kategori']); ?></td>
                        <td><?= $buku['stok']; ?></td>
                        <td>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="id_buku" value="<?= $buku['id']; ?>">
                                <button type="submit" name="pinjam">Pinjam</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Tidak ada buku yang ditemukan.</p>
    <?php endif; ?>
</div>
</body>
</html>