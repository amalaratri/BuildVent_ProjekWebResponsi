<?php
require_once 'config/session.php';
require_once 'config/database.php';

// Redirect to login if not logged in
requireLogin();

// Redirect to dashboard.php
header('Location: dashboard.php');
exit();
?>
