<?php
require_once 'config/session.php';
require_once 'config/database.php';

// Check if user is admin
requireAdmin();

// Handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = mysqli_real_escape_string($conn, $_GET['delete']);
    $query = "UPDATE users SET status = 'nonaktif' WHERE id = '$id' AND role = 'supplier'";
    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Akun supplier berhasil dihapus!";
    } else {
        $_SESSION['error'] = "Error: " . mysqli_error($conn);
    }
    header('Location: supplier_accounts.php');
    exit();
}

// Handle activate
if (isset($_GET['activate']) && is_numeric($_GET['activate'])) {
    $id = mysqli_real_escape_string($conn, $_GET['activate']);
    $query = "UPDATE users SET status = 'aktif' WHERE id = '$id' AND role = 'supplier'";
    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Akun supplier berhasil diaktifkan!";
    } else {
        $_SESSION['error'] = "Error: " . mysqli_error($conn);
    }
    header('Location: supplier_accounts.php');
    exit();
}

// Get all supplier accounts
$result = mysqli_query($conn, "SELECT * FROM users WHERE role = 'supplier' ORDER BY username");
$suppliers = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $suppliers[] = $row;
    }
}

$page_title = "Kelola Akun Supplier";
include 'includes/header.php';
?>

<div class="layout">
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <h1 class="page-title">Kelola Akun Supplier</h1>
        
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
        
        <!-- Action Buttons -->
        <div class="mb-3">
            <a href="supplier_account_form.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Akun Supplier
            </a>
        </div>
        
        <!-- Supplier Accounts Table -->
        <div class="card">
            <div class="card-header">
                <h3>Daftar Akun Supplier</h3>
            </div>
            <div class="card-body">
                <?php if (empty($suppliers)): ?>
                    <p class="text-muted text-center">Tidak ada akun supplier.</p>
                <?php else: ?>
                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Username</th>
                                    <th>Nama Lengkap</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Terdaftar</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($suppliers as $index => $supplier): ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td><?php echo htmlspecialchars($supplier['username']); ?></td>
                                        <td><?php echo htmlspecialchars($supplier['nama_lengkap']); ?></td>
                                        <td><?php echo htmlspecialchars($supplier['email']); ?></td>
                                        <td>
                                            <?php if ($supplier['status'] == 'aktif'): ?>
                                                <span class="badge bg-success">Aktif</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Nonaktif</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo date('d/m/Y', strtotime($supplier['created_at'])); ?></td>
                                        <td>
                                            <a href="supplier_account_form.php?edit=<?php echo $supplier['id']; ?>" 
                                               class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <?php if ($supplier['status'] == 'aktif'): ?>
                                                <a href="?delete=<?php echo $supplier['id']; ?>" 
                                                   class="btn btn-danger btn-sm"
                                                   onclick="return confirm('Yakin ingin menonaktifkan akun supplier ini?')">
                                                    <i class="fas fa-ban"></i>
                                                </a>
                                            <?php else: ?>
                                                <a href="?activate=<?php echo $supplier['id']; ?>" 
                                                   class="btn btn-success btn-sm"
                                                   onclick="return confirm('Yakin ingin mengaktifkan akun supplier ini?')">
                                                    <i class="fas fa-check"></i>
                                                </a>
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
