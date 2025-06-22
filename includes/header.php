<?php
// Ensure session is started
require_once 'config/session.php';
requireLogin();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'BuildVent'; ?> - Sistem Inventaris</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <i class="fas fa-boxes"></i> BuildVent
                </div>
                <div class="user-info">
                    <span>
                        <i class="fas fa-user-circle"></i>
                        <?php echo htmlspecialchars($_SESSION['username']); ?>
                        <?php if (isAdmin()): ?>
                            <small>(Admin)</small>
                        <?php else: ?>
                            <small>(Supplier)</small>
                        <?php endif; ?>
                    </span>
                    <a href="logout.php" class="btn btn-sm btn-danger">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </header>
    
    <nav class="nav">
        <div class="container">
            <ul class="nav-list">
                <li class="nav-item">
                    <a href="dashboard.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="barang.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'barang.php' ? 'active' : ''; ?>">
                        <i class="fas fa-box"></i> Barang
                    </a>
                </li>
                <li class="nav-item">
                    <a href="transaksi.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'transaksi.php' ? 'active' : ''; ?>">
                        <i class="fas fa-exchange-alt"></i> Transaksi
                    </a>
                </li>
                <li class="nav-item">
                    <a href="laporan.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'laporan.php' ? 'active' : ''; ?>">
                        <i class="fas fa-chart-bar"></i> Laporan
                    </a>
                </li>
                <?php if (isAdmin()): ?>
                <li class="nav-item">
                    <a href="users.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>">
                        <i class="fas fa-users"></i> Pengguna
                    </a>
                </li>
                <li class="nav-item">
                    <a href="kategori.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'kategori.php' ? 'active' : ''; ?>">
                        <i class="fas fa-tags"></i> Kategori
                    </a>
                </li>
                <li class="nav-item">
                    <a href="supplier.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'supplier.php' ? 'active' : ''; ?>">
                        <i class="fas fa-truck"></i> Data Supplier
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
    
    <div class="container">
