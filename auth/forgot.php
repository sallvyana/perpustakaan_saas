<?php
require '../includes/db.php';

if (isset($_POST['reset'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $newpass  = $_POST['new_password']; // Tidak menggunakan hash untuk kesederhanaan

    $cek = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");
    if (mysqli_num_rows($cek) > 0) {
        mysqli_query($conn, "UPDATE users SET password='$newpass' WHERE username='$username'");
        echo "<script>alert('Password berhasil diubah'); window.location='login.php';</script>";
    } else {
        $error_message = "Username tidak ditemukan.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
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

        .reset-container {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 300px;
            text-align: center;
        }

        .reset-container h2 {
            margin-bottom: 20px;
            color: #0288D1;
        }

        .reset-container input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .reset-container button {
            width: 100%;
            padding: 10px;
            background-color: #0288D1;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .reset-container button:hover {
            background-color: #03A9F4;
        }

        .error-message {
            color: red;
            font-size: 14px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="reset-container">
        <h2>Reset Password</h2>
        <?php if (isset($error_message)): ?>
            <div class="error-message"><?= htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Masukkan username" required>
            <input type="password" name="new_password" placeholder="Password baru" required>
            <button type="submit" name="reset">Reset Password</button>
        </form>
        <p><a href="login.php">Kembali ke Login</a></p>
    </div>
</body>
</html>