<?php
// Menentukan response berupa JSON
header("Content-Type: application/json");

// Mencegah browser melakukan cache response
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

// Fungsi helper untuk mengirim response JSON secara konsisten
function send_json_response($success, $message, $data = null) {
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    // Menghentikan eksekusi script setelah response dikirim
    exit();
}

// Fungsi untuk mendapatkan koneksi database (singleton connection)
function get_connection() {
    static $conn = null; // Menyimpan koneksi agar tidak dibuat berulang
    
    if ($conn === null) {
        // Memuat file konfigurasi database
        $conn = include 'config.php';
        
        // Validasi koneksi database
        if (!$conn) {
            send_json_response(false, 'Database connection failed.');
        }
    }
    return $conn;
}

// Mengambil koneksi database
$conn = get_connection();

// Mengambil parameter filter dari request POST
$jurusanFilter = $_POST['filterJurusan'] ?? '';
$statusFilter  = $_POST['filterStatus'] ?? '';
$tanggalFilter = $_POST['filterTanggal'] ?? '';

// Query dasar untuk mengambil data agenda
$sql = "SELECT id, judul_rapat, jurusan, tanggal, waktu, status, lokasi, tipe_tempat 
        FROM agendas WHERE 1=1";

// Array untuk menyimpan parameter query
$params = [];
$types  = '';

// Filter berdasarkan jurusan (jika ada)
if (!empty($jurusanFilter)) {
    $sql .= " AND jurusan = ?";
    $params[] = &$jurusanFilter;
    $types   .= 's';
}

// Filter berdasarkan status agenda (jika ada)
if (!empty($statusFilter)) {
    $sql .= " AND status = ?";
    $params[] = &$statusFilter;
    $types   .= 's';
}

// Filter berdasarkan tanggal rapat (jika ada)
if (!empty($tanggalFilter)) {
    $sql .= " AND tanggal = ?";
    $params[] = &$tanggalFilter;
    $types   .= 's';
}

// Pengurutan data berdasarkan prioritas status dan waktu
$sql .= " ORDER BY 
        CASE 
            WHEN status = 'akan datang' THEN 1
            WHEN status = 'berlangsung' THEN 2
            WHEN status = 'selesai' THEN 3
            ELSE 4
        END, 
        tanggal ASC, waktu ASC";

// Menyiapkan query menggunakan prepared statement
$stmt = $conn->prepare($sql);

// Binding parameter jika filter digunakan
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

// Menjalankan query
$stmt->execute();

// Mengambil hasil query
$result = $stmt->get_result();

// Validasi hasil query
if (!$result) {
    send_json_response(false, 'Query execution failed: ' . $conn->error);
}

// Menyimpan seluruh data agenda ke dalam array
$allAgendas = [];
while ($row = $result->fetch_assoc()) {
    $allAgendas[] = $row;
}

// Menutup koneksi database
$conn->close();

// Mengirim response sukses beserta data agenda
send_json_response(true, 'Data retrieved successfully.', $allAgendas);
