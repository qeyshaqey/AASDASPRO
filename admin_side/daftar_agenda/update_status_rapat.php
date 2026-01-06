<?php
require_once 'config.php';

 $sql = "UPDATE agendas SET status = 
        CASE 
            WHEN status = 'selesai' THEN 'selesai'
            WHEN CONCAT(tanggal, ' ', waktu) < NOW() THEN 'berlangsung'
            ELSE 'akan datang'
        END
        WHERE status != 'selesai'";

if ($conn->query($sql)) {
    echo json_encode(['success' => true, 'message' => 'Status rapat berhasil diperbarui']);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal memperbarui status: ' . $conn->error]);
}

 $conn->close();
?>