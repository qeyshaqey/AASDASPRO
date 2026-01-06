<?php
error_reporting(0);
ini_set('display_errors', 0);

ob_start();

header('Content-Type: application/json; charset=utf-8');

function sendResponse($success, $message, $data = null) {
    ob_clean();
    
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ], JSON_UNESCAPED_UNICODE);
    
    ob_end_flush();
    exit();
}

try {
    require_once 'config.php';
    
    if (!$conn || $conn->connect_error) {
        sendResponse(false, 'Koneksi database gagal: ' . ($conn ? $conn->connect_error : 'Connection is null'));
    }
} catch (Exception $e) {
    sendResponse(false, 'Error loading config: ' . $e->getMessage());
}

$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');

if (empty($action)) {
    sendResponse(false, 'Action tidak ditemukan');
}

// === AKSI TAMBAH ===
if ($action === 'tambah') {
    try {
        $required = ['judul_rapat', 'jurusan', 'tanggal', 'waktu', 'host', 'tipe_tempat'];
        foreach ($required as $field) {
            if (!isset($_POST[$field]) || empty($_POST[$field])) {
                sendResponse(false, "Field '$field' wajib diisi!");
            }
        }
        
        $judul = trim($_POST['judul_rapat']);
        $jurusan = trim($_POST['jurusan']);
        $tanggal = trim($_POST['tanggal']);
        $waktu = trim($_POST['waktu']);
        $host = trim($_POST['host']);
        $tipeTempat = trim($_POST['tipe_tempat']);
        
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
        
        // Ambil peserta
        $userIds = isset($_POST['peserta_ids']) ? $_POST['peserta_ids'] : [];
        
        $datetime = $tanggal . ' ' . $waktu;
        $status = (strtotime($datetime) <= time()) ? 'berlangsung' : 'akan datang';
        
        $conn->begin_transaction();
        
        $sql = "INSERT INTO agendas (judul_rapat, jurusan, tanggal, waktu, lokasi, tipe_tempat, host, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $conn->error);
        }
        
        $stmt->bind_param("ssssssss", $judul, $jurusan, $tanggal, $waktu, $lokasi, $tipeTempat, $host, $status);
        
        if (!$stmt->execute()) {
            throw new Exception('Execute failed: ' . $stmt->error);
        }
        
        $agendaId = $conn->insert_id;
        $stmt->close();
        
        // Insert peserta jika ada
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
        
        $conn->commit();
        sendResponse(true, 'Agenda rapat berhasil ditambahkan!', ['id' => $agendaId]);
        
    } catch (Exception $e) {
        if (isset($conn)) {
            $conn->rollback();
        }
        sendResponse(false, 'Error: ' . $e->getMessage());
    }
}

// === AKSI DETAIL ===
elseif ($action === 'detail') {
    try {
        if (!isset($_GET['id'])) {
            sendResponse(false, 'ID tidak ditemukan');
        }
        
        $agendaId = intval($_GET['id']);
        
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
        
        $agendaData['peserta_detail'] = $pesertaList;
        
        sendResponse(true, 'Data ditemukan', $agendaData);
        
    } catch (Exception $e) {
        sendResponse(false, 'Error: ' . $e->getMessage());
    }
}

// === AKSI EDIT ===
elseif ($action === 'edit') {
    try {
        $required = ['id', 'judul_rapat', 'jurusan', 'tanggal', 'waktu', 'host', 'tempat'];
        foreach ($required as $field) {
            if (!isset($_POST[$field]) || empty($_POST[$field])) {
                sendResponse(false, "Field '$field' wajib diisi!");
            }
        }
        
        $agendaId = intval($_POST['id']);
        $judul = trim($_POST['judul_rapat']);
        $jurusan = trim($_POST['jurusan']);
        $tanggal = trim($_POST['tanggal']);
        $waktu = trim($_POST['waktu']);
        $host = trim($_POST['host']);
        $tempat = trim($_POST['tempat']);
        $pesertaStatus = isset($_POST['peserta_status']) ? json_decode($_POST['peserta_status'], true) : [];
        
        $conn->begin_transaction();
        
        // Update agenda
        $sql = "UPDATE agendas SET judul_rapat=?, jurusan=?, tanggal=?, waktu=?, lokasi=?, host=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssi", $judul, $jurusan, $tanggal, $waktu, $tempat, $host, $agendaId);
        $stmt->execute();
        $stmt->close();
        
        // Update status kehadiran
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
            
            // Jika semua sudah diabsen, ubah status jadi selesai
            if ($allAbsent) {
                $sqlStatus = "UPDATE agendas SET status = 'selesai' WHERE id = ?";
                $stmtStatus = $conn->prepare($sqlStatus);
                $stmtStatus->bind_param("i", $agendaId);
                $stmtStatus->execute();
                $stmtStatus->close();
            }
        }
        
        $conn->commit();
        sendResponse(true, 'Perubahan berhasil disimpan!');
        
    } catch (Exception $e) {
        if (isset($conn)) {
            $conn->rollback();
        }
        sendResponse(false, 'Error: ' . $e->getMessage());
    }
}

// === AKSI HAPUS ===
elseif ($action === 'hapus') {
    try {
        if (!isset($_POST['id'])) {
            sendResponse(false, 'ID tidak ditemukan');
        }
        
        $agendaId = intval($_POST['id']);
        
        $conn->begin_transaction();
        
        // Hapus peserta
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
        
        $conn->commit();
        sendResponse(true, 'Agenda berhasil dihapus!');
        
    } catch (Exception $e) {
        if (isset($conn)) {
            $conn->rollback();
        }
        sendResponse(false, 'Error: ' . $e->getMessage());
    }
}

else {
    sendResponse(false, 'Action tidak valid: ' . $action);
}

$conn->close();
?>