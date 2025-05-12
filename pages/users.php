<?php
require '../includes/session.php';
require '../includes/db.php';
if ($_SESSION['role'] != 'admin') {
    die("Hanya admin yang dapat mengakses halaman ini.");
}
?>

<?php include '../includes/sidebar.php'; ?>

<div style="margin-left: 220px;">
    <h2>Daftar Pengguna</h2>

    <table border="1" cellpadding="8">
        <tr>
            <th>No</th><th>Nama</th><th>Username</th><th>Role</th>
        </tr>
        <?php
        $no = 1;
        $users = mysqli_query($conn, "SELECT * FROM users");
        while ($row = mysqli_fetch_assoc($users)):
        ?>
        <tr>
            <td><?= $no++; ?></td>
            <td><?= $row['nama']; ?></td>
            <td><?= $row['username']; ?></td>
            <td><?= $row['role']; ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>
