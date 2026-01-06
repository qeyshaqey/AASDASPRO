<?php
// Menonaktifkan tampilan error ke browser (keamanan)
error_reporting(0);
ini_set('display_errors', 0);

// Mengaktifkan output buffering
ob_start();

// Menentukan response berupa JSON
header('Content-Type: application/json; charset=utf-8');

// Fungsi helper untuk mengirim response JSON secara konsisten
function sendResponse($success, $message, $data = null) {
    // Membersihkan buffer sebelum output
    ob_clean();
    
    // Mengirim response dalam format JSON
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ], JSON_UNESCAPED_UNICODE);
    
    // Mengakhiri output buffer dan menghentikan eksekusi script
    ob_end_flush();
    exit();
}

try {
    // Memuat konfigurasi database
    require_once 'config.php';
    
    // Validasi koneksi database
    if (!$conn || $conn->connect_error) {
        sendResponse(false, 'Koneksi database gagal: ' . ($conn ? $conn->connect_error : 'Connection is null'));
    }
} catch (Exception $e) {
    // Menangani error saat memuat config
    sendResponse(false, 'Error loading config: ' . $e->getMessage());
}

// Mengambil parameter action dari POST atau GET
$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');

// Validasi action
if (empty($action)) {
    sendResponse(false, 'Action tidak ditemukan');
}

// =======================
// AKSI TAMBAH AGENDA
// =======================
if ($action === 'tambah') {
    try {
        // Validasi field wajib
        $required = ['judul_rapat', 'jurusan', 'tanggal', 'waktu', 'host', 'tipe_tempat'];
        foreach ($required as $field) {
            if (!isset($_POST[$field]) || empty($_POST[$field])) {
                sendResponse(false, "Field '$field' wajib diisi!");
            }
        }
        
        // Sanitasi input
        $judul = trim($_POST['judul_rapat']);
        $jurusan = trim($_POST['jurusan']);
        $tanggal = trim($_POST['tanggal']);
        $waktu = trim($_POST['waktu']);
        $host = trim($_POST['host']);
        $tipeTempat = trim($_POST['tipe_tempat']);
        
        // Percabangan berdasarkan tipe rapat (online / offline)
        if ($tipeTempat === 'online') {
            if (!isset($_POST['zoom_link']) || empty($_POST['zoom_link'])) {
                sendResponse(false, 'Link Zoom wajib diisi untuk rapat online!');
            }
            $lokasi = trim($_POST['zoom_link']);
        } else {
            if (!isset($_POST['tempat_offline']) || empty($_POST['tempat_offline'])) {
                sendResponse(false, 'Tempat rapat wajib diisi untuk rapat offline!');
            }
            $lokasi = trim($_POST['tempat_offline']);
        }
        
        // Mengambil daftar peserta
        $userIds = isset($_POST['peserta_ids']) ? $_POST['peserta_ids'] : [];
        
        // Menentukan status agenda berdasarkan waktu
        $datetime = $tanggal . ' ' . $waktu;
        $status = (strtotime($datetime) <= time()) ? 'berlangsung' : 'akan datang';
        
        // Memulai transaksi database
        $conn->begin_transaction();
        
        // Query insert agenda
        $sql = "INSERT INTO agendas (judul_rapat, jurusan, tanggal, waktu, lokasi, tipe_tempat, host, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $conn->error);
        }
        
        // Binding parameter
        $stmt->bind_param("ssssssss", $judul, $jurusan, $tanggal, $waktu, $lokasi, $tipeTempat, $host, $status);
        
        if (!$stmt->execute()) {
            throw new Exception('Execute failed: ' . $stmt->error);
        }
        
        // Mengambil ID agenda yang baru dibuat
        $agendaId = $conn->insert_id;
        $stmt->close();
        
        // Insert peserta agenda
        if (!empty($userIds) && is_array($userIds)) {
            $sqlPeserta = "INSERT INTO agenda_participants (agenda_id, user_id, status_kehadiran) VALUES (?, ?, 'belum_dikonfirmasi')";
            $stmtPeserta = $conn->prepare($sqlPeserta);
            
            if (!$stmtPeserta) {
                throw new Exception('Prepare participant failed: ' . $conn->error);
            }
            
            foreach ($userIds as $userId) {
                $userIdInt = intval($userId);
                $stmtPeserta->bind_param("ii", $agendaId, $userIdInt);
                
                if (!$stmtPeserta->execute()) {
                    throw new Exception('Execute participant failed: ' . $stmtPeserta->error);
                }
            }
            
            $stmtPeserta->close();
        }
        
        // Commit transaksi jika sukses
        $conn->commit();
        sendResponse(true, 'Agenda rapat berhasil ditambahkan!', ['id' => $agendaId]);
        
    } catch (Exception $e) {
        // Rollback jika terjadi error
        if (isset($conn)) {
            $conn->rollback();
        }
        sendResponse(false, 'Error: ' . $e->getMessage());
    }
}

// =======================
// AKSI DETAIL AGENDA
// =======================
elseif ($action === 'detail') {
    try {
        // Validasi ID agenda
        if (!isset($_GET['id'])) {
            sendResponse(false, 'ID tidak ditemukan');
        }
        
        $agendaId = intval($_GET['id']);
        
        // Query data agenda
        $sql = "SELECT * FROM agendas WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $agendaId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            sendResponse(false, 'Agenda tidak ditemukan');
        }
        
        $agendaData = $result->fetch_assoc();
        $stmt->close();
        
        // Query peserta agenda
        $sqlPeserta = "SELECT u.full_name as nama_peserta, ap.status_kehadiran 
                       FROM agenda_participants ap
                       JOIN users u ON ap.user_id = u.id
                       WHERE ap.agenda_id = ?";
        $stmtPeserta = $conn->prepare($sqlPeserta);
        $stmtPeserta->bind_param("i", $agendaId);
        $stmtPeserta->execute();
        $resultPeserta = $stmtPeserta->get_result();
        
        $pesertaList = [];
        while ($row = $resultPeserta->fetch_assoc()) {
            $pesertaList[] = $row;
        }
        $stmtPeserta->close();
        
        // Menggabungkan data agenda dan peserta
        $agendaData['peserta_detail'] = $pesertaList;
        
        sendResponse(true, 'Data ditemukan', $agendaData);
        
    } catch (Exception $e) {
        sendResponse(false, 'Error: ' . $e->getMessage());
    }
}

// =======================
// AKSI EDIT AGENDA
// =======================
elseif ($action === 'edit') {
    try {
        // Validasi field wajib
        $required = ['id', 'judul_rapat', 'jurusan', 'tanggal', 'waktu', 'host', 'tempat'];
        foreach ($required as $field) {
            if (!isset($_POST[$field]) || empty($_POST[$field])) {
                sendResponse(false, "Field '$field' wajib diisi!");
            }
        }
        
        // Mengambil dan membersihkan input
        $agendaId = intval($_POST['id']);
        $judul = trim($_POST['judul_rapat']);
        $jurusan = trim($_POST['jurusan']);
        $tanggal = trim($_POST['tanggal']);
        $waktu = trim($_POST['waktu']);
        $host = trim($_POST['host']);
        $tempat = trim($_POST['tempat']);
        $pesertaStatus = isset($_POST['peserta_status']) ? json_decode($_POST['peserta_status'], true) : [];
        
        // Memulai transaksi
        $conn->begin_transaction();
        
        // Update data agenda
        $sql = "UPDATE agendas SET judul_rapat=?, jurusan=?, tanggal=?, waktu=?, lokasi=?, host=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssi", $judul, $jurusan, $tanggal, $waktu, $tempat, $host, $agendaId);
        $stmt->execute();
        $stmt->close();
        
        // Update status kehadiran peserta
        if (!empty($pesertaStatus)) {
            $allAbsent = true;
            foreach ($pesertaStatus as $status) {
                if (!in_array($status, ['hadir', 'tidak_hadir'])) {
                    $allAbsent = false;
                    break;
                }
            }
            
            $sqlPeserta = "UPDATE agenda_participants ap 
                           JOIN users u ON ap.user_id = u.id 
                           SET ap.status_kehadiran = ? 
                           WHERE ap.agenda_id = ? AND u.full_name = ?";
            $stmtPeserta = $conn->prepare($sqlPeserta);
            
            foreach ($pesertaStatus as $nama => $status) {
                if (in_array($status, ['hadir', 'tidak_hadir'])) {
                    $stmtPeserta->bind_param("sis", $status, $agendaId, $nama);
                    $stmtPeserta->execute();
                }
            }
            $stmtPeserta->close();
            
            // Jika semua peserta sudah diabsen, ubah status agenda menjadi selesai
            if ($allAbsent) {
                $sqlStatus = "UPDATE agendas SET status = 'selesai' WHERE id = ?";
                $stmtStatus = $conn->prepare($sqlStatus);
                $stmtStatus->bind_param("i", $agendaId);
                $stmtStatus->execute();
                $stmtStatus->close();
            }
        }
        
        // Commit transaksi
        $conn->commit();
        sendResponse(true, 'Perubahan berhasil disimpan!');
        
    } catch (Exception $e) {
        // Rollback jika error
        if (isset($conn)) {
            $conn->rollback();
        }
        sendResponse(false, 'Error: ' . $e->getMessage());
    }
}

// =======================
// AKSI HAPUS AGENDA
// =======================
elseif ($action === 'hapus') {
    try {
        // Validasi ID
        if (!isset($_POST['id'])) {
            sendResponse(false, 'ID tidak ditemukan');
        }
        
        $agendaId = intval($_POST['id']);
        
        // Memulai transaksi
        $conn->begin_transaction();
        
        // Hapus peserta agenda
        $sql1 = "DELETE FROM agenda_participants WHERE agenda_id = ?";
        $stmt1 = $conn->prepare($sql1);
        $stmt1->bind_param("i", $agendaId);
        $stmt1->execute();
        $stmt1->close();
        
        // Hapus agenda
        $sql2 = "DELETE FROM agendas WHERE id = ?";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bind_param("i", $agendaId);
        $stmt2->execute();
        $stmt2->close();
        
        // Commit transaksi
        $conn->commit();
        sendResponse(true, 'Agenda berhasil dihapus!');
        
    } catch (Exception $e) {
        // Rollback jika gagal
        if (isset($conn)) {
            $conn->rollback();
        }
        sendResponse(false, 'Error: ' . $e->getMessage());
    }
}

// Jika action tidak dikenali
else {
    sendResponse(false, 'Action tidak valid: ' . $action);
}

// Menutup koneksi database
$conn->close();
?>
