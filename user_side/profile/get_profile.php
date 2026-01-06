<?php
session_start();
include "koneksi.php";

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

// Dapatkan ID user dari sesi
 $id = $_SESSION['user_id'];

//  Ambil data user dari database
 $query = mysqli_query($koneksi, "SELECT * FROM users WHERE id='$id'");
 $user = mysqli_fetch_assoc($query);

echo json_encode([
    "nama"    => $user['full_name'],
    "username"=> $user['username'],
    "email"   => $user['email'],
    "jurusan" => $user['jurusan'],
    "prodi"   => $user['prodi'],
    "foto"    => $user['foto']
]);
?>