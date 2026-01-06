<?php
// Menentukan response dalam format JSON
header('Content-Type: application/json');

// Memuat konfigurasi dan koneksi database
require_once 'config.php';

// Mengambil parameter jurusan dari request GET (jika ada)
$jurusan = isset($_GET['jurusan']) ? $_GET['jurusan'] : '';

// Query dasar untuk mengambil data user dengan role "user"
$sql = "SELECT id, full_name as nama FROM users WHERE role = 'user'";

// Menambahkan filter jurusan jika parameter dikirim
if (!empty($jurusan)) {
    $sql .= " AND jurusan = ?";
}

// Menyiapkan query menggunakan prepared statement
$stmt = $conn->prepare($sql);

// Binding parameter jurusan jika filter digunakan
if (!empty($jurusan)) {
    $stmt->bind_param("s", $jurusan);
}

// Menjalankan query
$stmt->execute();

// Mengambil hasil query
$result = $stmt->get_result();

// Menyimpan data peserta ke dalam array
$peserta = [];
while ($row = $result->fetch_assoc()) {
    $peserta[] = $row;
}

// Mengirim data peserta dalam format JSON
echo json_encode($peserta);

// Menutup koneksi database
$conn->close();
?>
