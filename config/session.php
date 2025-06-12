<?php
session_start();

// Session timeout (30 minutes)
$session_timeout = 1800; // 30 minutes in seconds

// Check if session is active and not expired
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $session_timeout)) {
    // Session expired
    session_unset();
    session_destroy();
    header("Location: login.php?expired=1");
    exit();
}

// Update last activity time
$_SESSION['last_activity'] = time();

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check if user is admin
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] == 'admin';
}

// Require login to access page
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}

// Require admin role to access page
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header("Location: index.php?error=unauthorized");
        exit();
    }
}

// Logout function
function logout() {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}
?>
