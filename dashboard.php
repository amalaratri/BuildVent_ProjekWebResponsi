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
$user_id = $_SESSION['user_id'];
    $is_admin = isAdmin();
    
    // Total barang
    if ($is_admin) {
        $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM barang WHERE status = 'aktif'");
    } else {
        $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM barang WHERE status = 'aktif' AND user_id = '$user_id'");
    }
    $total_barang = mysqli_fetch_assoc($result)['total'];
    
    // Total kategori (only for admin)
    if ($is_admin) {
        $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM kategori WHERE status = 'aktif'");
        $total_kategori = mysqli_fetch_assoc($result)['total'];
        
        // Total supplier (only for admin)
        $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM supplier WHERE status = 'aktif'");
        $total_supplier = mysqli_fetch_assoc($result)['total'];
    }
    
    // Total transaksi hari ini
    if ($is_admin) {
        $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM transaksi WHERE DATE(tanggal) = CURDATE()");
    } else {
        $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM transaksi WHERE DATE(tanggal) = CURDATE() AND user_id = '$user_id'");
    }
    $total_transaksi_hari_ini = mysqli_fetch_assoc($result)['total'];
    
    // Barang dengan stok menipis (< 10)
    if ($is_admin) {
        $result = mysqli_query($conn, "SELECT * FROM barang WHERE stok < 10 AND status = 'aktif' ORDER BY stok ASC LIMIT 5");
    } else {
        $result = mysqli_query($conn, "SELECT * FROM barang WHERE stok < 10 AND status = 'aktif' AND user_id = '$user_id' ORDER BY stok ASC LIMIT 5");
    }
    $barang_stok_menipis = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $barang_stok_menipis[] = $row;
    }
    
    // Transaksi terbaru
    if ($is_admin) {
        $result = mysqli_query($conn, "
            SELECT t.*, b.nama_barang, u.username 
            FROM transaksi t 
            JOIN barang b ON t.barang_id = b.id 
            JOIN users u ON t.user_id = u.id 
            ORDER BY t.tanggal DESC 
            LIMIT 5
        ");
    } else {
        $result = mysqli_query($conn, "
            SELECT t.*, b.nama_barang, u.username 
            FROM transaksi t 
            JOIN barang b ON t.barang_id = b.id 
            JOIN users u ON t.user_id = u.id 
            WHERE t.user_id = '$user_id'
            ORDER BY t.tanggal DESC 
            LIMIT 5
        ");
    }
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
        <h1 class="page-title">Dashboard <?php echo isAdmin() ? '(Admin)' : '(Supplier)'; ?></h1>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card primary">
                <div class="stat-number"><?php echo $total_barang; ?></div>
                <div class="stat-label"><?php echo isAdmin() ? 'Total Barang' : 'Barang Saya'; ?></div>
            </div>
            
            <?php if (isAdmin()): ?>
            <div class="stat-card success">
                <div class="stat-number"><?php echo $total_kategori; ?></div>
                <div class="stat-label">Total Kategori</div>
            </div>
            
            <div class="stat-card info">
                <div class="stat-number"><?php echo $total_supplier; ?></div>
                <div class="stat-label">Total Supplier</div>
            </div>
            <?php endif; ?>
            
            <div class="stat-card warning">
                <div class="stat-number"><?php echo $total_transaksi_hari_ini; ?></div>
                <div class="stat-label"><?php echo isAdmin() ? 'Transaksi Hari Ini' : 'Transaksi Saya Hari Ini'; ?></div>
            </div>
        </div>
        
        <div class="form-row">
            <!-- Barang Stok Menipis -->
            <div class="card">
                <div class="card-header">
                    <h3><?php echo isAdmin() ? 'Barang Stok Menipis' : 'Barang Saya - Stok Menipis'; ?></h3>
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
                    <h3><?php echo isAdmin() ? 'Transaksi Terbaru' : 'Transaksi Saya Terbaru'; ?></h3>
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
                                        <?php if (isAdmin()): ?>
                                        <th>User</th>
                                        <?php endif; ?>
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
                                            <?php if (isAdmin()): ?>
                                            <td><?php echo htmlspecialchars($transaksi['username']); ?></td>
                                            <?php endif; ?>
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
