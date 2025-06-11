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
    $nama_kategori = mysqli_real_escape_string($conn, trim($_POST['nama_kategori']));
    $deskripsi = mysqli_real_escape_string($conn, trim($_POST['deskripsi']));
    $edit_id = isset($_POST['edit_id']) ? mysqli_real_escape_string($conn, $_POST['edit_id']) : null;
    
    // Validation
    $errors = [];
    
    if (empty($nama_kategori)) {
        $errors[] = "Nama kategori harus diisi!";
    }
    
    // Check duplicate name
    if ($edit_id) {
        $check_query = "SELECT id FROM kategori WHERE nama_kategori = '$nama_kategori' AND id != '$edit_id'";
    } else {
        $check_query = "SELECT id FROM kategori WHERE nama_kategori = '$nama_kategori'";
    }
    
    $check_result = mysqli_query($conn, $check_query);
    if ($check_result && mysqli_num_rows($check_result) > 0) {
        $errors[] = "Nama kategori sudah ada!";
    }
    
    if (empty($errors)) {
        if ($edit_id) {
            // Update kategori
            $query = "UPDATE kategori SET nama_kategori = '$nama_kategori', deskripsi = '$deskripsi', updated_at = NOW() WHERE id = '$edit_id'";
            $success_msg = "Kategori berhasil diupdate!";
        } else {
            // Insert new kategori
            $query = "INSERT INTO kategori (nama_kategori, deskripsi, created_at) VALUES ('$nama_kategori', '$deskripsi', NOW())";
            $success_msg = "Kategori berhasil ditambahkan!";
        }
        
        if (mysqli_query($conn, $query)) {
            $_SESSION['success'] = $success_msg;
            header('Location: kategori.php');
            exit();
        } else {
            $errors[] = "Error: " . mysqli_error($conn);
        }
    }
}

// Handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = mysqli_real_escape_string($conn, $_GET['delete']);
    
    // Check if kategori is being used
    $check_result = mysqli_query($conn, "SELECT COUNT(*) as count FROM barang WHERE kategori_id = '$id'");
    $usage_count = mysqli_fetch_assoc($check_result)['count'];
    
    if ($usage_count > 0) {
        $_SESSION['error'] = "Kategori tidak dapat dihapus karena masih digunakan oleh $usage_count barang!";
    } else {
        $query = "UPDATE kategori SET status = 'nonaktif' WHERE id = '$id'";
        if (mysqli_query($conn, $query)) {
            $_SESSION['success'] = "Kategori berhasil dihapus!";
        } else {
            $_SESSION['error'] = "Error: " . mysqli_error($conn);
        }
    }
    
    header('Location: kategori.php');
    exit();
}

// Get kategori for editing
$edit_kategori = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $id = mysqli_real_escape_string($conn, $_GET['edit']);
    $result = mysqli_query($conn, "SELECT * FROM kategori WHERE id = '$id'");
    if ($result && mysqli_num_rows($result) > 0) {
        $edit_kategori = mysqli_fetch_assoc($result);
    }
}

// Get all kategori
$result = mysqli_query($conn, "SELECT * FROM kategori WHERE status = 'aktif' ORDER BY nama_kategori");
$kategori_list = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $kategori_list[] = $row;
    }
}

$page_title = "Data Kategori";
include 'includes/header.php';
?>

<div class="layout">
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <h1 class="page-title">Data Kategori</h1>
        
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
            <!-- Form Kategori -->
            <div class="card">
                <div class="card-header">
                    <h3><?php echo $edit_kategori ? "Edit Kategori" : "Tambah Kategori"; ?></h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <?php if ($edit_kategori): ?>
                            <input type="hidden" name="edit_id" value="<?php echo $edit_kategori['id']; ?>">
                        <?php endif; ?>
                        
                        <div class="form-group">
                            <label class="form-label">Nama Kategori *</label>
                            <input type="text" name="nama_kategori" class="form-control" 
                                   value="<?php echo $edit_kategori ? htmlspecialchars($edit_kategori['nama_kategori']) : ''; ?>" 
                                   required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="deskripsi" class="form-control" rows="3" 
                                      placeholder="Deskripsi kategori..."><?php echo $edit_kategori ? htmlspecialchars($edit_kategori['deskripsi']) : ''; ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <?php echo $edit_kategori ? "Update Kategori" : "Simpan Kategori"; ?>
                            </button>
                            <?php if ($edit_kategori): ?>
                                <a href="kategori.php" class="btn btn-secondary">Batal</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- List Kategori -->
            <div class="card">
                <div class="card-header">
                    <h3>Daftar Kategori</h3>
                </div>
                <div class="card-body">
                    <?php if (empty($kategori_list)): ?>
                        <p class="text-muted text-center">Belum ada data kategori.</p>
                    <?php else: ?>
                        <div class="table-container">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Kategori</th>
                                        <th>Deskripsi</th>
                                        <th>Dibuat</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($kategori_list as $index => $kategori): ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td><?php echo htmlspecialchars($kategori['nama_kategori']); ?></td>
                                            <td><?php echo htmlspecialchars($kategori['deskripsi']); ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($kategori['created_at'])); ?></td>
                                            <td>
                                                <a href="?edit=<?php echo $kategori['id']; ?>" 
                                                   class="btn btn-warning btn-sm">Edit</a>
                                                <a href="?delete=<?php echo $kategori['id']; ?>" 
                                                   class="btn btn-danger btn-sm"
                                                   onclick="return confirm('Yakin ingin menghapus kategori ini?')">Hapus</a>
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
