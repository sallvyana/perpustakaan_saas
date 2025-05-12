<?php
require '../includes/db.php';

if (isset($_POST['register'])) {
    $nama     = $_POST['nama'];
    $username = $_POST['username'];
    $password = md5($_POST['password']); // untuk sederhana pakai md5
    $role     = 'siswa'; // default siswa

    $cek = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('Username sudah digunakan');</script>";
    } else {
        mysqli_query($conn, "INSERT INTO users (nama, username, password, role) 
                             VALUES ('$nama', '$username', '$password', '$role')");
        echo "<script>alert('Registrasi berhasil, silakan login'); window.location='login.php';</script>";
    }
}
?>

<h2>Form Register</h2>
<form method="POST">
    <input type="text" name="nama" placeholder="Nama lengkap" required><br>
    <input type="text" name="username" placeholder="Username" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <button type="submit" name="register">Daftar</button>
</form>
<p>Sudah punya akun? <a href="login.php">Login di sini</a></p>
