<?php
header('Content-Type: application/json');
require_once '../daftar_agenda/config.php';

$sqlPeserta = "SELECT COUNT(*) as total FROM users WHERE role = 'user'";
$resultPeserta = $conn->query($sqlPeserta);
$totalPeserta = $resultPeserta->fetch_assoc()['total'];

$sqlAgenda = "SELECT COUNT(*) as total FROM agendas";
$resultAgenda = $conn->query($sqlAgenda);
$totalAgenda = $resultAgenda->fetch_assoc()['total'];

$sqlBerlangsung = "SELECT COUNT(*) as total FROM agendas WHERE status = 'berlangsung'";
$resultBerlangsung = $conn->query($sqlBerlangsung);
$rapatBerlangsung = $resultBerlangsung->fetch_assoc()['total'];

$sqlSelesai = "SELECT COUNT(*) as total FROM agendas WHERE status = 'selesai'";
$resultSelesai = $conn->query($sqlSelesai);
$rapatSelesai = $resultSelesai->fetch_assoc()['total'];

echo json_encode([
    'totalPeserta' => $totalPeserta,
    'totalAgenda' => $totalAgenda,
    'rapatBerlangsung' => $rapatBerlangsung,
    'rapatSelesai' => $rapatSelesai
]);

$conn->close();
?>