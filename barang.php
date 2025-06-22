<?php
require_once 'config/session.php';
require_once 'config/database.php';

// Check if user is logged in
requireLogin();

$user_id = $_SESSION['user_id'];
$is_admin = isAdmin();

// Handle delete (only admin can delete, or user can delete their own items)
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = mysqli_real_escape_string($conn, $_GET['delete']);
    
    // Check ownership for non-admin users
    if (!$is_admin) {
        $check_result = mysqli_query($conn, "SELECT user_id FROM barang WHERE id = '$id'");
        $barang_owner = mysqli_fetch_assoc($check_result);
        if (!$barang_owner || $barang_owner['user_id'] != $user_id) {
            $_SESSION['error'] = "Anda tidak memiliki izin untuk menghapus barang ini!";
            header('Location: barang.php');
            exit();
        }
    }
    
    $query = "UPDATE barang SET status = 'nonaktif' WHERE id = '$id'";
    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Barang berhasil dihapus!";
    } else {
        $_SESSION['error'] = "Error: " . mysqli_error($conn);
    }
    header('Location: barang.php');
    exit();
}

// Search and filter
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$kategori_filter = isset($_GET['kategori']) ? mysqli_real_escape_string($conn, $_GET['kategori']) : '';
$status_filter = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : 'aktif';

// Build query with user filter for non-admin
$where_conditions = ["b.status = '$status_filter'"];

if (!$is_admin) {
    $where_conditions[] = "b.user_id = '$user_id'";
}

if (!empty($search)) {
    $where_conditions[] = "(b.nama_barang LIKE '%$search%' OR b.kode_barang LIKE '%$search%')";
}

if (!empty($kategori_filter)) {
    $where_conditions[] = "b.kategori_id = '$kategori_filter'";
}

$where_clause = implode(" AND ", $where_conditions);

// Get barang data
$query = "
    SELECT b.*, k.nama_kategori, s.nama_supplier, u.username as created_by
    FROM barang b 
    LEFT JOIN kategori k ON b.kategori_id = k.id 
    LEFT JOIN supplier s ON b.supplier_id = s.id 
    LEFT JOIN users u ON b.user_id = u.id
    WHERE $where_clause
    ORDER BY b.nama_barang
";

$result = mysqli_query($conn, $query);
$barang_list = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $barang_list[] = $row;
    }
}

// Get kategori for filter
$result = mysqli_query($conn, "SELECT * FROM kategori WHERE status = 'aktif' ORDER BY nama_kategori");
$kategori_list = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $kategori_list[] = $row;
    }
}

$page_title = "Data Barang";
include 'includes/header.php';
?>

<div class="layout">
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <h1 class="page-title">Data Barang <?php echo $is_admin ? '' : '(Saya)'; ?></h1>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <!-- Action Buttons -->
        <div class="mb-3">
            <a href="barang_form.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Barang
            </a>
        </div>
        
        <!-- Search and Filter -->
        <div class="card mb-3">
            <div class="card-header">
                <h3>Filter Barang</h3>
            </div>
            <div class="card-body">
                <form method="GET" action="">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Cari Barang</label>
                            <input type="text" name="search" class="form-control" 
                                   placeholder="Nama atau kode barang..." 
                                   value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Kategori</label>
                            <select name="kategori" class="form-control">
                                <option value="">Semua Kategori</option>
                                <?php foreach ($kategori_list as $kategori): ?>
                                    <option value="<?php echo $kategori['id']; ?>" 
                                            <?php echo $kategori_filter == $kategori['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($kategori['nama_kategori']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <?php if ($is_admin): ?>
                        <div class="form-group">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-control">
                                <option value="aktif" <?php echo $status_filter == 'aktif' ? 'selected' : ''; ?>>Aktif</option>
                                <option value="nonaktif" <?php echo $status_filter == 'nonaktif' ? 'selected' : ''; ?>>Nonaktif</option>
                            </select>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filter
                    </button>
                    <a href="barang.php" class="btn btn-secondary">Reset</a>
                </form>
            </div>
        </div>
        
        <!-- Barang Table -->
        <div class="card">
            <div class="card-header">
                <h3>Daftar Barang</h3>
            </div>
            <div class="card-body">
                <?php if (empty($barang_list)): ?>
                    <p class="text-muted text-center">Tidak ada data barang.</p>
                <?php else: ?>
                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode</th>
                                    <th>Nama Barang</th>
                                    <th>Kategori</th>
                                    <th>Stok</th>
                                    <th>Satuan</th>
                                    <th>Harga</th>
                                    <th>Supplier</th>
                                    <?php if ($is_admin): ?>
                                    <th>Dibuat Oleh</th>
                                    <?php endif; ?>
                                    <th>Status</th>
                                    <th class="no-print">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($barang_list as $index => $barang): ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td><code><?php echo htmlspecialchars($barang['kode_barang']); ?></code></td>
                                        <td><?php echo htmlspecialchars($barang['nama_barang']); ?></td>
                                        <td><?php echo htmlspecialchars($barang['nama_kategori'] ?? '-'); ?></td>
                                        <td class="<?php echo $barang['stok'] <= $barang['stok_minimum'] ? 'text-danger' : 'text-success'; ?>">
                                            <strong><?php echo $barang['stok']; ?></strong>
                                            <?php if ($barang['stok'] <= $barang['stok_minimum']): ?>
                                                <i class="fas fa-exclamation-triangle text-warning"></i>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($barang['satuan']); ?></td>
                                        <td><?php echo formatRupiah($barang['harga']); ?></td>
                                        <td><?php echo htmlspecialchars($barang['nama_supplier'] ?? '-'); ?></td>
                                        <?php if ($is_admin): ?>
                                        <td><?php echo htmlspecialchars($barang['created_by'] ?? '-'); ?></td>
                                        <?php endif; ?>
                                        <td>
                                            <?php if ($barang['status'] == 'aktif'): ?>
                                                <span class="badge bg-success">Aktif</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Nonaktif</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="no-print">
                                            <?php if ($is_admin || $barang['user_id'] == $user_id): ?>
                                                <a href="barang_form.php?edit=<?php echo $barang['id']; ?>" 
                                                   class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <?php if ($barang['status'] == 'aktif'): ?>
                                                    <a href="?delete=<?php echo $barang['id']; ?>" 
                                                       class="btn btn-danger btn-sm"
                                                       onclick="return confirm('Yakin ingin menghapus barang ini?')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <a href="barang_form.php?restore=<?php echo $barang['id']; ?>" 
                                                       class="btn btn-success btn-sm">
                                                        <i class="fas fa-undo"></i>
                                                    </a>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
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
