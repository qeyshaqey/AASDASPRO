<?php
// Memanggil file konfigurasi untuk koneksi database
require_once 'config.php';

// Query untuk memperbarui status rapat berdasarkan waktu saat ini
// - Jika status sudah "selesai", tidak diubah
// - Jika waktu rapat sudah lewat, status menjadi "berlangsung"
// - Jika belum dimulai, status menjadi "akan datang"
$sql = "UPDATE agendas SET status = 
        CASE 
            WHEN status = 'selesai' THEN 'selesai'
            WHEN CONCAT(tanggal, ' ', waktu) < NOW() THEN 'berlangsung'
            ELSE 'akan datang'
        END
        WHERE status != 'selesai'";

// Mengeksekusi query update status
if ($conn->query($sql)) {
    // Response JSON jika update berhasil
    echo json_encode([
        'success' => true,
        'message' => 'Status rapat berhasil diperbarui'
    ]);
} else {
    // Response JSON jika update gagal
    echo json_encode([
        'success' => false,
        'message' => 'Gagal memperbarui status: ' . $conn->error
    ]);
}

// Menutup koneksi database
$conn->close();
?>
