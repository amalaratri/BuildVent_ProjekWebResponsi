<?php
require_once 'config/session.php';
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_supplier = mysqli_real_escape_string($conn, trim($_POST['nama_supplier']));
    $kontak = mysqli_real_escape_string($conn, trim($_POST['kontak']));
    $alamat = mysqli_real_escape_string($conn, trim($_POST['alamat']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $edit_id = isset($_POST['edit_id']) ? mysqli_real_escape_string($conn, $_POST['edit_id']) : null;
    
    // Validation
    $errors = [];
    
    if (empty($nama_supplier)) {
        $errors[] = "Nama supplier harus diisi!";
    }
    
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format email tidak valid!";
    }
    
    // Check duplicate name
    if ($edit_id) {
        $check_query = "SELECT id FROM supplier WHERE nama_supplier = '$nama_supplier' AND id != '$edit_id'";
    } else {
        $check_query = "SELECT id FROM supplier WHERE nama_supplier = '$nama_supplier'";
    }
    
    $check_result = mysqli_query($conn, $check_query);
    if ($check_result && mysqli_num_rows($check_result) > 0) {
        $errors[] = "Nama supplier sudah ada!";
    }
    
    if (empty($errors)) {
        if ($edit_id) {
            // Update supplier
            $query = "UPDATE supplier SET nama_supplier = '$nama_supplier', kontak = '$kontak', alamat = '$alamat', email = '$email', updated_at = NOW() WHERE id = '$edit_id'";
            $success_msg = "Supplier berhasil diupdate!";
        } else {
            // Insert new supplier
            $query = "INSERT INTO supplier (nama_supplier, kontak, alamat, email, created_at) VALUES ('$nama_supplier', '$kontak', '$alamat', '$email', NOW())";
            $success_msg = "Supplier berhasil ditambahkan!";
        }
        
        if (mysqli_query($conn, $query)) {
            $_SESSION['success'] = $success_msg;
            header('Location: supplier.php');
            exit();
        } else {
            $errors[] = "Error: " . mysqli_error($conn);
        }
    }
}

// Handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = mysqli_real_escape_string($conn, $_GET['delete']);
    
    // Check if supplier is being used
    $check_result = mysqli_query($conn, "SELECT COUNT(*) as count FROM transaksi WHERE supplier_id = '$id'");
    $usage_count = mysqli_fetch_assoc($check_result)['count'];
    
    if ($usage_count > 0) {
        $_SESSION['error'] = "Supplier tidak dapat dihapus karena masih digunakan dalam $usage_count transaksi!";
    } else {
        $query = "UPDATE supplier SET status = 'nonaktif' WHERE id = '$id'";
        if (mysqli_query($conn, $query)) {
            $_SESSION['success'] = "Supplier berhasil dihapus!";
        } else {
            $_SESSION['error'] = "Error: " . mysqli_error($conn);
        }
    }
    
    header('Location: supplier.php');
    exit();
}

// Get supplier for editing
$edit_supplier = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $id = mysqli_real_escape_string($conn, $_GET['edit']);
    $result = mysqli_query($conn, "SELECT * FROM supplier WHERE id = '$id'");
    if ($result && mysqli_num_rows($result) > 0) {
        $edit_supplier = mysqli_fetch_assoc($result);
    }
}

// Get all supplier
$result = mysqli_query($conn, "SELECT * FROM supplier WHERE status = 'aktif' ORDER BY nama_supplier");
$supplier_list = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $supplier_list[] = $row;
    }
}

$page_title = "Data Supplier";
include 'includes/header.php';
?>

<div class="layout">
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <h1 class="page-title">Data Supplier</h1>
        
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
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <ul style="margin: 0; padding-left: 20px;">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="form-row">
            <!-- Form Supplier -->
            <div class="card">
                <div class="card-header">
                    <h3><?php echo $edit_supplier ? "Edit Supplier" : "Tambah Supplier"; ?></h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <?php if ($edit_supplier): ?>
                            <input type="hidden" name="edit_id" value="<?php echo $edit_supplier['id']; ?>">
                        <?php endif; ?>
                        
                        <div class="form-group">
                            <label class="form-label">Nama Supplier *</label>
                            <input type="text" name="nama_supplier" class="form-control" 
                                   value="<?php echo $edit_supplier ? htmlspecialchars($edit_supplier['nama_supplier']) : ''; ?>" 
                                   required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Kontak</label>
                            <input type="text" name="kontak" class="form-control" 
                                   value="<?php echo $edit_supplier ? htmlspecialchars($edit_supplier['kontak']) : ''; ?>" 
                                   placeholder="Nomor telepon/HP">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" 
                                   value="<?php echo $edit_supplier ? htmlspecialchars($edit_supplier['email']) : ''; ?>" 
                                   placeholder="email@example.com">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat" class="form-control" rows="3" 
                                      placeholder="Alamat lengkap supplier..."><?php echo $edit_supplier ? htmlspecialchars($edit_supplier['alamat']) : ''; ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <?php echo $edit_supplier ? "Update Supplier" : "Simpan Supplier"; ?>
                            </button>
                            <?php if ($edit_supplier): ?>
                                <a href="supplier.php" class="btn btn-secondary">Batal</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- List Supplier -->
            <div class="card">
                <div class="card-header">
                    <h3>Daftar Supplier</h3>
                </div>
                <div class="card-body">
                    <?php if (empty($supplier_list)): ?>
                        <p class="text-muted text-center">Belum ada data supplier.</p>
                    <?php else: ?>
                        <div class="table-container">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Supplier</th>
                                        <th>Kontak</th>
                                        <th>Email</th>
                                        <th>Alamat</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($supplier_list as $index => $supplier): ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td><?php echo htmlspecialchars($supplier['nama_supplier']); ?></td>
                                            <td><?php echo htmlspecialchars($supplier['kontak']); ?></td>
                                            <td><?php echo htmlspecialchars($supplier['email']); ?></td>
                                            <td><?php echo htmlspecialchars($supplier['alamat']); ?></td>
                                            <td>
                                                <a href="?edit=<?php echo $supplier['id']; ?>" 
                                                   class="btn btn-warning btn-sm">Edit</a>
                                                <a href="?delete=<?php echo $supplier['id']; ?>" 
                                                   class="btn btn-danger btn-sm"
                                                   onclick="return confirm('Yakin ingin menghapus supplier ini?')">Hapus</a>
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
</div>

<?php include 'includes/footer.php'; ?>
