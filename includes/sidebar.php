<div class="sidebar">
    <div class="sidebar-header">
        <i class="fas fa-th-large"></i> Menu Utama
    </div>
    <ul class="sidebar-menu">
        <li>
            <a href="dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
        </li>
        <li>
            <a href="barang.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'barang.php' || basename($_SERVER['PHP_SELF']) == 'barang_form.php' ? 'active' : ''; ?>">
                <i class="fas fa-box"></i> Data Barang
            </a>
        </li>
        <li>
            <a href="kategori.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'kategori.php' ? 'active' : ''; ?>">
                <i class="fas fa-tags"></i> Kategori
            </a>
        </li>
        <li>
            <a href="supplier.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'supplier.php' ? 'active' : ''; ?>">
                <i class="fas fa-truck"></i> Supplier
            </a>
        </li>
        <li>
            <a href="transaksi.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'transaksi.php' || basename($_SERVER['PHP_SELF']) == 'transaksi_form.php' ? 'active' : ''; ?>">
                <i class="fas fa-exchange-alt"></i> Transaksi
            </a>
        </li>
        <li>
            <a href="laporan.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'laporan.php' ? 'active' : ''; ?>">
                <i class="fas fa-chart-bar"></i> Laporan
            </a>
        </li>
        <?php if (isAdmin()): ?>
        <li>
            <a href="users.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'users.php' || basename($_SERVER['PHP_SELF']) == 'user_form.php' ? 'active' : ''; ?>">
                <i class="fas fa-users"></i> Pengguna
            </a>
        </li>
        <?php endif; ?>
    </ul>
</div>
