<?php
require '../includes/session.php';
require '../includes/db.php';

$id = $_GET['id'];
$query = "DELETE FROM users WHERE id = $id AND role = 'siswa'";
mysqli_query($conn, $query);

header("Location: data_pengguna.php");
exit;
?>