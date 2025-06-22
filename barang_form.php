<?php
require_once 'config/session.php';
require_once 'config/database.php';

// Check if user is logged in
requireLogin();

$user_id = $_SESSION['user_id'];
$is_admin = isAdmin();

$edit_mode = false;
$barang = null;

// Check if editing
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $edit_mode = true;
    $id = mysqli_real_escape_string($conn, $_GET['edit']);
    
    // Check ownership for non-admin users
    $ownership_check = $is_admin ? "" : "AND user_id = '$user_id'";
    $result = mysqli_query($conn, "SELECT * FROM barang WHERE id = '$id' $ownership_check");
    
    if ($result && mysqli_num_rows($result) > 0) {
        $barang = mysqli_fetch_assoc($result);
    } else {
        $_SESSION['error'] = "Barang tidak ditemukan atau Anda tidak memiliki izin untuk mengeditnya!";
        header('Location: barang.php');
        exit();
    }
}

// Check if restoring
if (isset($_GET['restore']) && is_numeric($_GET['restore'])) {
    $id = mysqli_real_escape_string($conn, $_GET['restore']);
    
    // Check ownership for non-admin users
    $ownership_check = $is_admin ? "" : "AND user_id = '$user_id'";
    $query = "UPDATE barang SET status = 'aktif' WHERE id = '$id' $ownership_check";
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Barang berhasil diaktifkan kembali!";
    } else {
        $_SESSION['error'] = "Error: " . mysqli_error($conn);
    }
    header('Location: barang.php');
    exit();
}

// Get kategori and supplier for dropdowns
$result = mysqli_query($conn, "SELECT * FROM kategori WHERE status = 'aktif' ORDER BY nama_kategori");
$kategori_list = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $kategori_list[] = $row;
    }
}

$result = mysqli_query($conn, "SELECT * FROM supplier WHERE status = 'aktif' ORDER BY nama_supplier");
$supplier_list = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $supplier_list[] = $row;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kode_barang = mysqli_real_escape_string($conn, trim($_POST['kode_barang']));
    $nama_barang = mysqli_real_escape_string($conn, trim($_POST['nama_barang']));
    $kategori_id = !empty($_POST['kategori_id']) ? mysqli_real_escape_string($conn, $_POST['kategori_id']) : 'NULL';
    $supplier_id = !empty($_POST['supplier_id']) ? mysqli_real_escape_string($conn, $_POST['supplier_id']) : 'NULL';
    $satuan = mysqli_real_escape_string($conn, trim($_POST['satuan']));
    $stok = (int)$_POST['stok'];
    $stok_minimum = (int)$_POST['stok_minimum'];
    $harga = str_replace(['Rp', '.', ' '], '', $_POST['harga']);
    $lokasi = mysqli_real_escape_string($conn, trim($_POST['lokasi']));
    $deskripsi = mysqli_real_escape_string($conn, trim($_POST['deskripsi']));
    
    // Validation
    $errors = [];
    
    if (empty($kode_barang)) {
        $errors[] = "Kode barang harus diisi!";
    }
    
    if (empty($nama_barang)) {
        $errors[] = "Nama barang harus diisi!";
    }
    
    if (empty($satuan)) {
        $errors[] = "Satuan harus diisi!";
    }
    
    // Check duplicate code
    if ($edit_mode) {
        $check_query = "SELECT id FROM barang WHERE kode_barang = '$kode_barang' AND id != '{$barang['id']}'";
    } else {
        $check_query = "SELECT id FROM barang WHERE kode_barang = '$kode_barang'";
    }
    
    $check_result = mysqli_query($conn, $check_query);
    if ($check_result && mysqli_num_rows($check_result) > 0) {
        $errors[] = "Kode barang sudah digunakan!";
    }
    
    if (empty($errors)) {
        if ($edit_mode) {
            // Update barang
            $query = "UPDATE barang SET 
                kode_barang = '$kode_barang', 
                nama_barang = '$nama_barang', 
                kategori_id = $kategori_id, 
                supplier_id = $supplier_id, 
                satuan = '$satuan', 
                stok = $stok, 
                stok_minimum = $stok_minimum, 
                harga = $harga, 
                lokasi = '$lokasi', 
                deskripsi = '$deskripsi', 
                updated_at = NOW() 
                WHERE id = '{$barang['id']}'";
        } else {
            // Insert new barang with user_id
            $query = "INSERT INTO barang (kode_barang, nama_barang, kategori_id, supplier_id, satuan, stok, stok_minimum, harga, lokasi, deskripsi, user_id, created_at) 
                VALUES ('$kode_barang', '$nama_barang', $kategori_id, $supplier_id, '$satuan', $stok, $stok_minimum, $harga, '$lokasi', '$deskripsi', '$user_id', NOW())";
        }
        
        if (mysqli_query($conn, $query)) {
            $_SESSION['success'] = $edit_mode ? "Barang berhasil diupdate!" : "Barang berhasil ditambahkan!";
            header('Location: barang.php');
            exit();
        } else {
            $errors[] = "Error: " . mysqli_error($conn);
        }
    }
}

// Generate kode barang if new
if (!$edit_mode && empty($_POST)) {
    $kode_barang = generateKode('BRG', 'barang', 'kode_barang');
} else {
    $kode_barang = $_POST['kode_barang'] ?? $barang['kode_barang'] ?? '';
}

$page_title = $edit_mode ? "Edit Barang" : "Tambah Barang";
include 'includes/header.php';
?>

<div class="layout">
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <h1 class="page-title"><?php echo $page_title; ?></h1>
        
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
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-header">
                <h3><?php echo $edit_mode ? "Edit Data Barang" : "Tambah Data Barang"; ?></h3>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Kode Barang *</label>
                            <input type="text" name="kode_barang" class="form-control" 
                                   value="<?php echo htmlspecialchars($kode_barang); ?>" required>
                            <small class="text-muted">Kode unik untuk identifikasi barang</small>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Nama Barang *</label>
                            <input type="text" name="nama_barang" class="form-control" 
                                   value="<?php echo isset($_POST['nama_barang']) ? htmlspecialchars($_POST['nama_barang']) : ($barang ? htmlspecialchars($barang['nama_barang']) : ''); ?>" 
                                   required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Kategori</label>
                            <select name="kategori_id" class="form-control">
                                <option value="">Pilih Kategori</option>
                                <?php foreach ($kategori_list as $kategori): ?>
                                    <option value="<?php echo $kategori['id']; ?>" 
                                            <?php echo (isset($_POST['kategori_id']) && $_POST['kategori_id'] == $kategori['id']) || ($barang && $barang['kategori_id'] == $kategori['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($kategori['nama_kategori']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Supplier</label>
                            <select name="supplier_id" class="form-control">
                                <option value="">Pilih Supplier</option>
                                <?php foreach ($supplier_list as $supplier): ?>
                                    <option value="<?php echo $supplier['id']; ?>" 
                                            <?php echo (isset($_POST['supplier_id']) && $_POST['supplier_id'] == $supplier['id']) || ($barang && $barang['supplier_id'] == $supplier['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($supplier['nama_supplier']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Satuan *</label>
                            <select name="satuan" class="form-control" required>
                                <option value="">Pilih Satuan</option>
                                <?php 
                                $satuan_options = ['Pcs', 'Kg', 'Sak', 'Batang', 'Kaleng', 'M³', 'Roll', 'Lembar', 'Dus', 'Set'];
                                $current_satuan = isset($_POST['satuan']) ? $_POST['satuan'] : ($barang ? $barang['satuan'] : '');
                                foreach ($satuan_options as $option): 
                                ?>
                                    <option value="<?php echo $option; ?>" 
                                            <?php echo $current_satuan == $option ? 'selected' : ''; ?>>
                                        <?php echo $option; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Stok</label>
                            <input type="number" name="stok" class="form-control" 
                                   value="<?php echo isset($_POST['stok']) ? $_POST['stok'] : ($barang ? $barang['stok'] : '0'); ?>" 
                                   min="0">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Stok Minimum</label>
                            <input type="number" name="stok_minimum" class="form-control" 
                                   value="<?php echo isset($_POST['stok_minimum']) ? $_POST['stok_minimum'] : ($barang ? $barang['stok_minimum'] : '0'); ?>" 
                                   min="0">
                            <small class="text-muted">Batas minimum untuk peringatan stok</small>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Harga</label>
                            <input type="text" name="harga" class="form-control" 
                                   value="<?php echo isset($_POST['harga']) ? $_POST['harga'] : ($barang ? formatRupiah($barang['harga']) : ''); ?>" 
                                   onkeyup="formatNumber(this)">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Lokasi Penyimpanan</label>
                            <input type="text" name="lokasi" class="form-control" 
                                   value="<?php echo isset($_POST['lokasi']) ? htmlspecialchars($_POST['lokasi']) : ($barang ? htmlspecialchars($barang['lokasi']) : ''); ?>" 
                                   placeholder="Contoh: Gudang A - Rak 1">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="3" 
                                  placeholder="Deskripsi detail barang (opsional)"><?php echo isset($_POST['deskripsi']) ? htmlspecialchars($_POST['deskripsi']) : ($barang ? htmlspecialchars($barang['deskripsi']) : ''); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> <?php echo $edit_mode ? "Update Barang" : "Simpan Barang"; ?>
                        </button>
                        <a href="barang.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function formatNumber(input) {
    // Remove all non-digit characters except for the decimal point
    let value = input.value.replace(/[^\d]/g, '');
    
    // Format as currency
    if (value) {
        value = parseInt(value).toLocaleString('id-ID');
        input.value = 'Rp ' + value;
    }
}
</script>

<?php include 'includes/footer.php'; ?>
