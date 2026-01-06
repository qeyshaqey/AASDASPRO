<?php
header("Content-Type: application/json");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

function send_json_response($success, $message, $data = null) {
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit();
}

function get_connection() {
    static $conn = null;
    if ($conn === null) {
        $conn = include 'config.php';
        if (!$conn) {
            send_json_response(false, 'Database connection failed.');
        }
    }
    return $conn;
}

 $conn = get_connection();
 $jurusanFilter = $_POST['filterJurusan'] ?? '';
 $statusFilter = $_POST['filterStatus'] ?? '';
 $tanggalFilter = $_POST['filterTanggal'] ?? '';

 $sql = "SELECT id, judul_rapat, jurusan, tanggal, waktu, status, lokasi, tipe_tempat 
        FROM agendas WHERE 1=1";

 $params = [];
 $types = '';

if (!empty($jurusanFilter)) {
    $sql .= " AND jurusan = ?";
    $params[] = &$jurusanFilter;
    $types .= 's';
}

if (!empty($statusFilter)) {
    $sql .= " AND status = ?";
    $params[] = &$statusFilter;
    $types .= 's';
}

if (!empty($tanggalFilter)) {
    $sql .= " AND tanggal = ?";
    $params[] = &$tanggalFilter;
    $types .= 's';
}

 $sql .= " ORDER BY 
        CASE 
            WHEN status = 'akan datang' THEN 1
            WHEN status = 'berlangsung' THEN 2
            WHEN status = 'selesai' THEN 3
            ELSE 4
        END, 
        tanggal ASC, waktu ASC";

 $stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

 $stmt->execute();
 $result = $stmt->get_result();

if (!$result) {
    send_json_response(false, 'Query execution failed: ' . $conn->error);
}

 $allAgendas = [];
while ($row = $result->fetch_assoc()) {
    $allAgendas[] = $row;
}

 $conn->close();

send_json_response(true, 'Data retrieved successfully.', $allAgendas);