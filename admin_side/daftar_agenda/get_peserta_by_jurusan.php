<?php
header('Content-Type: application/json');
require_once 'config.php';

 $jurusan = isset($_GET['jurusan']) ? $_GET['jurusan'] : '';

 $sql = "SELECT id, full_name as nama FROM users WHERE role = 'user'";
if (!empty($jurusan)) {
    $sql .= " AND jurusan = ?";
}

 $stmt = $conn->prepare($sql);
if (!empty($jurusan)) {
    $stmt->bind_param("s", $jurusan);
}
 $stmt->execute();
 $result = $stmt->get_result();

 $peserta = [];
while ($row = $result->fetch_assoc()) {
    $peserta[] = $row;
}

echo json_encode($peserta);
 $conn->close();
?>