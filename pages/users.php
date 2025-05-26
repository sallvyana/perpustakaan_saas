<?php
require '../includes/session.php';
require '../includes/db.php';

// Pastikan pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: ../auth/login.php");
    exit;
}

// Ambil riwayat aktivitas pengguna dari database
$user_id = $_SESSION['user_id']; // Pastikan user_id disimpan di sesi saat login
$query = "SELECT * FROM riwayat WHERE user_id = '$user_id' ORDER BY tanggal DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Pengguna</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #E0F7FA;
            margin: 0;
            padding: 20px;
        }

        .profile-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .profile-container h2 {
            text-align: center;
            color: #01579B;
        }

        .profile-container p {
            font-size: 16px;
            margin: 10px 0;
        }

        .profile-container a {
            display: block;
            text-align: center;
            margin: 10px 0;
            padding: 10px;
            background-color: #0288D1;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .profile-container a:hover {
            background-color: #03A9F4;
        }

        .riwayat-container {
            margin-top: 20px;
        }

        .riwayat-container h3 {
            color: #01579B;
            margin-bottom: 10px;
        }

        .riwayat-container table {
            width: 100%;
            border-collapse: collapse;
        }

        .riwayat-container table th,
        .riwayat-container table td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }

        .riwayat-container table th {
            background-color: #0288D1;
            color: white;
        }

        .riwayat-container table tr:nth-child(even) {
            background-color: #E1F5FE;
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <h2>Profil Pengguna</h2>
        <p><strong>Username:</strong> <?= htmlspecialchars($_SESSION['username']); ?></p>
        <p><strong>Nama:</strong> <?= htmlspecialchars($_SESSION['nama']); ?></p>
        <p><strong>Kode:</strong> <?= htmlspecialchars($_SESSION['kode']); ?></p>
        <p><strong>Keterangan:</strong> <?= htmlspecialchars($_SESSION['keterangan']); ?></p>
        <a href="../auth/logout.php">Logout</a>
    </div>

    <div class="riwayat-container">
        <h3>Riwayat Aktivitas</h3>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Aktivitas</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php $no = 1; ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= htmlspecialchars($row['tanggal']); ?></td>
                            <td><?= htmlspecialchars($row['aktivitas']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" style="text-align: center;">Tidak ada riwayat aktivitas.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
