<?php
// Menentukan response dalam format JSON
header('Content-Type: application/json');

// Memuat konfigurasi dan koneksi database
require_once 'config.php';

// Mengecek apakah koneksi database gagal
if ($conn->connect_error) {
    echo json_encode(['error' => 'Koneksi database gagal: ' . $conn->connect_error]);
    exit();
}

// Parameter DataTables untuk pagination dan pencarian
$draw  = isset($_POST['draw']) ? intval($_POST['draw']) : 1;
$start = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;

// Nilai pencarian global
$searchValue = isset($_POST['search']['value']) 
    ? $conn->real_escape_string($_POST['search']['value']) 
    : '';

// Parameter sorting DataTables
$orderColumn = isset($_POST['order'][0]['column']) 
    ? intval($_POST['order'][0]['column']) 
    : 0;
$orderDir = isset($_POST['order'][0]['dir']) 
    ? $_POST['order'][0]['dir'] 
    : 'asc';

// Filter berdasarkan jurusan
$filterJurusan = isset($_POST['filterJurusan']) 
    ? $conn->real_escape_string($_POST['filterJurusan']) 
    : '';

// Daftar kolom yang bisa diurutkan
$columns = ['id', 'judul_rapat', 'jurusan', 'tanggal', 'waktu', 'lokasi', 'host'];

// Query dasar untuk mengambil data agenda
$sql = "SELECT SQL_CALC_FOUND_ROWS a.id, a.judul_rapat, a.jurusan, a.tanggal, a.waktu, a.tipe_tempat, a.lokasi, a.host 
        FROM agendas a WHERE 1=1";

// Pencarian berdasarkan keyword
if (!empty($searchValue)) {
    $sql .= " AND (a.judul_rapat LIKE '%$searchValue%' 
              OR a.host LIKE '%$searchValue%' 
              OR a.lokasi LIKE '%$searchValue%')";
}

// Filter jurusan jika dipilih
if (!empty($filterJurusan)) {
    $sql .= " AND a.jurusan = '$filterJurusan'";
}

// Pengurutan data sesuai permintaan DataTables
if (isset($columns[$orderColumn])) {
    $sql .= " ORDER BY " . $columns[$orderColumn] . " " . $orderDir;
}

// Pagination data
$sql .= " LIMIT $start, $length";

// Menjalankan query utama
$result = $conn->query($sql);

// Validasi query
if (!$result) {
    echo json_encode(['error' => 'Query SQL gagal: ' . $conn->error]);
    exit();
}

// Mengumpulkan ID agenda untuk mengambil data peserta
$agendaIds = [];
while ($row = $result->fetch_assoc()) {
    $agendaIds[] = $row['id'];
}

// Array untuk menyimpan data peserta per agenda
$participantsData = [];

// Mengambil data peserta jika agenda tersedia
if (!empty($agendaIds)) {
    $idsPlaceholder = implode(',', array_fill(0, count($agendaIds), '?'));
    $types = str_repeat('i', count($agendaIds));

    $pesertaQuery = "SELECT ap.agenda_id, u.full_name, ap.status_kehadiran 
                      FROM agenda_participants ap
                      JOIN users u ON ap.user_id = u.id
                      WHERE ap.agenda_id IN ($idsPlaceholder)";
    
    $stmtPeserta = $conn->prepare($pesertaQuery);
    $stmtPeserta->bind_param($types, ...$agendaIds);
    $stmtPeserta->execute();
    $pesertaResult = $stmtPeserta->get_result();

    // Mengelompokkan peserta berdasarkan agenda
    while ($p = $pesertaResult->fetch_assoc()) {
        $participantsData[$p['agenda_id']][] = $p;
    }
}

// Mengembalikan pointer hasil query ke awal
$result->data_seek(0);

// Menyiapkan data akhir untuk DataTables
$data = [];
while ($row = $result->fetch_assoc()) {
    $agendaId = $row['id'];
    
    // Membuat badge peserta
    $pesertaBadges = [];
    if (isset($participantsData[$agendaId])) {
        foreach ($participantsData[$agendaId] as $peserta) {
            $nama = htmlspecialchars($peserta['full_name']);
            $status = $peserta['status_kehadiran'];
            
            // Menentukan warna badge berdasarkan status kehadiran
            $badgeClass = 'bg-secondary';
            if ($status === 'hadir') {
                $badgeClass = 'bg-success';
            } elseif ($status === 'tidak_hadir') {
                $badgeClass = 'bg-danger';
            }
            $pesertaBadges[] = "<span class=\"badge {$badgeClass} me-1\">{$nama}</span>";
        }
    }
    $pesertaHTML = implode('', $pesertaBadges);

    // Menentukan tampilan tempat rapat (online / offline)
    $tempatHTML = '';
    if ($row['tipe_tempat'] === 'online') {
        $tempatHTML = "<a href='{$row['lokasi']}' target='_blank' class='text-primary text-decoration-underline'>Link Meeting</a>";
    } else {
        $tempatHTML = htmlspecialchars($row['lokasi']);
    }

    // Menyusun data per baris untuk DataTables
    $data[] = [
        'DT_RowId' => 'row-' . $row['id'],
        'id' => $row['id'],
        'judul_rapat' => htmlspecialchars($row['judul_rapat']),
        'jurusan' => htmlspecialchars($row['jurusan']),
        'tanggal' => htmlspecialchars($row['tanggal']),
        'waktu' => htmlspecialchars($row['waktu']),
        'tempat' => $tempatHTML,
        'host' => htmlspecialchars($row['host']),
        'peserta' => $pesertaHTML
    ];
}

// Mengambil jumlah data setelah filter
$totalFilteredResult = $conn->query("SELECT FOUND_ROWS()");
$totalFiltered = $totalFilteredResult->fetch_row()[0];

// Mengambil total seluruh data agenda
$totalDataResult = $conn->query("SELECT COUNT(*) FROM agendas");
$totalData = $totalDataResult->fetch_row()[0];

// Menyusun response untuk DataTables
$response = [
    "draw" => intval($draw),
    "recordsTotal" => intval($totalData),
    "recordsFiltered" => intval($totalFiltered),
    "data" => $data
];

// Mengirim response JSON
echo json_encode($response);

// Menutup koneksi database
$conn->close();
?>
