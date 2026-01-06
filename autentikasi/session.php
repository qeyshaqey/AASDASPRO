<?php
session_start();

// Regenerasi ID sesi
if (!isset($_SESSION['initiated'])) {
    session_regenerate_id();
    $_SESSION['initiated'] = true;
}

// Atur waktu kedaluwarsa sesi (30 menit)
 $timeout = 1800; 

//  Periksa aktivitas terakhir
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
    session_unset();
    session_destroy();
    header('Location: login.php?timeout=1');
    exit();
}
// Perbarui waktu aktivitas terakhir
 $_SESSION['last_activity'] = time();
?>