<?php
require_once '../../autentikasi/session.php';
require_once '../../autentikasi/functions.php';
require_once '../profile/koneksi.php';

if (!is_logged_in() || is_admin()) {
    redirect('../../autentikasi/login.php');
}

if (!isset($_SESSION['login_notified'])) {
    $_SESSION['show_login_notification'] = true;
    $_SESSION['login_notified'] = true;
}

 $user_id = $_SESSION['user_id'];

 $sql = "SELECT a.id, a.judul_rapat, a.tanggal, a.waktu, a.lokasi, a.status
        FROM agendas a
        JOIN agenda_participants ap ON a.id = ap.agenda_id
        WHERE ap.user_id = ?
        AND a.status IN ('akan datang', 'berlangsung')
        ORDER BY a.tanggal ASC, a.waktu ASC";
 $stmt = $koneksi->prepare($sql);
 $stmt->bind_param("i", $user_id);
 $stmt->execute();
 $rapat_aktif = $stmt->get_result();

 $sql1 = "SELECT COUNT(*)
        FROM agendas a 
        JOIN agenda_participants ap ON a.id = ap.agenda_id
        WHERE ap.user_id = ?
        AND a.status = 'akan datang'";
 $stmt1 = $koneksi->prepare($sql1);
 $stmt1->bind_param("i", $user_id);
 $stmt1->execute();
 $total_upcoming = $stmt1->get_result()->fetch_row()[0];

 $sql2 = "SELECT COUNT(*)
        FROM agendas a 
        JOIN agenda_participants ap ON a.id = ap.agenda_id
        WHERE ap.user_id = ?
        AND a.status = 'selesai'";
 $stmt2 = $koneksi->prepare($sql2);
 $stmt2->bind_param("i", $user_id);
 $stmt2->execute();
 $total_finished = $stmt2->get_result()->fetch_row()[0];

 $sql3 = "SELECT 
            SUM(CASE WHEN ap.status_kehadiran='hadir' THEN 1 ELSE 0 END) AS hadir,
            SUM(CASE WHEN ap.status_kehadiran='tidak_hadir' THEN 1 ELSE 0 END) AS absen
        FROM agenda_participants ap
        WHERE ap.user_id = ?";
 $stmt3 = $koneksi->prepare($sql3);
 $stmt3->bind_param("i", $user_id);
 $stmt3->execute();
 $kehadiran = $stmt3->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard Peserta - TaskMeet</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

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
      box-shadow: 2px 0 10px rgba(0, 0, 0, .1);
      transition: 0.3s;
      z-index: 999;
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
      padding: 30px;
      transition: 0.3s;
    }
    .card-rail {
      background: #fff;
      border-radius: 10px;
      padding: 18px;
      box-shadow: 0 6px 18px rgba(18, 38, 63, .06);
    }
    .card-click {
      cursor: pointer;
      transition: 0.25s;
    }
    .card-click:hover {
      transform: scale(1.03);
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
        background: #5b2c83;
        color: #fff;
        border: none;
        padding: 8px 12px;
        border-radius: 6px;
        z-index: 1000;
      }
    }
    
    .notification-container {
      position: fixed;
      top: 20px;
      right: 20px;
      z-index: 9999;
    }
    
    .login-toast {
      min-width: 300px;
    }
  </style>
</head>

<body>
  <div class="sidebar" id="sidebar">
    <div class="text-center mb-4">
      <img src="../../admin_side/format_gambar/logo.png" width="110" class="rounded-circle mb-2">
      <h5 class="fw-bold">TaskMeet</h5>
    </div>
    <a class="fw-bold" href="dashboard_pengguna.php"><i class="bi bi-house-door me-2"></i> Beranda</a>
    <a class="fw-bold" href="../detail/detail_rapat.php"><i class="bi bi-file-text me-2"></i> Detail Rapat</a>
    <a class="fw-bold" href="../profile/profil_pengguna.php"><i class="bi bi-person-circle me-2"></i> Profil</a>
    <div class="position-absolute bottom-0 w-100">
      <hr>
      <a class="fw-bold" href="../../autentikasi/logout.php"><i class="bi bi-box-arrow-right me-2"></i> Keluar</a>
    </div>
  </div>

  <button class="toggle-btn d-md-none" id="toggleBtn"><i class="bi bi-list"></i></button>

  <div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h3 class="fw-bold text-white"><i class="bi bi-house-door me-2"></i> Beranda Peserta</h3>
      <div class="d-flex align-items-center fw-bold">
        <span class="me-2 text-white">Selamat Datang, <?php echo $_SESSION['full_name']; ?>!</span>
      </div>
    </div>

    <div class="row mb-4">
      <div class="col-md-4 mb-3">
        <div class="card card-click">
          <div class="card-body">
            <h6>Rapat Akan Datang</h6>
            <h3><?= $total_upcoming ?> Rapat</h3>
            <small>Rapat yang belum dimulai</small>
          </div>
        </div>
      </div>

      <div class="col-md-4 mb-3">
        <div class="card card-click" onclick="window.location.href='../detail/detail_rapat.php'">
          <div class="card-body">
            <h6>Rapat Selesai</h6>
            <h3><?= $total_finished ?> Rapat</h3>
            <small>Klik untuk melihat riwayat</small>
          </div>
        </div>
      </div>

      <div class="col-md-4 mb-3">
        <div class="card card-click" onclick="window.location.href='../detail/detail_rapat.php'">
          <div class="card-body">
            <h6>Kehadiran Saya</h6>
            <h3>Hadir <?= $kehadiran['hadir'] ?? 0 ?> / Absen <?= $kehadiran['absen'] ?? 0 ?></h3>
            <small>Klik untuk melihat detail</small>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-3">
      <div class="col-lg-12">
        <div class="card-rail">
          <h5 class="fw-bold mb-4">Daftar Rapat</h5>
          <div class="table-responsive">
            <table class="table table-bordered bg-white table-hover">
              <thead class="table-light">
                <tr>
                  <th>No.</th>
                  <th>Judul Rapat</th>
                  <th>Tanggal</th>
                  <th>Waktu</th>
                  <th>Tempat</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
              <?php 
                $no = 1;
                if ($rapat_aktif->num_rows == 0): 
              ?>
                <tr>
                  <td colspan="6" class="text-center text-muted">Tidak ada rapat aktif.</td>
                </tr>
              <?php 
                else:
                  while ($row = $rapat_aktif->fetch_assoc()):
                    $statusClass = ($row['status'] === 'berlangsung') ? 'bg-warning text-dark' : 'bg-primary';
              ?>
                <tr>
                  <td><?= $no++ ?></td>
                  <td><?= htmlspecialchars($row['judul_rapat']) ?></td>
                  <td><?= date('d M Y', strtotime($row['tanggal'])) ?></td>
                  <td><?= substr($row['waktu'], 0, 5) ?></td>
                  <td>
                      <?php 
                      if (filter_var($row['lokasi'], FILTER_VALIDATE_URL)) {
                          echo '<a href="' . htmlspecialchars($row['lokasi']) . '" target="_blank" class="text-primary">Link Meeting</a>';
                      } else {
                          echo htmlspecialchars($row['lokasi']);
                      }
                      ?>
                  </td>
                  <td><span class="badge <?= $statusClass ?>"><?= strtoupper($row['status']) ?></span></td>
                </tr>
              <?php 
                  endwhile;
                endif;
              ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="notification-container">
    <div class="toast login-toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true" id="loginSuccessToast">
      <div class="d-flex">
        <div class="toast-body">
          <i class="fas fa-check-circle me-2"></i>
          Login berhasil! Selamat datang, <?php echo $_SESSION['full_name']; ?>
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.getElementById('toggleBtn')?.addEventListener('click', () => {
      document.getElementById('sidebar').classList.toggle('active');
    });
    
    <?php if (isset($_SESSION['show_login_notification']) && $_SESSION['show_login_notification']): ?>
      document.addEventListener('DOMContentLoaded', function() {
        const loginToast = new bootstrap.Toast(document.getElementById('loginSuccessToast'));
        loginToast.show();
        <?php unset($_SESSION['show_login_notification']); ?>
      });
    <?php endif; ?>
    
    setTimeout(() => location.reload(), 30000);
  </script>
</body>
</html>