<?php
require '../includes/session.php';
require '../includes/db.php';
include '../includes/sidebar.php';

// Hapus data (admin only)
if (isset($_GET['hapus']) && $_SESSION['role'] == 'admin') {
    $id = (int) $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM buku WHERE id = $id");
    header("Location: buku.php");
    exit;
}
?>

<div style="margin-left: 220px;">
    <h2>Data Buku</h2>

    <?php if ($_SESSION['role'] == 'admin'): ?>
        <a href="/edit & hapus/tambah_buku.php"><button>Tambah Buku</button></a>
    <?php endif; ?>

    <!-- FILTER DAN SEARCH -->
    <form method="GET" style="margin-top: 20px;">
        <input type="text" name="search" placeholder="Cari judul atau penulis" value="<?= $_GET['search'] ?? '' ?>">
        <select name="kategori">
            <option value="">Semua Kategori</option>
            <?php
            $kategori_result = mysqli_query($conn, "SELECT DISTINCT kategori FROM buku");
            while ($row = mysqli_fetch_assoc($kategori_result)) {
                $selected = ($_GET['kategori'] ?? '') == $row['kategori'] ? 'selected' : '';
                echo "<option value='{$row['kategori']}' $selected>{$row['kategori']}</option>";
            }
            ?>
        </select>
        <button type="submit">Filter</button>
    </form>

    <br>

    <!-- TABEL -->
    <table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <th>No</th><th>Judul</th><th>Kategori</th><th>Penulis</th><th>Tahun</th><th>Stok</th>
            <?php if ($_SESSION['role'] == 'admin'): ?><th>Aksi</th><?php endif; ?>
        </tr>

        <?php
        $batas = 10;
        $halaman = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
        $mulai = ($halaman > 1) ? ($halaman * $batas) - $batas : 0;

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

        $total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM buku $where"))['total'];
        $pages = ceil($total / $batas);

        $query = mysqli_query($conn, "SELECT * FROM buku $where LIMIT $mulai, $batas");
        $no = $mulai + 1;

        while ($row = mysqli_fetch_assoc($query)):
        ?>
        <tr>
            <td><?= $no++; ?></td>
            <td><?= $row['judul']; ?></td>
            <td><?= $row['kategori']; ?></td>
            <td><?= $row['penulis']; ?></td>
            <td><?= $row['tahun']; ?></td>
            <td><?= $row['stok']; ?></td>
            <?php if ($_SESSION['role'] == 'admin'): ?>
                <td>
                    <a href="/edit & hapus/edit_buku.php?id=<?= $row['id']; ?>">Edit</a> |
                    <a href="buku.php?hapus=<?= $row['id']; ?>" onclick="return confirm('Hapus buku ini?')">Hapus</a>
                </td>
            <?php endif; ?>
        </tr>
        <?php endwhile; ?>
    </table>

    <!-- PAGINATION -->
    <div style="margin-top: 15px;">
        <?php for ($i = 1; $i <= $pages; $i++): ?>
            <a href="?halaman=<?= $i ?>&search=<?= urlencode($search) ?>&kategori=<?= urlencode($kategori_filter) ?>" 
                style="margin-right: 5px; <?= ($halaman == $i ? 'font-weight: bold;' : '') ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>
    </div>
</div>
