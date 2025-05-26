<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$role = isset($_SESSION['role']) ? $_SESSION['role'] : null;
?>

<div class="sidebar hidden">
    <div class="profile-section">
        <!-- Tombol Close Menu -->
        <button class="close-menu" onclick="toggleMenu()">×</button>
        <img src="path/to/profile-picture.jpg" alt="Profile Picture">
        <h3><?= isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Guest'; ?></h3>
        <p><?= isset($_SESSION['phone']) ? htmlspecialchars($_SESSION['phone']) : 'No Phone'; ?></p>
    </div>
    <ul>
        <li><a href="dashboard.php" class="active"><i class="icon-home"></i> Home</a></li>
        <li><a href="users.php"><i class="icon-user"></i> My Profile</a></li>
        <li><a href="data_buku.php"><i class="icon-book"></i> Data Buku</a></li>
        <?php if ($role === 'admin'): ?>
            <li><a href="data_pengguna.php"><i class="icon-users"></i> Data Pengguna</a></li>
        <?php endif; ?>
        <li><a href="data_peminjam.php"><i class="icon-users"></i> Data Peminjam</a></li>
        <li><a href="riwayat.php"><i class="icon-history"></i> Riwayat</a></li>
        <li><a href="../auth/logout.php"><i class="icon-logout"></i> Logout</a></li>
    </ul>
</div>

<script>
    function toggleMenu() {
        const sidebar = document.querySelector('.sidebar');
        const content = document.querySelector('.content');
        sidebar.classList.toggle('hidden');

        // Pastikan konten bergeser sesuai dengan status sidebar
        if (sidebar.classList.contains('hidden')) {
            content.classList.remove('shifted');
        } else {
            content.classList.add('shifted');
        }
    }
</script>

<style>
/* Sidebar Styles */
.sidebar {
    position: fixed;
    top: 0;
    left: 0;
    width: 240px;
    height: 100%;
    background-color: #0288D1;
    color: white;
    padding: 20px;
    box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
    z-index: 1000;
}

.sidebar.hidden {
    transform: translateX(-100%);
}

.sidebar .profile-section {
    text-align: center;
    margin-bottom: 20px;
}

.sidebar .profile-section img {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    margin-bottom: 10px;
}

.sidebar .profile-section h3 {
    font-size: 18px;
    color: white;
    margin: 0;
}

.sidebar .profile-section p {
    font-size: 14px;
    color: #B3E5FC;
    margin: 5px 0 0;
}

.sidebar ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.sidebar ul li {
    margin: 15px 0;
}

.sidebar ul li a {
    color: white;
    text-decoration: none;
    font-size: 16px;
    display: block;
    padding: 10px;
    border-radius: 5px;
    transition: background-color 0.3s;
}

.sidebar ul li a:hover {
    background-color: #03A9F4;
}

.sidebar ul li a.active {
    background-color: #01579B;
}

/* Tombol Close Menu */
.close-menu {
    background: none;
    border: none;
    color: white;
    font-size: 24px;
    font-weight: bold;
    position: absolute;
    top: 10px;
    right: 10px;
    cursor: pointer;
    transition: color 0.3s;
}

.close-menu:hover {
    color: #FF5252;
}
</style>