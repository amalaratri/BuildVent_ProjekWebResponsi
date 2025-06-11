<?php
require_once 'config/session.php';
require_once 'config/database.php';

// Check if user is admin
requireAdmin();

$edit_mode = false;
$user = null;

// Check if editing
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $edit_mode = true;
    $id = mysqli_real_escape_string($conn, $_GET['edit']);
    $result = mysqli_query($conn, "SELECT * FROM users WHERE id = '$id'");
    
    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
    } else {
        $_SESSION['error'] = "Pengguna tidak ditemukan!";
        header('Location: users.php');
        exit();
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $nama_lengkap = mysqli_real_escape_string($conn, trim($_POST['nama_lengkap']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validation
    $errors = [];
    
    if (empty($username)) {
        $errors[] = "Username harus diisi!";
    }
    
    if (empty($nama_lengkap)) {
        $errors[] = "Nama lengkap harus diisi!";
    }
    
    if (empty($email)) {
        $errors[] = "Email harus diisi!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format email tidak valid!";
    }
    
    // Check duplicate username
    if ($edit_mode) {
        $check_query = "SELECT id FROM users WHERE username = '$username' AND id != '{$user['id']}'";
    } else {
        $check_query = "SELECT id FROM users WHERE username = '$username'";
    }
    
    $check_result = mysqli_query($conn, $check_query);
    if ($check_result && mysqli_num_rows($check_result) > 0) {
        $errors[] = "Username sudah digunakan!";
    }
    
    // Check duplicate email
    if ($edit_mode) {
        $check_query = "SELECT id FROM users WHERE email = '$email' AND id != '{$user['id']}'";
    } else {
        $check_query = "SELECT id FROM users WHERE email = '$email'";
    }
    
    $check_result = mysqli_query($conn, $check_query);
    if ($check_result && mysqli_num_rows($check_result) > 0) {
        $errors[] = "Email sudah digunakan!";
    }
    
    // Password validation
    if (!$edit_mode || !empty($password)) {
        if (strlen($password) < 6) {
            $errors[] = "Password minimal 6 karakter!";
        }
        
        if ($password !== $confirm_password) {
            $errors[] = "Konfirmasi password tidak cocok!";
        }
    }
    
    if (empty($errors)) {
        if ($edit_mode) {
            if (!empty($password)) {
                // Update user with new password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $query = "UPDATE users SET 
                    username = '$username', 
                    nama_lengkap = '$nama_lengkap', 
                    email = '$email', 
                    role = '$role', 
                    status = '$status', 
                    password = '$hashed_password', 
                    updated_at = NOW() 
                    WHERE id = '{$user['id']}'";
            } else {
                // Update user without changing password
                $query = "UPDATE users SET 
                    username = '$username', 
                    nama_lengkap = '$nama_lengkap', 
                    email = '$email', 
                    role = '$role', 
                    status = '$status', 
                    updated_at = NOW() 
                    WHERE id = '{$user['id']}'";
            }
            $success_msg = "Pengguna berhasil diupdate!";
        } else {
            // Insert new user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $query = "INSERT INTO users (username, nama_lengkap, email, role, status, password, created_at) 
                VALUES ('$username', '$nama_lengkap', '$email', '$role', '$status', '$hashed_password', NOW())";
            $success_msg = "Pengguna berhasil ditambahkan!";
        }
        
        if (mysqli_query($conn, $query)) {
            $_SESSION['success'] = $success_msg;
            header('Location: users.php');
            exit();
        } else {
            $errors[] = "Error: " . mysqli_error($conn);
        }
    }
}

$page_title = $edit_mode ? "Edit Pengguna" : "Tambah Pengguna";
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
        
        <div class="card">
            <div class="card-header">
                <h3><?php echo $edit_mode ? "Edit Data Pengguna" : "Tambah Data Pengguna"; ?></h3>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Username *</label>
                            <input type="text" name="username" class="form-control" 
                                   value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ($user ? htmlspecialchars($user['username']) : ''); ?>" 
                                   required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Nama Lengkap *</label>
                            <input type="text" name="nama_lengkap" class="form-control" 
                                   value="<?php echo isset($_POST['nama_lengkap']) ? htmlspecialchars($_POST['nama_lengkap']) : ($user ? htmlspecialchars($user['nama_lengkap']) : ''); ?>" 
                                   required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Email *</label>
                            <input type="email" name="email" class="form-control" 
                                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ($user ? htmlspecialchars($user['email']) : ''); ?>" 
                                   required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Role *</label>
                            <select name="role" class="form-control" required>
                                <option value="admin" <?php echo (isset($_POST['role']) && $_POST['role'] == 'admin') || ($user && $user['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                                <option value="operator" <?php echo (isset($_POST['role']) && $_POST['role'] == 'operator') || ($user && $user['role'] == 'operator') ? 'selected' : ''; ?>>Operator</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Status *</label>
                            <select name="status" class="form-control" required>
                                <option value="aktif" <?php echo (isset($_POST['status']) && $_POST['status'] == 'aktif') || ($user && $user['status'] == 'aktif') ? 'selected' : ''; ?>>Aktif</option>
                                <option value="nonaktif" <?php echo (isset($_POST['status']) && $_POST['status'] == 'nonaktif') || ($user && $user['status'] == 'nonaktif') ? 'selected' : ''; ?>>Nonaktif</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label"><?php echo $edit_mode ? "Password Baru (kosongkan jika tidak diubah)" : "Password *"; ?></label>
                            <input type="password" name="password" class="form-control" 
                                   <?php echo $edit_mode ? '' : 'required'; ?>>
                            <?php if ($edit_mode): ?>
                                <small class="text-muted">Kosongkan jika tidak ingin mengubah password</small>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label"><?php echo $edit_mode ? "Konfirmasi Password Baru" : "Konfirmasi Password *"; ?></label>
                            <input type="password" name="confirm_password" class="form-control" 
                                   <?php echo $edit_mode ? '' : 'required'; ?>>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> <?php echo $edit_mode ? "Update Pengguna" : "Simpan Pengguna"; ?>
                        </button>
                        <a href="users.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
