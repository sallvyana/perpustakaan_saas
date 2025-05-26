<?php
require '../includes/session.php';
require '../includes/db.php';

// Hapus data buku (admin only)
if (isset($_GET['hapus']) && $_SESSION['role'] == 'admin') {
    $id = (int) $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM buku WHERE id = $id");
    header("Location: data_buku.php");
    exit;
}

// Proses pinjam buku
if (isset($_POST['pinjam']) && ($_SESSION['role'] == 'siswa' || $_SESSION['role'] == 'guru')) {
    $id_buku = intval($_POST['id_buku']);
    $id_siswa = $_SESSION['id'];
    $tanggal_pinjam = date('Y-m-d');

    // Ambil data buku
    $buku_query = mysqli_query($conn, "SELECT * FROM buku WHERE id = $id_buku");
    $buku = mysqli_fetch_assoc($buku_query);

    if ($buku && $buku['stok'] > 0) {
        // Kurangi stok buku
        mysqli_query($conn, "UPDATE buku SET stok = stok - 1 WHERE id = $id_buku");

        // Tambahkan ke data peminjaman
        mysqli_query($conn, "INSERT INTO peminjam (id_buku, id_siswa, tanggal_pinjam) VALUES ($id_buku, $id_siswa, '$tanggal_pinjam')");

        header("Location: data_buku.php?msg=success");
        exit;
    } else {
        header("Location: data_buku.php?msg=error");
        exit;
    }
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
        /* General Styles */
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #E0F7FA;
        }

        /* Top Bar Styles */
        .top-bar {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 20px;
            gap: 10px;
        }

        .top-bar h1 {
            font-size: 28px;
            color: #01579B;
            margin: 0;
            text-align: center;
        }

        .actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            max-width: 1200px;
            gap: 10px;
        }

        .menu-toggle {
            background-color: #0288D1;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .menu-toggle:hover {
            background-color: #03A9F4;
        }

        .search-bar {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-grow: 1;
            justify-content: center;
        }

        .search-bar input,
        .search-bar select {
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            margin-left: 5px;
            margin-right: 5px; 
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

<!-- Top Bar -->
<div class="top-bar">
    <h1>Data Buku</h1>
    <div class="actions">
        <button class="menu-toggle" onclick="toggleMenu()">☰ Menu</button>
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
        <?php if ($_SESSION['role'] == 'admin'): ?>
            <a href="/edit & hapus/tambah_buku.php" class="add-button">Tambah Buku</a>
        <?php endif; ?>
    </div>
</div>

<!-- Sidebar -->
<?php require '../includes/sidebar.php'; ?>

<div class="content">
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
                <?php if ($_SESSION['role'] == 'siswa' || $_SESSION['role'] == 'guru'): ?><th>Aksi</th><?php endif; ?>
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
                            <a href="data_buku.php?hapus=<?= $row['id']; ?>" class="delete" onclick="return confirm('Hapus buku ini?')">Hapus</a>
                        </td>
                    <?php endif; ?>
                    <?php if ($_SESSION['role'] == 'siswa' || $_SESSION['role'] == 'guru'): ?>
                        <td>
                            <form method="POST">
                                <input type="hidden" name="id_buku" value="<?= $row['id']; ?>">
                                <button type="submit" name="pinjam">Pinjam</button>
                            </form>
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

<script>
    function toggleMenu() {
        const sidebar = document.querySelector('.sidebar');
        sidebar.classList.toggle('hidden');
    }
</script>

</body>
</html>