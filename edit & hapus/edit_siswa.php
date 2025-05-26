<?php
require '../includes/session.php';
require '../includes/db.php';

$id = $_GET['id'];
$query = "SELECT * FROM users WHERE id = $id AND role = 'siswa'";
$result = mysqli_query($conn, $query);
$siswa = mysqli_fetch_assoc($result);

if (!$siswa) {
    die("Siswa tidak ditemukan!");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $username = $_POST['username'];

    $query = "UPDATE users SET nama = '$nama', username = '$username' WHERE id = $id";
    mysqli_query($conn, $query);

    header("Location: data_pengguna.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Siswa</title>
</head>
<body>
    <h2>Edit Siswa</h2>
    <form method="POST">
        <label for="nama">Nama:</label><br>
        <input type="text" id="nama" name="nama" value="<?= htmlspecialchars($siswa['nama']) ?>" required><br><br>

        <label for="username">Username:</label><br>
        <input type="text" id="username" name="username" value="<?= htmlspecialchars($siswa['username']) ?>" required><br><br>

        <button type="submit">Simpan</button>
        <a href="data_pengguna.php"><button type="button">Kembali</button></a>
    </form>
</body>
</html>