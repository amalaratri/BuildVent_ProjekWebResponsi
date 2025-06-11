<?php
require_once 'config/session.php';
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = mysqli_real_escape_string($conn, $_GET['delete']);
    $query = "DELETE FROM transaksi WHERE id = '$id'";
    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Transaksi berhasil dihapus!";
    } else {
        $_SESSION['error'] = "Error: " . mysqli_error($conn);
    }
    header('Location: transaksi.php');
    exit();
}

// Search and filter
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$jenis_filter = isset($_GET['jenis']) ? mysqli_real_escape_string($conn, $_GET['jenis']) : '';
$tanggal_dari = isset($_GET['tanggal_dari']) ? mysqli_real_escape_string($conn, $_GET['tanggal_dari']) : '';
$tanggal_sampai = isset($_GET['tanggal_sampai']) ? mysqli_real_escape_string($conn, $_GET['tanggal_sampai']) : '';

// Build query
$where_conditions = [];

if (!empty($search)) {
    $where_conditions[] = "(b.nama_barang LIKE '%$search%' OR t.keterangan LIKE '%$search%')";
}

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

$page_title = "Data Transaksi";
include 'includes/header.php';
?>

<div class="layout">
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <h1 class="page-title">Data Transaksi</h1>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <!-- Action Buttons -->
        <div class="mb-3">
            <a href="transaksi_form.php" class="btn btn-primary">Tambah Transaksi</a>
            <a href="laporan.php" class="btn btn-success">Lihat Laporan</a>
        </div>
        
        <!-- Search and Filter -->
        <div class="card mb-3">
            <div class="card-header">
                <h3>Filter Transaksi</h3>
            </div>
            <div class="card-body">
                <form method="GET" action="">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Cari Transaksi</label>
                            <input type="text" name="search" class="form-control" 
                                   placeholder="Nama barang atau keterangan..." 
                                   value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                        
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
                    <a href="transaksi.php" class="btn btn-secondary">Reset</a>
                </form>
            </div>
        </div>
        
        <!-- Transaksi Table -->
        <div class="card">
            <div class="card-header">
                <h3>Daftar Transaksi</h3>
            </div>
            <div class="card-body">
                <?php if (empty($transaksi)): ?>
                    <p class="text-muted text-center">Tidak ada data transaksi.</p>
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
                                    <th class="no-print">Aksi</th>
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
                                        <td class="no-print">
                                            <a href="transaksi_form.php?edit=<?php echo $item['id']; ?>" 
                                               class="btn btn-warning btn-sm">Edit</a>
                                            <a href="?delete=<?php echo $item['id']; ?>" 
                                               class="btn btn-danger btn-sm"
                                               onclick="return confirm('Yakin ingin menghapus transaksi ini?')">Hapus</a>
                                        </td>
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
