<?php
$host = "localhost";
$user = "root"; // ubah jika user MySQL kamu beda
$pass = "54117";     // isi sesuai password MySQL kamu
$db   = "perpus_saas";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
