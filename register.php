<?php
require_once 'config/session.php';
require_once 'config/database.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

$error = '';
$success = '';

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $nama_lengkap = mysqli_real_escape_string($conn, trim($_POST['nama_lengkap']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validation
    $errors = [];
    
    if (empty($username)) {
        $errors[] = "Username harus diisi!";
    } elseif (strlen($username) < 3) {
        $errors[] = "Username minimal 3 karakter!";
    }
    
    if (empty($nama_lengkap)) {
        $errors[] = "Nama lengkap harus diisi!";
    }
    
    if (empty($email)) {
        $errors[] = "Email harus diisi!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format email tidak valid!";
    }
    
    if (empty($password)) {
        $errors[] = "Password harus diisi!";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password minimal 6 karakter!";
    }
    
    if ($password !== $confirm_password) {
        $errors[] = "Konfirmasi password tidak cocok!";
    }
    
    // Check duplicate username
    $check_username = mysqli_query($conn, "SELECT id FROM users WHERE username = '$username'");
    if ($check_username && mysqli_num_rows($check_username) > 0) {
        $errors[] = "Username sudah digunakan!";
    }
    
    // Check duplicate email
    $check_email = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email'");
    if ($check_email && mysqli_num_rows($check_email) > 0) {
        $errors[] = "Email sudah digunakan!";
    }
    
    if (empty($errors)) {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert new user (default role: operator)
        $query = "INSERT INTO users (username, nama_lengkap, email, password, role, status, created_at) 
                  VALUES ('$username', '$nama_lengkap', '$email', '$hashed_password', 'operator', 'aktif', NOW())";
        
        if (mysqli_query($conn, $query)) {
            $success = "Registrasi berhasil! Silakan login dengan akun Anda.";
            // Clear form data
            $username = $nama_lengkap = $email = '';
        } else {
            $errors[] = "Error: " . mysqli_error($conn);
        }
    }
    
    if (!empty($errors)) {
        $error = implode('<br>', $errors);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - BuildVent</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1><i class="fas fa-boxes"></i> BuildVent</h1>
                <p>Daftar Akun Baru</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <div class="input-group">
                        <span class="input-icon"><i class="fas fa-user"></i></span>
                        <input type="text" name="username" class="form-control" 
                               value="<?php echo htmlspecialchars($username ?? ''); ?>" 
                               placeholder="Masukkan username" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Nama Lengkap</label>
                    <div class="input-group">
                        <span class="input-icon"><i class="fas fa-id-card"></i></span>
                        <input type="text" name="nama_lengkap" class="form-control" 
                               value="<?php echo htmlspecialchars($nama_lengkap ?? ''); ?>" 
                               placeholder="Masukkan nama lengkap" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <div class="input-group">
                        <span class="input-icon"><i class="fas fa-envelope"></i></span>
                        <input type="email" name="email" class="form-control" 
                               value="<?php echo htmlspecialchars($email ?? ''); ?>" 
                               placeholder="Masukkan email" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-icon"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password" class="form-control" 
                               placeholder="Masukkan password (min. 6 karakter)" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Konfirmasi Password</label>
                    <div class="input-group">
                        <span class="input-icon"><i class="fas fa-lock"></i></span>
                        <input type="password" name="confirm_password" class="form-control" 
                               placeholder="Ulangi password" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-user-plus"></i> Daftar
                    </button>
                </div>
            </form>
            
            <div class="text-center mt-3">
                <p class="text-muted">
                    Sudah punya akun? 
                    <a href="login.php" style="color: #667eea; text-decoration: none;">
                        <strong>Login di sini</strong>
                    </a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
