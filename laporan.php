<?php
require_once 'config/session.php';
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Handle export
if (isset($_GET['export']) && $_GET['export'] == 'csv') {
    $jenis = isset($_GET['jenis']) ? mysqli_real_escape_string($conn, $_GET['jenis']) : '';
    $tanggal_dari = isset($_GET['tanggal_dari']) ? mysqli_real_escape_string($conn, $_GET['tanggal_dari']) : '';
    $tanggal_sampai = isset($_GET['tanggal_sampai']) ? mysqli_real_escape_string($conn, $_GET['tanggal_sampai']) : '';
    
    // Build query for export
    $where_conditions = [];
    
    if (!empty($jenis)) {
        $where_conditions[] = "t.jenis = '$jenis'";
    }
    
    if (!empty($tanggal_dari)) {
        $where_conditions[] = "DATE(t.tanggal) >= '$tanggal_dari'";
    }
    
    if (!empty($tanggal_sampai)) {
        $where_conditions[] = "DATE(t.tanggal) <= '$tanggal_sampai'";
    }
    
    $where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";
    
    $query = "
        SELECT t.tanggal, t.kode_transaksi, b.nama_barang, t.jenis, t.jumlah, 
               b.satuan, s.nama_supplier, t.keterangan, u.username
        FROM transaksi t 
        JOIN barang b ON t.barang_id = b.id 
        JOIN users u ON t.user_id = u.id 
        LEFT JOIN supplier s ON t.supplier_id = s.id
        $where_clause
        ORDER BY t.tanggal DESC
    ";
    
    $result = mysqli_query($conn, $query);
    
    if ($result) {
        // Set headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="laporan_transaksi_' . date('Y-m-d') . '.csv"');
        
        // Create CSV content
        $output = fopen('php://output', 'w');
        
        // CSV headers
        fputcsv($output, [
            'Tanggal', 'Kode Transaksi', 'Nama Barang', 'Jenis', 'Jumlah', 
            'Satuan', 'Supplier', 'Keterangan', 'User'
        ]);
        
        // CSV data
        while ($row = mysqli_fetch_assoc($result)) {
            fputcsv($output, [
                date('d/m/Y H:i', strtotime($row['tanggal'])),
                $row['kode_transaksi'],
                $row['nama_barang'],
                ucfirst($row['jenis']),
                $row['jumlah'],
                $row['satuan'],
                $row['nama_supplier'] ?: '-',
                $row['keterangan'],
                $row['username']
            ]);
        }
        
        fclose($output);
        exit();
    } else {
        $_SESSION['error'] = "Error: " . mysqli_error($conn);
        header('Location: laporan.php');
        exit();
    }
}

// Get filter parameters
$jenis_filter = isset($_GET['jenis']) ? mysqli_real_escape_string($conn, $_GET['jenis']) : '';
$tanggal_dari = isset($_GET['tanggal_dari']) ? mysqli_real_escape_string($conn, $_GET['tanggal_dari']) : '';
$tanggal_sampai = isset($_GET['tanggal_sampai']) ? mysqli_real_escape_string($conn, $_GET['tanggal_sampai']) : '';

// Build query for display
$where_conditions = [];

if (!empty($jenis_filter)) {
    $where_conditions[] = "t.jenis = '$jenis_filter'";
}

if (!empty($tanggal_dari)) {
    $where_conditions[] = "DATE(t.tanggal) >= '$tanggal_dari'";
}

if (!empty($tanggal_sampai)) {
    $where_conditions[] = "DATE(t.tanggal) <= '$tanggal_sampai'";
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

// Get transaction data
$query = "
    SELECT t.*, b.nama_barang, b.satuan, u.username, s.nama_supplier
    FROM transaksi t 
    JOIN barang b ON t.barang_id = b.id 
    JOIN users u ON t.user_id = u.id 
    LEFT JOIN supplier s ON t.supplier_id = s.id
    $where_clause
    ORDER BY t.tanggal DESC
";

$result = mysqli_query($conn, $query);
$transaksi = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $transaksi[] = $row;
    }
}

// Get summary statistics
$summary_query = "
    SELECT 
        COUNT(*) as total_transaksi,
        SUM(CASE WHEN jenis = 'masuk' THEN jumlah ELSE 0 END) as total_masuk,
        SUM(CASE WHEN jenis = 'keluar' THEN jumlah ELSE 0 END) as total_keluar
    FROM transaksi t
    $where_clause
";

$summary_result = mysqli_query($conn, $summary_query);
$summary = mysqli_fetch_assoc($summary_result);

$page_title = "Laporan Transaksi";
include 'includes/header.php';
?>

<div class="layout">
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <h1 class="page-title">Laporan Transaksi</h1>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <!-- Filter Form -->
        <div class="card mb-3">
            <div class="card-header">
                <h3>Filter Laporan</h3>
            </div>
            <div class="card-body">
                <form method="GET" action="">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Jenis Transaksi</label>
                            <select name="jenis" class="form-control">
                                <option value="">Semua Jenis</option>
                                <option value="masuk" <?php echo $jenis_filter == 'masuk' ? 'selected' : ''; ?>>Barang Masuk</option>
                                <option value="keluar" <?php echo $jenis_filter == 'keluar' ? 'selected' : ''; ?>>Barang Keluar</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Tanggal Dari</label>
                            <input type="date" name="tanggal_dari" class="form-control" 
                                   value="<?php echo htmlspecialchars($tanggal_dari); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Tanggal Sampai</label>
                            <input type="date" name="tanggal_sampai" class="form-control" 
                                   value="<?php echo htmlspecialchars($tanggal_sampai); ?>">
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="laporan.php" class="btn btn-secondary">Reset</a>
                    
                    <?php if (!empty($transaksi)): ?>
                        <a href="?export=csv&jenis=<?php echo urlencode($jenis_filter); ?>&tanggal_dari=<?php echo urlencode($tanggal_dari); ?>&tanggal_sampai=<?php echo urlencode($tanggal_sampai); ?>" 
                           class="btn btn-success">Export CSV</a>
                        <button type="button" onclick="window.print()" class="btn btn-info no-print">Print</button>
                    <?php endif; ?>
                </form>
            </div>
        </div>
        
        <!-- Summary Statistics -->
        <?php if (isset($summary) && $summary['total_transaksi'] > 0): ?>
            <div class="stats-grid mb-3">
                <div class="stat-card primary">
                    <div class="stat-number"><?php echo $summary['total_transaksi']; ?></div>
                    <div class="stat-label">Total Transaksi</div>
                </div>
                
                <div class="stat-card success">
                    <div class="stat-number"><?php echo $summary['total_masuk']; ?></div>
                    <div class="stat-label">Total Barang Masuk</div>
                </div>
                
                <div class="stat-card warning">
                    <div class="stat-number"><?php echo $summary['total_keluar']; ?></div>
                    <div class="stat-label">Total Barang Keluar</div>
                </div>
                
                <div class="stat-card info">
                    <div class="stat-number"><?php echo $summary['total_masuk'] - $summary['total_keluar']; ?></div>
                    <div class="stat-label">Selisih</div>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Transaction Table -->
        <div class="card">
            <div class="card-header">
                <h3>Data Transaksi</h3>
            </div>
            <div class="card-body">
                <?php if (empty($transaksi)): ?>
                    <p class="text-muted text-center">Tidak ada data transaksi untuk periode yang dipilih.</p>
                <?php else: ?>
                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Kode Transaksi</th>
                                    <th>Barang</th>
                                    <th>Jenis</th>
                                    <th>Jumlah</th>
                                    <th>Supplier</th>
                                    <th>Keterangan</th>
                                    <th>User</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($transaksi as $index => $item): ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($item['tanggal'])); ?></td>
                                        <td><?php echo htmlspecialchars($item['kode_transaksi']); ?></td>
                                        <td><?php echo htmlspecialchars($item['nama_barang']); ?></td>
                                        <td>
                                            <span class="<?php echo $item['jenis'] == 'masuk' ? 'text-success' : 'text-warning'; ?>">
                                                <?php echo ucfirst($item['jenis']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo $item['jumlah'] . ' ' . htmlspecialchars($item['satuan']); ?></td>
                                        <td><?php echo $item['nama_supplier'] ? htmlspecialchars($item['nama_supplier']) : '-'; ?></td>
                                        <td><?php echo htmlspecialchars($item['keterangan']); ?></td>
                                        <td><?php echo htmlspecialchars($item['username']); ?></td>
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

<?php include 'includes/footer.php'; ?>
