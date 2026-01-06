<?php
require_once '../../autentikasi/session.php';
require_once '../../autentikasi/functions.php';

if (!is_logged_in()) {
    redirect('../../autentikasi/login.php');
}
if (!isset($_SESSION['login_notified'])) {
    $_SESSION['show_login_notification'] = true;
    $_SESSION['login_notified'] = true;
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dashboard - IF1A2 TASK MEET</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet" />
    
    <style>
        :root {
            --sidebar-w: 250px;
            --blue-1: #784B84;
            --blue-2: #D7BFDC;
        }
        
        body {
            background: #f4f7fb;
            margin: 0;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
        }
        
        .navbar {
            background: linear-gradient(90deg, var(--blue-1), var(--blue-2));
            z-index: 1000 !important;
            position: fixed;
            top: 0;
            width: 100%;
        }
        
        .toggle-floating {
            position: fixed !important;
            top: 14px;
            left: 14px;
            z-index: 5000 !important;
            background: #ffffff;
            border-radius: 50%;
            width: 46px;
            height: 46px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--blue-1);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        
        .navbar-brand {
            margin-left: 70px;
        }
        
        .navbar-brand img {
            height: 55px;
            width: auto;
        }
        
        .sidebar {
            width: var(--sidebar-w);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            padding: 72px 14px 20px 14px;
            background: linear-gradient(180deg, var(--blue-1), var(--blue-2));
            color: #fff;
            overflow-y: auto;
            transition: left .28s ease;
            box-shadow: 2px 0 12px rgba(0, 0, 0, .06);
            z-index: 900 !important;
        }
        
        .sidebar.hide {
            left: -260px;
        }
        
        .sidebar h4 {
            color: #fff;
            font-weight: 700;
            margin: 20px 8px 18px;
            font-size: 14px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        
        .sidebar .nav-link {
            color: rgba(255, 255, 255, .95);
            border-radius: 8px;
            padding: 10px 12px;
            margin: 6px 6px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(255, 255, 255, .14);
            color: #fff;
            transform: translateX(6px);
        }
        
        .content {
            margin-left: var(--sidebar-w);
            padding: 100px 24px 36px 24px;
            transition: margin-left .28s ease;
        }
        
        .sidebar.hide ~ .content {
            margin-left: 0 !important;
        }

        .table thead th {
            background: #e9eef7;
        }
        
        .overlay {
            display: none;
        }
        
        .overlay.show {
            display: block;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
            z-index: 850;
        }
        
        @media (max-width: 992px) {
            .sidebar {
                left: -260px;
                padding-top: 70px;
                width: 260px;
            }
            
            .sidebar.show {
                left: 0;
            }
            
            .content {
                margin-left: 0;
                padding-top: 100px;
            }
            
            .navbar-brand {
                margin-left: 60px;
            }
            
            .sidebar.show ~ .content {
                margin-left: 260px;
                margin-left: 0 !important;
            }
            
            .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                width: 100%;
            }
            
            #jadwalTable {
                min-width: 800px;
            }
            
            .sidebar.show ~ .content .table-responsive {
                width: calc(100vw - 260px);
            }
        }
        
        @media (max-width: 768px) {
            .d-flex.gap-2 {
                flex-direction: column !important;
                width: 100%;
            }
            
            .d-flex.gap-2 > * {
                width: 100% !important;
            }
        }
        .notification-container {
            position: fixed;
            top: 80px;
            right: 20px;
            z-index: 9999;
        }
        
        .login-toast {
            min-width: 300px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container-fluid d-flex align-items-center">
            <button class="toggle-floating" id="btnToggleFloating" title="Toggle menu">
                <i class="fas fa-bars"></i>
            </button>
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="../format_gambar/logo.png" alt="logo" class="brand-logo">
                <span class="text-white fw-bold fs-5">TASK MEET</span>
            </a>
        </div>
    </nav>
    <div class="overlay" id="overlay"></div>
    <aside class="sidebar" id="sidebar">
        <h4>Menu</h4>
        <nav class="nav flex-column">
            <a class="nav-link active" href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Beranda</a>
            <a class="nav-link" href="../daftar_peserta/peserta.php"><i class="fas fa-box"></i> Daftar Peserta</a>
            <a class="nav-link" href="../daftar_agenda/daftarAgenda_admin.php"><i class="fas fa-users"></i> Daftar Agenda Rapat</a>
            <div class="position-absolute bottom-0 w-100">
            <hr>
            <a class="nav-link" href="../../autentikasi/logout.php"><i class="fas fa-sign-out-alt"></i> Keluar</a>
        </nav>
    </aside>    
    <main class="content">
        <div class="container-fluid">
            <h4 class="mb-3 fw-semibold">Selamat Datang, <?php echo $_SESSION['full_name']; ?></h4>

            <div class="d-flex justify-content-between flex-wrap align-items-center mb-3">
                <div></div>
                
                <div class="d-flex gap-2 align-items-center mt-2 mt-md-0">
                    <select id="jenisRapat" class="form-select" style="width:200px;">
                        <option value="">Semua Jurusan</option>
                        <option>Teknik Informatika</option>
                        <option>Teknik Mesin</option>
                        <option>Teknik Elektro</option>
                        <option>Manajemen Bisnis</option>
                    </select>

                    <select id="filterStatus" class="form-select" style="width:200px;">
                        <option value="">Semua Status</option>
                        <option value="akan datang">Akan Datang</option>
                        <option value="berlangsung">Berlangsung</option>
                        <option value="selesai">Selesai</option>
                    </select>

                    <input id="datePicker" class="form-control" style="width:200px;" placeholder="Pilih tanggal" readonly>
                </div>
            </div>

            <div class="card p-3 shadow-sm">
                <div class="table-responsive">
                    <table id="jadwalTable" class="table table-striped table-bordered">
                        <thead class="table-light text-center">
                            <tr>
                                <th>No</th>
                                <th>Judul Rapat</th>
                                <th>Jurusan</th>
                                <th>Tanggal</th>
                                <th>Waktu</th>
                                <th>Tempat / Platform</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data akan dimuat secara dinamis -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
    $(document).ready(function() {
        <?php if (isset($_SESSION['show_login_notification']) && $_SESSION['show_login_notification']): ?>
                const loginToast = new bootstrap.Toast(document.getElementById('loginSuccessToast'));
                loginToast.show();
                <?php unset($_SESSION['show_login_notification']); ?>
        <?php endif; ?>
        const table = $('#jadwalTable').DataTable({
            pageLength: 10,
            lengthChange: false,
            searching: false,
            ordering: false,
            info: false,
            paging: true,
            language: {
                "oPaginate": {
                    "sFirst": "Pertama",
                    "sPrevious": "Sebelumnya",
                    "sNext": "Selanjutnya",
                    "sLast": "Terakhir"
                }
            }
        });

        function loadAndRenderRapat() {
            const jurusanFilter = $('#jenisRapat').val();
            const statusFilter = $('#filterStatus').val();
            const tanggalFilter = $('#datePicker').val();

            console.log("Memulai proses pemuatan data dengan filter:", { jurusanFilter, statusFilter, tanggalFilter });

            $.ajax({
                url: "../daftar_agenda/update_status_rapat.php",
                type: "POST",
                dataType: 'json',
                success: function(updateResponse) {
                    console.log("Status rapat diperbarui:", updateResponse.message);
                    fetchAndDisplayData(jurusanFilter, statusFilter, tanggalFilter);
                },
                error: function(xhr, status, error) {
                    console.error("Gagal memperbarui status:", error);
                    fetchAndDisplayData(jurusanFilter, statusFilter, tanggalFilter);
                }
            });
        }

        function fetchAndDisplayData(jurusanFilter, statusFilter, tanggalFilter) {
            $.ajax({
                url: "../daftar_agenda/get_agenda_dashboard.php?_t=" + new Date().getTime(),
                type: "POST",
                data: {
                    filterJurusan: jurusanFilter,
                    filterStatus: statusFilter,
                    filterTanggal: tanggalFilter
                },
                dataType: "json",
                success: function(response) {
                    console.log("Response dari server:", response);
                    
                    if (!response.success || !Array.isArray(response.data)) {
                        console.error("Response tidak valid:", response);
                        $('#jadwalTable tbody').empty();
                        $('#jadwalTable tbody').append('<tr><td colspan="6" class="text-center text-danger">Gagal memuat data agenda. Response tidak valid.</td></tr>');
                        return;
                    }

                    const dataToRender = response.data;
                    $('#jadwalTable tbody').empty();

                    if (dataToRender.length > 0) {
                        dataToRender.forEach((item, index) => {
                            let statusText = item.status;
                            let statusClass = 'bg-primary';

                            if (statusText === 'selesai') {
                                statusClass = 'bg-secondary';
                            } else if (statusText === 'berlangsung') {
                                statusClass = 'bg-warning text-dark';
                            }
                            let lokasiDisplay = item.lokasi;
                            if (item.lokasi && (item.lokasi.startsWith('http://') || item.lokasi.startsWith('https://'))) {
                                lokasiDisplay = `<a href="${item.lokasi}" target="_blank" class="text-primary text-decoration-underline">Link Meeting</a>`;
                            }

                            $('#jadwalTable tbody').append(`
                                <tr>
                                    <td>${index + 1}</td>
                                    <td>${item.judul_rapat}</td>
                                    <td>${item.jurusan}</td>
                                    <td>${item.tanggal}</td>
                                    <td>${item.waktu}</td>
                                    <td>${lokasiDisplay}</td>
                                    <td><span class="badge ${statusClass}">${statusText.toUpperCase()}</span></td>
                                </tr>
                            `);
                        });
                    } else {
                        $('#jadwalTable tbody').append('<tr><td colspan="7" class="text-center">Tidak ada agenda rapat yang sesuai filter.</td></tr>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error:", error);
                    console.error("Status:", status);
                    console.error("Response Text:", xhr.responseText);
                    $('#jadwalTable tbody').empty();
                    $('#jadwalTable tbody').append('<tr><td colspan="6" class="text-center text-danger">Gagal memuat data agenda. Periksa console untuk detail error.</td></tr>');
                }
            });
        }

        $('#jenisRapat').on('change', loadAndRenderRapat);
        $('#filterStatus').on('change', loadAndRenderRapat);
        
        flatpickr("#datePicker", {
            dateFormat: "Y-m-d",
            onChange: function() {
                loadAndRenderRapat();
            }
        });

        loadAndRenderRapat();

        const sidebar = document.getElementById('sidebar');
        const btnToggle = document.getElementById('btnToggleFloating');
        const overlay = document.getElementById('overlay');
        
        btnToggle.addEventListener('click', () => {
            if (window.innerWidth < 992) {
                sidebar.classList.toggle('show');
                overlay.classList.toggle('show');
            } else {
                sidebar.classList.toggle('hide');
            }
        });

        overlay.addEventListener('click', () => {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        });
    });
    </script>
</body>
</html>