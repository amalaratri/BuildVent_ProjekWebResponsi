<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "buildvent";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Helper functions
function formatRupiah($angka) {
    return "Rp " . number_format($angka, 0, ',', '.');
}

function formatTanggal($tanggal) {
    return date('d/m/Y H:i', strtotime($tanggal));
}

function generateKode($prefix, $table, $field) {
    global $conn;
    $query = "SELECT MAX(CAST(SUBSTRING($field, " . (strlen($prefix) + 1) . ") AS UNSIGNED)) as max_num FROM $table WHERE $field LIKE '$prefix%'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    $next_num = ($row['max_num'] ?? 0) + 1;
    return $prefix . str_pad($next_num, 3, '0', STR_PAD_LEFT);
}
?>
