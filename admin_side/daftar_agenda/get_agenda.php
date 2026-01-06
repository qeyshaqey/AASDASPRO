<?php
header('Content-Type: application/json');
require_once 'config.php';

if ($conn->connect_error) {
    echo json_encode(['error' => 'Koneksi database gagal: ' . $conn->connect_error]);
    exit();
}

 $draw = isset($_POST['draw']) ? intval($_POST['draw']) : 1;
 $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
 $length = isset($_POST['length']) ? intval($_POST['length']) : 10;
 $searchValue = isset($_POST['search']['value']) ? $conn->real_escape_string($_POST['search']['value']) : '';
 $orderColumn = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 0;
 $orderDir = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'asc';
 $filterJurusan = isset($_POST['filterJurusan']) ? $conn->real_escape_string($_POST['filterJurusan']) : '';

 $columns = ['id', 'judul_rapat', 'jurusan', 'tanggal', 'waktu', 'lokasi', 'host'];

 $sql = "SELECT SQL_CALC_FOUND_ROWS a.id, a.judul_rapat, a.jurusan, a.tanggal, a.waktu, a.tipe_tempat, a.lokasi, a.host 
        FROM agendas a WHERE 1=1";

if (!empty($searchValue)) {
    $sql .= " AND (a.judul_rapat LIKE '%$searchValue%' OR a.host LIKE '%$searchValue%' OR a.lokasi LIKE '%$searchValue%')";
}

if (!empty($filterJurusan)) {
    $sql .= " AND a.jurusan = '$filterJurusan'";
}

if (isset($columns[$orderColumn])) {
    $sql .= " ORDER BY " . $columns[$orderColumn] . " " . $orderDir;
}

 $sql .= " LIMIT $start, $length";

 $result = $conn->query($sql);
if (!$result) {
    echo json_encode(['error' => 'Query SQL gagal: ' . $conn->error]);
    exit();
}

 $agendaIds = [];
while ($row = $result->fetch_assoc()) {
    $agendaIds[] = $row['id'];
}

 $participantsData = [];
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

    while ($p = $pesertaResult->fetch_assoc()) {
        $participantsData[$p['agenda_id']][] = $p;
    }
}

 $result->data_seek(0);

 $data = [];
while ($row = $result->fetch_assoc()) {
    $agendaId = $row['id'];
    
    $pesertaBadges = [];
    if (isset($participantsData[$agendaId])) {
        foreach ($participantsData[$agendaId] as $peserta) {
            $nama = htmlspecialchars($peserta['full_name']);
            $status = $peserta['status_kehadiran'];
            
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

    $tempatHTML = '';
    if ($row['tipe_tempat'] === 'online') {
        $tempatHTML = "<a href='{$row['lokasi']}' target='_blank' class='text-primary text-decoration-underline'>Link Meeting</a>";
    } else {
        $tempatHTML = htmlspecialchars($row['lokasi']);
    }

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

 $totalFilteredResult = $conn->query("SELECT FOUND_ROWS()");
 $totalFiltered = $totalFilteredResult->fetch_row()[0];

 $totalDataResult = $conn->query("SELECT COUNT(*) FROM agendas");
 $totalData = $totalDataResult->fetch_row()[0];

 $response = [
    "draw" => intval($draw),
    "recordsTotal" => intval($totalData),
    "recordsFiltered" => intval($totalFiltered),
    "data" => $data
];

echo json_encode($response);

 $conn->close();
?>