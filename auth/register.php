<?php
require '../includes/db.php';

if (isset($_POST['register'])) {
    $nama       = mysqli_real_escape_string($conn, $_POST['nama']);
    $kode       = mysqli_real_escape_string($conn, $_POST['kode']); // NIP/NIS
    $username   = mysqli_real_escape_string($conn, $_POST['username']);
    $password   = $_POST['password']; // Tidak menggunakan hash untuk kesederhanaan
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']); // Status
    $role       = 'siswa'; // Default role adalah siswa

    // Cek apakah username atau kode sudah digunakan
    $cek = mysqli_query($conn, "SELECT * FROM users WHERE username='$username' OR kode='$kode'");
    if (mysqli_num_rows($cek) > 0) {
        $error_message = "Username atau kode (NIP/NIS) sudah digunakan.";
    } else {
        // Insert data ke database
        $query = "INSERT INTO users (nama, kode, username, password, keterangan, role) 
                  VALUES ('$nama', '$kode', '$username', '$password', '$keterangan', '$role')";
        if (mysqli_query($conn, $query)) {
            echo "<script>alert('Registrasi berhasil, silakan login'); window.location='login.php';</script>";
        } else {
            $error_message = "Terjadi kesalahan saat registrasi.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #E0F7FA;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .register-container {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 300px;
            text-align: center;
        }

        .register-container h2 {
            margin-bottom: 20px;
            color: #0288D1;
        }

        .register-container input,
        .register-container select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .register-container button {
            width: 100%;
            padding: 10px;
            background-color: #0288D1;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .register-container button:hover {
            background-color: #03A9F4;
        }

        .register-container p {
            margin: 10px 0;
            font-size: 14px;
        }

        .register-container a {
            color: #0288D1;
            text-decoration: none;
        }

        .register-container a:hover {
            text-decoration: underline;
        }

        .error-message {
            color: red;
            font-size: 14px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h2>Register</h2>
        <?php if (isset($error_message)): ?>
            <div class="error-message"><?= htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        <form method="POST">
            <input type="text" name="nama" placeholder="Nama Lengkap" required>
            <input type="text" name="kode" placeholder="Kode (NIP/NIS)" required>
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <select name="keterangan" required>
                <option value="" disabled selected>Pilih Keterangan (Status)</option>
                <option value="guru">Guru</option>
                <option value="siswa kelas 10">Siswa Kelas 10</option>
                <option value="siswa kelas 11">Siswa Kelas 11</option>
                <option value="siswa kelas 12">Siswa Kelas 12</option>
                <option value="siswa kelas 13">Siswa Kelas 13</option>
            </select>
            <button type="submit" name="register">Daftar</button>
        </form>
        <p>Sudah punya akun? <a href="login.php">Login di sini</a></p>
    </div>
</body>
</html>
