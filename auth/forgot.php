<?php
require '../includes/db.php';

if (isset($_POST['reset'])) {
    $username = $_POST['username'];
    $newpass  = md5($_POST['new_password']);

    $cek = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");
    if (mysqli_num_rows($cek) > 0) {
        mysqli_query($conn, "UPDATE users SET password='$newpass' WHERE username='$username'");
        echo "<script>alert('Password berhasil diubah'); window.location='login.php';</script>";
    } else {
        echo "<script>alert('Username tidak ditemukan');</script>";
    }
}
?>

<h2>Reset Password</h2>
<form method="POST">
    <input type="text" name="username" placeholder="Masukkan username" required><br>
    <input type="password" name="new_password" placeholder="Password baru" required><br>
    <button type="submit" name="reset">Reset Password</button>
</form>