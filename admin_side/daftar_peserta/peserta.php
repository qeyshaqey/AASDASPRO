<?php
require_once '../../autentikasi/session.php';
require_once '../../autentikasi/functions.php';
require_once 'koneksi.php';

if (!is_logged_in()) {
    redirect('../../autentikasi/login.php');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Daftar Peserta - IF1A2 TASK MEET</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css" rel="stylesheet">

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

        .sidebar.hide+.content {
            margin-left: 0;
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
        
        .info-badge {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 12px 16px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <!-- NAVBAR -->
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
    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">
        <div class="overlay" id="overlay"></div>
        <h4>Menu</h4>
        <nav class="nav flex-column">
            <a class="nav-link" href="../dashboard/dashboard.php"><i class="fas fa-tachometer-alt"></i> Beranda</a>
            <a class="nav-link active" href="peserta.php"><i class="fas fa-box"></i> Daftar Peserta</a>
            <a class="nav-link" href="../daftar_agenda/daftarAgenda_admin.php"><i class="fas fa-users"></i> Daftar Agenda Rapat</a>
            <div class="position-absolute bottom-0 w-100">
            <hr>
            <a class="nav-link" href="../../autentikasi/logout.php"><i class="fas fa-sign-out-alt"></i> Keluar</a>
        </nav>
    </aside>
    <!-- MAIN CONTENT -->
    <main class="content">
        <div class="container-fluid"> 
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
                <h4 class="fw-semibold mb-2 mb-md-0">Daftar Peserta</h4> 
            </div>
            <!-- INFO BOX -->
            <div class="info-badge">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Informasi:</strong> Daftar peserta ini diisi otomatis saat pengguna melakukan registrasi. Admin hanya dapat melihat data, tidak dapat mengedit atau menghapus.
            </div>
            <!-- FILTER JURUSAN -->
            <div class="mb-3">
                <label class="form-label fw-semibold">Filter Jurusan:</label>
                <select id="filterJurusan" class="form-select" style="width:250px;">
                    <option value="">Semua Jurusan</option>
                    <option>Teknik Informatika</option>
                    <option>Teknik Mesin</option>
                    <option>Teknik Elektro</option>
                    <option>Manajemen Bisnis</option>
                </select>
            </div>
            <div class="card p-3 shadow-sm">
                <div class="table-responsive">
                    <table id="userTable" class="table table-bordered table-striped align-middle">
                        <thead class="table-light text-center">
                            <tr>
                                <th style="width:50px;">No</th>
                                <th>Nama Peserta</th>
                                <th>Username</th>
                                <th>Jurusan</th>
                                <th>Prodi</th>
                                <th>Email</th>
                                <th style="width:120px;">Tanggal Daftar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $data_peserta = mysqli_query($koneksi, "SELECT * FROM users WHERE role = 'user' ORDER BY created_at DESC");
                            $no = 1;
                            while ($row = mysqli_fetch_assoc($data_peserta)) { ?>
                            <tr>
                                <td class="text-center"><?= $no++; ?></td>
                                <td><?= htmlspecialchars($row['full_name']); ?></td>
                                <td><?= htmlspecialchars($row['username']); ?></td>
                                <td><?= htmlspecialchars($row['jurusan']); ?></td>
                                <td><?= htmlspecialchars($row['prodi']); ?></td>
                                <td><?= htmlspecialchars($row['email']); ?></td>
                                <td class="text-center"><?= date('d/m/Y', strtotime($row['created_at'])); ?></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- JQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <!-- Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- DataTables CORE -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <!-- JSZip (untuk Excel) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

    <!-- PDFMake -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

    <!-- Buttons -->
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.colVis.min.js"></script>

    <script>
    $(document).ready(function () {
        // --- INISIALISASI DATATABLES ---
        const table = $('#userTable').DataTable({
            pageLength: 10,
            dom: 'Bfrtip',
            buttons: [
                { extend: 'copy', text: 'Salin' },
                { extend: 'excel', text: 'Excel' },
                { extend: 'pdf', text: 'PDF' },
                { extend: 'print', text: 'Cetak' },
                { extend: 'colvis', text: 'Pilih Kolom' }
            ],
            order: [[6, 'desc']],
            language: {
                lengthMenu: "Tampilkan _MENU_ data per halaman",
                zeroRecords: "Data tidak ditemukan",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                infoFiltered: "(difilter dari _MAX_ total data)",
                search: "Cari:",
                paginate: { 
                    previous: "Sebelumnya", 
                    next: "Berikutnya" 
                }
            }
        });
        // --- SIDEBAR TOGGLE ---
        const sidebar = document.getElementById('sidebar');
        const btnToggle = document.getElementById('btnToggleFloating');
        const overlay = document.getElementById('overlay');
        
        if (btnToggle) {
            btnToggle.addEventListener('click', () => {
                if (window.innerWidth < 992) {
                    sidebar.classList.toggle('show');
                    overlay.classList.toggle('show');
                } else {
                    sidebar.classList.toggle('hide');
                }
            });
        }
        if (overlay) {
            overlay.addEventListener('click', () => {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
            });
        }
        // --- FILTER JURUSAN ---
        $('#filterJurusan').on('change', function () {
            table.column(3).search(this.value).draw();
        });
    });
    </script>
</body>
</html>