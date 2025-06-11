<?php
require_once 'config/session.php';
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$edit_mode = false;
$transaksi = null;

// Check if editing
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $edit_mode = true;
    $id = mysqli_real_escape_string($conn, $_GET['edit']);
    $result = mysqli_query($conn, "SELECT * FROM transaksi WHERE id = '$id'");
    
    if ($result && mysqli_num_rows($result) > 0) {
        $transaksi = mysqli_fetch_assoc($result);
    } else {
        $_SESSION['error'] = "Transaksi tidak ditemukan!";
        header('Location: transaksi.php');
        exit();
    }
}

// Get barang and supplier for dropdowns
$result = mysqli_query($conn, "SELECT * FROM barang WHERE status = 'aktif' ORDER BY nama_barang");
$barang_list = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $barang_list[] = $row;
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
    $barang_id = mysqli_real_escape_string($conn, $_POST['barang_id']);
    $jenis = mysqli_real_escape_string($conn, $_POST['jenis']);
    $jumlah = (int)$_POST['jumlah'];
    $supplier_id = !empty($_POST['supplier_id']) ? mysqli_real_escape_string($conn, $_POST['supplier_id']) : 'NULL';
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);
    $tanggal = mysqli_real_escape_string($conn, $_POST['tanggal']);
    
    // Validation
    $errors = [];
    
    if (empty($barang_id)) {
        $errors[] = "Barang harus dipilih!";
    }
    
    if (empty($jenis)) {
        $errors[] = "Jenis transaksi harus dipilih!";
    }
    
    if (empty($jumlah) || $jumlah <= 0) {
        $errors[] = "Jumlah harus diisi dan lebih dari 0!";
    }
    
    if (empty($tanggal)) {
        $errors[] = "Tanggal harus diisi!";
    }
    
    // Check stock for keluar transaction
    if ($jenis == 'keluar' && !empty($barang_id) && !empty($jumlah)) {
        $stock_result = mysqli_query($conn, "SELECT stok FROM barang WHERE id = '$barang_id'");
        $current_stock = mysqli_fetch_assoc($stock_result)['stok'];
        
        if ($edit_mode) {
            // Add back the previous transaction amount
            $previous_amount = $transaksi['jenis'] == 'keluar' ? $transaksi['jumlah'] : -$transaksi['jumlah'];
            $current_stock += $previous_amount;
        }
        
        if ($current_stock < $jumlah) {
            $errors[] = "Stok tidak mencukupi! Stok tersedia: $current_stock";
        }
    }
    
    if (empty($errors)) {
        // Start transaction
        mysqli_autocommit($conn, FALSE);
        
        try {
            if ($edit_mode) {
                // Update transaction
                $query = "UPDATE transaksi SET 
                    barang_id = '$barang_id', 
                    jenis = '$jenis', 
                    jumlah = $jumlah, 
                    supplier_id = $supplier_id, 
                    keterangan = '$keterangan', 
                    tanggal = '$tanggal', 
                    updated_at = NOW()
                    WHERE id = '{$transaksi['id']}'";
                
                if (!mysqli_query($conn, $query)) {
                    throw new Exception(mysqli_error($conn));
                }
                
                // Revert previous stock change
                if ($transaksi['jenis'] == 'masuk') {
                    $revert_query = "UPDATE barang SET stok = stok - {$transaksi['jumlah']} WHERE id = '{$transaksi['barang_id']}'";
                } else {
                    $revert_query = "UPDATE barang SET stok = stok + {$transaksi['jumlah']} WHERE id = '{$transaksi['barang_id']}'";
                }
                
                if (!mysqli_query($conn, $revert_query)) {
                    throw new Exception(mysqli_error($conn));
                }
                
                $success_message = "Transaksi berhasil diupdate!";
            } else {
                // Generate transaction code
                $kode_transaksi = 'TRX' . date('Ymd') . sprintf('%04d', rand(1, 9999));
                
                // Insert new transaction
                $query = "INSERT INTO transaksi (kode_transaksi, barang_id, jenis, jumlah, supplier_id, keterangan, tanggal, user_id, created_at) 
                    VALUES ('$kode_transaksi', '$barang_id', '$jenis', $jumlah, $supplier_id, '$keterangan', '$tanggal', '{$_SESSION['user_id']}', NOW())";
                
                if (!mysqli_query($conn, $query)) {
                    throw new Exception(mysqli_error($conn));
                }
                
                $success_message = "Transaksi berhasil ditambahkan!";
            }
            
            // Update stock
            if ($jenis == 'masuk') {
                $stock_query = "UPDATE barang SET stok = stok + $jumlah WHERE id = '$barang_id'";
            } else {
                $stock_query = "UPDATE barang SET stok = stok - $jumlah WHERE id = '$barang_id'";
            }
            
            if (!mysqli_query($conn, $stock_query)) {
                throw new Exception(mysqli_error($conn));
            }
            
            mysqli_commit($conn);
            mysqli_autocommit($conn, TRUE);
            
            $_SESSION['success'] = $success_message;
            header('Location: transaksi.php');
            exit();
            
        } catch (Exception $e) {
            mysqli_rollback($conn);
            mysqli_autocommit($conn, TRUE);
            $errors[] = "Error: " . $e->getMessage();
        }
    }
}

$page_title = $edit_mode ? "Edit Transaksi" : "Tambah Transaksi";
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
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-header">
                <h3><?php echo $edit_mode ? "Edit Data Transaksi" : "Tambah Data Transaksi"; ?></h3>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Barang *</label>
                            <select name="barang_id" class="form-control" required>
                                <option value="">Pilih Barang</option>
                                <?php foreach ($barang_list as $barang): ?>
                                    <option value="<?php echo $barang['id']; ?>" 
                                            <?php echo ($edit_mode && $transaksi['barang_id'] == $barang['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($barang['nama_barang']); ?> 
                                        (Stok: <?php echo $barang['stok']; ?> <?php echo htmlspecialchars($barang['satuan']); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Jenis Transaksi *</label>
                            <select name="jenis" class="form-control" required>
                                <option value="">Pilih Jenis</option>
                                <option value="masuk" <?php echo ($edit_mode && $transaksi['jenis'] == 'masuk') ? 'selected' : ''; ?>>
                                    Barang Masuk
                                </option>
                                <option value="keluar" <?php echo ($edit_mode && $transaksi['jenis'] == 'keluar') ? 'selected' : ''; ?>>
                                    Barang Keluar
                                </option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Jumlah *</label>
                            <input type="number" name="jumlah" class="form-control" 
                                   value="<?php echo $edit_mode ? $transaksi['jumlah'] : ''; ?>" 
                                   min="1" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Supplier</label>
                            <select name="supplier_id" class="form-control">
                                <option value="">Pilih Supplier (Opsional)</option>
                                <?php foreach ($supplier_list as $supplier): ?>
                                    <option value="<?php echo $supplier['id']; ?>" 
                                            <?php echo ($edit_mode && $transaksi['supplier_id'] == $supplier['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($supplier['nama_supplier']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Tanggal *</label>
                        <input type="datetime-local" name="tanggal" class="form-control" 
                               value="<?php echo $edit_mode ? date('Y-m-d\TH:i', strtotime($transaksi['tanggal'])) : date('Y-m-d\TH:i'); ?>" 
                               required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="3" 
                                  placeholder="Keterangan tambahan..."><?php echo $edit_mode ? htmlspecialchars($transaksi['keterangan']) : ''; ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <?php echo $edit_mode ? "Update Transaksi" : "Simpan Transaksi"; ?>
                        </button>
                        <a href="transaksi.php" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
