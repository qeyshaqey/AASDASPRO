<?php
// Menonaktifkan semua error reporting agar tidak ditampilkan ke user
// Biasanya digunakan pada environment production
error_reporting(0);
ini_set('display_errors', 0);

// Konfigurasi koneksi database
$host = "localhost";   // Host database
$user = "root";        // Username database
$pass = "";            // Password database
$dbname = "db_rapat";  // Nama database yang digunakan

try {
    // Membuat koneksi ke database menggunakan mysqli
    $conn = new mysqli($host, $user, $pass, $dbname);
    
    // Mengecek apakah terjadi error saat koneksi
    if ($conn->connect_error) {
        // Menyimpan pesan error ke log server
        error_log("Database connection failed: " . $conn->connect_error);
        
        // Mengatur koneksi menjadi null jika gagal
        $conn = null;
    } else {
        // Mengatur charset untuk mendukung karakter UTF-8
        $conn->set_charset("utf8mb4");
    }
} catch (Exception $e) {
    // Menangani exception jika terjadi kesalahan saat koneksi database
    error_log("Database connection exception: " . $e->getMessage());
    
    // Mengatur koneksi menjadi null jika terjadi exception
    $conn = null;
}

// Mengembalikan objek koneksi database
return $conn;
?>
