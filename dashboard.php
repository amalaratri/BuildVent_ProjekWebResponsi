<?php
require_once 'config/session.php';
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get statistics
try {
    // Total barang
    $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM barang WHERE status = 'aktif'");
    $total_barang = mysqli_fetch_assoc($result)['total'];
    
    // Total kategori
    $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM kategori WHERE status = 'aktif'");
    $total_kategori = mysqli_fetch_assoc($result)['total'];
    
    // Total supplier
    $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM supplier WHERE status = 'aktif'");
    $total_supplier = mysqli_fetch_assoc($result)['total'];
    
    // Total transaksi hari ini
    $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM transaksi WHERE DATE(tanggal) = CURDATE()");
    $total_transaksi_hari_ini = mysqli_fetch_assoc($result)['total'];
    
    // Barang dengan stok menipis (< 10)
    $result = mysqli_query($conn, "SELECT * FROM barang WHERE stok < 10 AND status = 'aktif' ORDER BY stok ASC LIMIT 5");
    $barang_stok_menipis = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $barang_stok_menipis[] = $row;
    }
    
    // Transaksi terbaru
    $result = mysqli_query($conn, "
        SELECT t.*, b.nama_barang, u.username 
        FROM transaksi t 
        JOIN barang b ON t.barang_id = b.id 
        JOIN users u ON t.user_id = u.id 
        ORDER BY t.tanggal DESC 
        LIMIT 5
    ");
    $transaksi_terbaru = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $transaksi_terbaru[] = $row;
    }
    
} catch (Exception $e) {
    $error = "Error: " . $e->getMessage();
}

$page_title = "Dashboard";
include 'includes/header.php';
?>

<div class="layout">
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <h1 class="page-title">Dashboard</h1>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card primary">
                <div class="stat-number"><?php echo $total_barang; ?></div>
                <div class="stat-label">Total Barang</div>
            </div>
            
            <div class="stat-card success">
                <div class="stat-number"><?php echo $total_kategori; ?></div>
                <div class="stat-label">Total Kategori</div>
            </div>
            
            <div class="stat-card info">
                <div class="stat-number"><?php echo $total_supplier; ?></div>
                <div class="stat-label">Total Supplier</div>
            </div>
            
            <div class="stat-card warning">
                <div class="stat-number"><?php echo $total_transaksi_hari_ini; ?></div>
                <div class="stat-label">Transaksi Hari Ini</div>
            </div>
        </div>
        
        <div class="form-row">
            <!-- Barang Stok Menipis -->
            <div class="card">
                <div class="card-header">
                    <h3>Barang Stok Menipis</h3>
                </div>
                <div class="card-body">
                    <?php if (empty($barang_stok_menipis)): ?>
                        <p class="text-muted">Tidak ada barang dengan stok menipis.</p>
                    <?php else: ?>
                        <div class="table-container">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Nama Barang</th>
                                        <th>Stok</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($barang_stok_menipis as $barang): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($barang['nama_barang']); ?></td>
                                            <td>
                                                <span class="text-danger">
                                                    <?php echo $barang['stok']; ?> <?php echo htmlspecialchars($barang['satuan']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="text-warning">Stok Menipis</span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Transaksi Terbaru -->
            <div class="card">
                <div class="card-header">
                    <h3>Transaksi Terbaru</h3>
                </div>
                <div class="card-body">
                    <?php if (empty($transaksi_terbaru)): ?>
                        <p class="text-muted">Belum ada transaksi.</p>
                    <?php else: ?>
                        <div class="table-container">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Barang</th>
                                        <th>Jenis</th>
                                        <th>Jumlah</th>
                                        <th>User</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($transaksi_terbaru as $transaksi): ?>
                                        <tr>
                                            <td><?php echo date('d/m/Y H:i', strtotime($transaksi['tanggal'])); ?></td>
                                            <td><?php echo htmlspecialchars($transaksi['nama_barang']); ?></td>
                                            <td>
                                                <span class="<?php echo $transaksi['jenis'] == 'masuk' ? 'text-success' : 'text-warning'; ?>">
                                                    <?php echo ucfirst($transaksi['jenis']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo $transaksi['jumlah']; ?></td>
                                            <td><?php echo htmlspecialchars($transaksi['username']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
