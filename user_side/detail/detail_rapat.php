<?php
require_once '../../autentikasi/session.php';
require_once '../../autentikasi/functions.php';
require_once '../profile/koneksi.php';

if (!is_logged_in() || is_admin()) {
    redirect('../../autentikasi/login.php');
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT a.id, a.judul_rapat, a.tanggal, a.waktu, a.lokasi, a.tipe_tempat, ap.status_kehadiran
        FROM agendas a
        JOIN agenda_participants ap ON a.id = ap.agenda_id
        WHERE ap.user_id = ?
        ORDER BY a.tanggal DESC, a.waktu DESC";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$totalHadir = 0;
$totalTidakHadir = 0;
$allData = [];

while ($row = $result->fetch_assoc()) {
    $allData[] = $row;
    if ($row['status_kehadiran'] === 'hadir') {
        $totalHadir++;
    } elseif ($row['status_kehadiran'] === 'tidak_hadir') {
        $totalTidakHadir++;
    }
}

$totalRapat = count($allData);
$persentase = $totalRapat > 0 ? round(($totalHadir / $totalRapat) * 100) : 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Detail Rapat Peserta - IF1A2 TaskMeet</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">

  <style>
    body {
      background: linear-gradient(180deg, #784B84, #D7BFDC);
      min-height: 100vh;
    }
    .sidebar {
      width: 240px;
      background: linear-gradient(180deg, #784B84, #D7BFDC);
      color: #fff;
      height: 100vh;
      position: fixed;
      padding-top: 30px;
      transition: 0.3s;
      z-index: 999;
      box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
    }
    .sidebar a {
      display: flex;
      align-items: center;
      padding: 12px 25px;
      color: #fff;
      text-decoration: none;
      transition: 0.3s;
    }
    .sidebar a:hover {
      background: rgba(255,255,255,0.2);
      border-radius: 3px;
      transform: translateX(4px);
    }
    .main-content {
      margin-left: 240px;
      padding: 50px 30px;
      transition: 0.3s;
    }
    .card-rail {
      background: #fff;
      border-radius: 10px;
      padding: 18px;
      box-shadow: 0 6px 18px rgba(18, 38, 63, .06);
    }
    .card-stat {
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    @media(max-width: 768px) {
      .sidebar {
        left: -240px;
      }
      .sidebar.active {
        left: 0;
      }
      .main-content {
        margin-left: 0;
      }
      .toggle-btn {
        position: fixed;
        top: 15px;
        left: 15px;
        z-index: 1000;
        background: #5b2c83;
        color: #fff;
        border: none;
        padding: 8px 12px;
        border-radius: 6px;
      }
    }
  </style>
</head>

<body>
  <div class="sidebar" id="sidebar">
    <div class="text-center mb-4">
      <img src="../../admin_side/format_gambar/logo.png" width="110" class="rounded-circle mb-2">
      <h5 class="fw-bold">TaskMeet</h5>
    </div>
    <a class="fw-bold" href="../dashboard_p/dashboard_pengguna.php"><i class="bi bi-house-door me-2"></i> Beranda</a>
    <a class="fw-bold" href="detail_rapat.php"><i class="bi bi-file-text me-2"></i>Detail Rapat</a>
    <a class="fw-bold" href="../profile/profil_pengguna.php"><i class="bi bi-person-circle me-2"></i> Profil</a>
    <div class="position-absolute bottom-0 w-100">
      <hr>
      <a class="fw-bold" href="../../autentikasi/logout.php"><i class="bi bi-box-arrow-right me-2"></i> Keluar</a>
    </div>
  </div>

  <button class="toggle-btn d-md-none" id="toggleBtn"><i class="bi bi-list"></i></button>

  <div class="main-content">
    <div class="row g-3">
      <div class="col-lg-12">
        <h3 class="fw-bold text-white"><i class="bi bi-file-text me-2"></i> Detail Rapat</h3>
        <!-- STATISTIK -->
        <div class="row g-3 mb-4">
          <div class="col-12 col-md-4">
            <div class="card card-stat p-3">
              <h6>Total Hadir</h6>
              <h4 class="fw-bold text-success"><?= $totalHadir ?></h4>
            </div>
          </div>
          <div class="col-12 col-md-4">
            <div class="card card-stat p-3">
              <h6>Total Tidak Hadir</h6>
              <h4 class="fw-bold text-danger"><?= $totalTidakHadir ?></h4>
            </div>
          </div>
          <div class="col-12 col-md-4">
            <div class="card card-stat p-3">
              <h6>Persentase Kehadiran</h6>
              <h4 class="fw-bold text-primary"><?= $persentase ?>%</h4>
            </div>
          </div>
        </div>
        <!-- TABEL RIWAYAT -->
        <div class="card-rail">
          <h4 class="fw-bold mb-4">Riwayat Rapat</h4>
          <div class="table-responsive">
            <table id="riwayatTable" class="table table-bordered bg-white table-hover">
              <thead class="table-light">
                <tr>
                  <th>No.</th>
                  <th>Judul Rapat</th>
                  <th>Tanggal</th>
                  <th>Waktu</th>
                  <th>Tempat/Platform</th>
                  <th>Status Kehadiran</th>
                </tr>
              </thead>
              <tbody>
                <?php 
                $no = 1;
                if (count($allData) == 0): 
                ?>
                  <tr>
                    <td colspan="6" class="text-center text-muted">Anda belum diundang ke rapat mana pun.</td>
                  </tr>
                <?php 
                else:
                  foreach ($allData as $row):
                    $status_badge = '';
                    if ($row['status_kehadiran'] === 'hadir') {
                      $status_badge = '<span class="badge bg-success">Hadir</span>';
                    } elseif ($row['status_kehadiran'] === 'tidak_hadir') {
                      $status_badge = '<span class="badge bg-danger">Tidak Hadir</span>';
                    } else {
                      $status_badge = '<span class="badge bg-secondary">Belum Dikonfirmasi</span>';
                    }                    
                    $tempat_display = htmlspecialchars($row['lokasi']);
                    if ($row['tipe_tempat'] === 'online') {
                      $tempat_display = '<a href="' . htmlspecialchars($row['lokasi']) . '" target="_blank" class="text-primary">Link Meeting</a>';
                    }
                ?>
                  <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($row['judul_rapat']) ?></td>
                    <td><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                    <td><?= substr($row['waktu'], 0, 5) ?></td>
                    <td><?= $tempat_display ?></td>
                    <td><?= $status_badge ?></td>
                  </tr>
                <?php 
                  endforeach;
                endif;
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

  <script>
  $(document).ready(function() {
    $('#riwayatTable').DataTable({
      pageLength: 10,
      order: [[2, 'desc']],
      language: {
        lengthMenu: "Tampilkan _MENU_ data",
        search: "Cari:",
        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
        infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
        zeroRecords: "Tidak ada data ditemukan",
        paginate: {
          previous: "Sebelumnya",
          next: "Berikutnya"
        }
      }
    });
  });

  document.getElementById('toggleBtn')?.addEventListener('click', () => {
    document.getElementById('sidebar').classList.toggle('active');
  });
  </script>
</body>
</html>