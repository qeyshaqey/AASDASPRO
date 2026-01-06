    <?php
    require_once '../../autentikasi/session.php';
    require_once '../../autentikasi/functions.php';

    if (!is_logged_in()) {
        redirect('../../autentikasi/login.php');
    }
    ?>
    <!doctype html>
    <html lang="en">

    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Daftar Agenda Rapat - TASK MEET</title>

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

            .sidebar.hide~.content {
                margin-left: 0 !important;
            }

            .table thead th {
                background: #e9eef7;
            }

            .overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.4);
                z-index: 850;
            }

            .overlay.show {
                display: block;
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

                .sidebar.show~.content {
                    margin-left: 260px;
                }

                .table-responsive {
                    overflow-x: auto;
                    -webkit-overflow-scrolling: touch;
                }

                #agendaTable {
                    min-width: 100%;
                }

                #agendaTable_wrapper {
                    width: 100%;
                    overflow-x: auto;
                }

            }

            @media (max-width: 768px) {
                #agendaTable {
                    font-size: 0.85rem;
                }

                #agendaTable th,
                #agendaTable td {
                    padding: 8px 4px;
                }

                .btn-sm {
                    font-size: 0.75rem;
                    padding: 4px 8px;
                }
            }

            .toast-container {
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 9999;
            }

            .toast {
                background-color: white;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                border-left: 4px solid;
                min-width: 300px;
                max-width: 350px;
                opacity: 0.9;
            }

            .toast.success {
                border-left-color: #28a745;
            }

            .toast.error {
                border-left-color: #dc3545;
            }

            .toast.warning {
                border-left-color: #ffc107;
            }

            .toast.info {
                border-left-color: #17a2b8;
            }

            .toast-header {
                background-color: transparent;
                border-bottom: 1px solid rgba(0, 0, 0, 0.05);
                padding: 12px 15px;
                display: flex;
                align-items: center;
            }

            .toast-title {
                font-weight: 600;
                margin-right: auto;
            }

            .toast-close {
                background: none;
                border: none;
                font-size: 1.2rem;
                cursor: pointer;
                color: #6c757d;
                padding: 0;
                margin-left: 10px;
            }

            .toast-body {
                padding: 10px 15px;
            }

            .modal-confirm {
                animation: slideIn 0.3s ease-out;
            }

            @keyframes slideIn {
                from {
                    transform: translateY(-50px);
                    opacity: 0;
                }

                to {
                    transform: translateY(0);
                    opacity: 1;
                }
            }

            .modal-confirm .modal-content {
                border: none;
                border-radius: 12px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            }

            .modal-confirm .modal-header {
                border-top-left-radius: 12px;
                border-top-right-radius: 12px;
            }

            .modal-confirm .btn-danger {
                background-color: #dc3545;
                border-color: #dc3545;
            }

            .modal-confirm .btn-danger:hover {
                background-color: #c82333;
                border-color: #bd2130;
            }
        </style>
    </head>

    <body>
        <div class="toast-container" id="toastContainer"></div>
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
        <div class="overlay" id="overlay"></div>
        <!-- SIDEBAR -->
        <aside class="sidebar" id="sidebar">
            <h4>Menu</h4>
            <nav class="nav flex-column">
                <a class="nav-link" href="../dashboard/dashboard.php"><i class="fas fa-tachometer-alt"></i> Beranda</a>
                <a class="nav-link" href="../daftar_peserta/peserta.php"><i class="fas fa-box"></i> Daftar Peserta</a>
                <a class="nav-link active" href="daftarAgenda_admin.php"><i class="fas fa-users"></i> Daftar Agenda Rapat</a>
                <div class="position-absolute bottom-0 w-100">
                    <hr>
                    <a class="nav-link" href="../../autentikasi/logout.php"><i class="fas fa-sign-out-alt"></i> Keluar</a>
            </nav>
        </aside>
        <!-- MAIN CONTENT -->
        <main class="content">
            <div class="container-fluid">
                <h4 class="mb-3 fw-semibold">Daftar Agenda Rapat</h4>

                <div class="mb-3 d-flex justify-content-between flex-wrap align-items-center">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah"><i class="fa-solid fa-plus me-2"></i> Tambah Data Agenda Rapat</button>

                    <div class="d-flex gap-2 align-items-center mt-2 mt-md-0">
                        <select id="filterJenis" class="form-select" style="width:200px;">
                            <option value="">Semua Jurusan</option>
                            <option>Teknik Informatika</option>
                            <option>Teknik Mesin</option>
                            <option>Teknik Elektro</option>
                            <option>Manajemen Bisnis</option>
                        </select>
                    </div>
                </div>

                <div class="card p-3 shadow-sm">
                    <div class="table-responsive">
                        <table id="agendaTable" class="table table-bordered table-striped align-middle">

                            <thead class="table-light text-center">
                                <tr style="vertical-align: middle;">
                                    <th style="width:50px;">No</th>
                                    <th>Judul Rapat</th>
                                    <th>Jurusan</th>
                                    <th>Tanggal</th>
                                    <th>Waktu</th>
                                    <th>Tempat/Platform</th>
                                    <th>Host</th>
                                    <th>Peserta</th>
                                    <th style="width:80px;">Proses</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td>
                                        <button class="btn btn-primary btn-sm text-white fw-semibold btn-detail">Detail</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
        <!-- MODAL TAMBAH DATA -->
        <div class="modal fade" id="modalTambah" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Tambah Data Agenda Rapat</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formTambah">
                            <div class="mb-3">
                                <label class="form-label">Judul Rapat</label>
                                <input type="text" class="form-control" id="judulRapatInput" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Jurusan</label>
                                <select class="form-select" id="jurusanInput" required>
                                    <option value="">Pilih Jurusan</option>
                                    <option>Teknik Informatika</option>
                                    <option>Teknik Mesin</option>
                                    <option>Teknik Elektro</option>
                                    <option>Manajemen Bisnis</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tanggal Rapat</label>
                                <input type="date" class="form-control" id="tanggalInput" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Waktu</label>
                                <input type="time" class="form-control" id="waktuInput" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tempat / Platform</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="tipeTempat" id="offlineRadio" value="offline" checked>
                                    <label class="form-check-label" for="offlineRadio">Offline (Ruangan)</label>
                                </div>
                                <input type="text" class="form-control mt-2" id="tempatOfflineInput" placeholder="Contoh: TA 10.4">
                                <div class="form-check mt-3">
                                    <input class="form-check-input" type="radio" name="tipeTempat" id="onlineRadio" value="online">
                                    <label class="form-check-label" for="onlineRadio">Online (Link Meeting)</label>
                                </div>
                                <input type="url" class="form-control mt-2 d-none" id="zoomLinkInput" placeholder="Tempel link meeting disini https://">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Host</label>
                                <input type="text" class="form-control" id="hostInput" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Peserta</label>
                                <div class="form-control" id="pesertaCheckBoxes">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="selectAllPeserta">
                                        <label for="selectAllPeserta" class="form-check-label">Pilih Semua / Hapus Semua</label>
                                    </div>
                                    <div id="pesertaList">
                                        <!-- Peserta akan dimuat di sini secara otomatis oleh JavaScript -->
                                        <div class="text-center py-3">
                                            <div class="spinner-border spinner-border-sm" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                            <p class="mt-2 mb-0">Silakan pilih jurusan terlebih dahulu</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- MODAL DETAIL -->
        <div class="modal fade" id="modalDetail" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title">Detail Agenda Rapat</h5>
                        <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p><strong>Judul Rapat:</strong> <span id="detailJudul"></span></p>
                        <p><strong>Jurusan:</strong> <span id="detailJurusan"></span></p>
                        <p><strong>Tanggal:</strong> <span id="detailTanggal"></span></p>
                        <p><strong>Waktu:</strong> <span id="detailWaktu"></span></p>
                        <p><strong>Tempat / Platform:</strong> <span id="detailTempat"></span></p>
                        <p><strong>Host:</strong> <span id="detailHost"></span></p>
                        <hr>
                        <h6 class="fw-semibold">Peserta:</h6>
                        <ul id="detailPesertaList" class="list-group mb-3"></ul>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button class="btn btn-warning" id="btnEditDetail"><i class="fa fa-pen me-1"></i> Edit</button>
                        <button class="btn btn-danger" id="btnHapusDetail"><i class="fa fa-trash me-1"></i> Hapus</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- MODAL KONFIRMASI KUSTOM -->
        <div class="modal fade modal-confirm" id="modalConfirm" tabindex="-1">
            <div class="modal-dialog modal-sm modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i>Konfirmasi</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <i class="fas fa-exclamation-triangle text-danger" style="font-size: 3rem;"></i>
                        <p id="confirmMessage" class="mt-3 mb-0">Apakah Anda yakin ingin melakukan tindakan ini?</p>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-danger" id="btnConfirmYes">Ya, Lanjutkan</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- MODAL EDIT -->
        <div class="modal fade" id="modalEdit" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-warning text-white">
                        <h5 class="modal-title">Edit Agenda Rapat</h5>
                        <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formEdit">
                            <div class="mb-3">
                                <label class="form-label">Judul Rapat</label>
                                <input type="text" class="form-control" id="editJudul">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Jurusan</label>
                                <select class="form-select" id="editJurusan">
                                    <option value="">Pilih Jurusan</option>
                                    <option>Teknik Informatika</option>
                                    <option>Teknik Mesin</option>
                                    <option>Teknik Elektro</option>
                                    <option>Manajemen Bisnis</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tanggal</label>
                                <input type="date" class="form-control" id="editTanggal">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Waktu</label>
                                <input type="time" class="form-control" id="editWaktu">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tempat / Platform</label>
                                <input type="text" class="form-control" id="editTempat">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Host</label>
                                <input type="text" class="form-control" id="editHost">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Peserta & Kehadiran</label>
                                <ul id="editPesertaList" class="list-group"></ul>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer justify-content-end">
                        <button class="btn btn-success" id="btnSimpanEdit"><i class="fa fa-save me-1"></i> Simpan</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- JS -->
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

        <!-- DataTables -->
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.colVis.min.js"></script>

        <script>
            $(document).ready(function() {
                console.log("Document siap. Memulai inisialisasi...");

                // === SIDEBAR TOGGLE ===
                const sidebar = document.getElementById('sidebar');
                const btnToggle = document.getElementById('btnToggleFloating');
                if (btnToggle) {
                    btnToggle.addEventListener('click', () => {
                        if (window.innerWidth < 992) {
                            sidebar.classList.toggle('show');
                            overlay.classList.toggle('show');
                        } else {
                            sidebar.classList.toggle('hide');
                        }

                        sidebar.addEventListener('transitionend', () => {
                            table.columns.adjust().draw();
                        });
                    });


                    overlay.addEventListener('click', () => {
                        sidebar.classList.remove('show');
                        overlay.classList.remove('show');
                    });
                }

                function showToast(message, type = 'info', title = '') {
                    const toastContainer = document.getElementById('toastContainer');

                    let icon = '';
                    switch (type) {
                        case 'success':
                            icon = '<i class="fas fa-check-circle text-success me-2"></i>';
                            break;
                        case 'error':
                            icon = '<i class="fas fa-exclamation-circle text-danger me-2"></i>';
                            break;
                        case 'warning':
                            icon = '<i class="fas fa-exclamation-triangle text-warning me-2"></i>';
                            break;
                        default:
                            icon = '<i class="fas fa-info-circle text-info me-2"></i>';
                    }

                    const toastId = 'toast-' + Date.now();
                    const toastHTML = `
                <div id="${toastId}" class="toast ${type}" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header">
                        ${icon}
                        <strong class="toast-title">${title || (type.charAt(0).toUpperCase() + type.slice(1))}</strong>
                        <button type="button" class="toast-close" data-bs-dismiss="toast" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="toast-body">
                        ${message}
                    </div>
                </div>
            `;

                    toastContainer.insertAdjacentHTML('beforeend', toastHTML);

                    const toastElement = document.getElementById(toastId);
                    const toast = new bootstrap.Toast(toastElement, {
                        autohide: true,
                        delay: 5000
                    });

                    toast.show();

                    toastElement.addEventListener('hidden.bs.toast', function() {
                        toastElement.remove();
                    });
                }
                // === FUNGSI UNTUK MEMUAT PESERTA ===
                function loadPeserta(jurusan) {
                    console.log("Memuat peserta untuk jurusan:", jurusan);
                    $.ajax({
                        url: 'get_peserta_by_jurusan.php',
                        type: 'GET',
                        data: {
                            jurusan: jurusan
                        },
                        dataType: 'json',
                        success: function(response) {
                            console.log("Berhasil memuat peserta:", response);
                            let pesertaHTML = '';
                            if (response.length === 0) {
                                pesertaHTML = '<p class="text-center py-3">Tidak ada peserta untuk jurusan ini</p>';
                            } else {
                                response.forEach(function(peserta) {
                                    pesertaHTML += `
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input peserta-checkbox" id="peserta${peserta.id}" value="${peserta.nama}" data-id="${peserta.id}">
                                    <label class="form-check-label" for="peserta${peserta.id}">${peserta.nama}</label>
                                </div>
                            `;
                                });
                            }
                            $('#pesertaList').html(pesertaHTML);
                            $('#selectAllPeserta').prop('checked', false);
                        },
                        error: function(xhr, status, error) {
                            console.error("Gagal memuat peserta:", error);
                            $('#pesertaList').html('<p class="text-danger text-center py-3">Gagal memuat data peserta. Cek console.</p>');
                        }
                    });
                }
                // === EVENT LISTENER MODAL & FORM ===
                $('#modalTambah').on('show.bs.modal', function() {
                    loadPeserta($('#jurusanInput').val());
                });

                $('#jurusanInput').on('change', function() {
                    loadPeserta($(this).val());
                });

                $(document).on('change', '#selectAllPeserta', function() {
                    const checked = $(this).is(':checked');
                    $('.peserta-checkbox').prop('checked', checked);
                });

                $(document).on('change', '.peserta-checkbox', function() {
                    const total = $('.peserta-checkbox').length;
                    const checked = $('.peserta-checkbox:checked').length;
                    $('#selectAllPeserta').prop('checked', total === checked && total > 0);
                });

                $('input[name="tipeTempat"]').on('change', function() {
                    if ($(this).val() === 'offline') {
                        $('#tempatOfflineInput').removeClass('d-none');
                        $('#zoomLinkInput').addClass('d-none').val('');
                    } else {
                        $('#tempatOfflineInput').addClass('d-none').val('');
                        $('#zoomLinkInput').removeClass('d-none');
                    }
                });
                // === DATATABLES ===
                const table = $('#agendaTable').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    scrollX: true,
                    pageLength: 5,
                    dom: 'Bfrtip',
                    buttons: [{
                            extend: 'copy',
                            text: 'Salin'
                        },
                        {
                            extend: 'excel',
                            text: 'Excel'
                        },
                        {
                            extend: 'pdf',
                            text: 'PDF'
                        },
                        {
                            extend: 'print',
                            text: 'Cetak'
                        },
                        {
                            extend: 'colvis',
                            text: 'Pilih Kolom'
                        }
                    ],
                    order: [
                        [1, 'asc']
                    ],
                    language: {
                        "sProcessing": "Sedang memproses...",
                        "sLengthMenu": "Tampilkan _MENU_ entri",
                        "sZeroRecords": "Tidak ditemukan data yang cocok",
                        "sInfo": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                        "sInfoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
                        "sInfoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
                        "sInfoPostFix": "",
                        "sSearch": "Cari:",
                        "sUrl": "",
                        "oPaginate": {
                            "sFirst": "Pertama",
                            "sPrevious": "Sebelumnya",
                            "sNext": "Selanjutnya",
                            "sLast": "Terakhir"
                        },
                        "buttons": {
                            "copy": "Salin",
                            "copyTitle": "Salin ke clipboard",
                            "copySuccess": {
                                "_": "Salin %d baris",
                                "1": "Salin 1 baris"
                            },
                            "excel": "Excel",
                            "pdf": "PDF",
                            "print": "Cetak",
                            "colvis": "Pilih Kolom"
                        }
                    },
                    ajax: {
                        url: 'get_agenda.php',
                        type: 'POST',
                        data: function(d) {
                            d.filterJurusan = $('#filterJenis').val();
                        }
                    },
                    columns: [{
                            data: null,
                            orderable: false,
                            className: 'text-center',
                            render: function(data, type, row, meta) {
                                return meta.row + meta.settings._iDisplayStart + 1;
                            }
                        },
                        {
                            data: 'judul_rapat'
                        },
                        {
                            data: 'jurusan'
                        },
                        {
                            data: 'tanggal'
                        },
                        {
                            data: 'waktu'
                        },
                        {
                            data: 'tempat',
                            render: function(data, type, row) {
                                if (row.tipe_tempat === 'online') {
                                    return `<a href="${data}" target="_blank" class="text-primary text-decoration-none">Link Meeting</a>`;
                                } else {
                                    return data;
                                }
                            }
                        },
                        {
                            data: 'host'
                        },
                        {
                            data: 'peserta'
                        },
                        {
                            data: null,
                            orderable: false,
                            className: 'text-center',
                            render: function(data, type, row) {
                                return `<button class="btn btn-primary btn-sm text-white fw-semibold btn-detail" data-id="${row.id}">Detail</button>`;
                            }
                        }
                    ]
                });

                $('#filterJenis').on('change', function() {
                    table.ajax.reload();
                });

                // === CRUD OPERATIONS ===
                // --- TAMBAH DATA ---
                $('#formTambah').on('submit', function(e) {
                    e.preventDefault();
                    const pesertaTerpilih = [];
                    $('.peserta-checkbox:checked').each(function() {
                        pesertaTerpilih.push($(this).data('id'));
                    });
                    const formData = {
                        action: 'tambah',
                        judul_rapat: $('#judulRapatInput').val(),
                        jurusan: $('#jurusanInput').val(),
                        tanggal: $('#tanggalInput').val(),
                        waktu: $('#waktuInput').val(),
                        host: $('#hostInput').val(),
                        tipe_tempat: $('input[name="tipeTempat"]:checked').val(),
                        peserta_ids: pesertaTerpilih
                    };
                    if (formData.tipe_tempat === 'online') {
                        formData.zoom_link = $('#zoomLinkInput').val();
                    } else {
                        formData.tempat_offline = $('#tempatOfflineInput').val();
                    }
                    $.ajax({
                        url: 'crud_agenda.php',
                        type: 'POST',
                        data: formData,
                        dataType: 'json',
                        success: function(response) {
                            console.log("Response dari server (tambah):", response);
                            if (response.success) {
                                $('#modalTambah').modal('hide');
                                $('#formTambah')[0].reset();
                                table.ajax.reload();
                                showToast(response.message, 'success', 'Berhasil');
                            } else {
                                showToast('Error: ' + response.message, 'error', 'Kesalahan');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Gagal menambah agenda:", error);
                            showToast('Terjadi kesalahan saat menghubungi server. Cek console untuk detail.', 'error', 'Kesalahan');
                        }
                    });
                });
                // --- DETAIL ---
                $('#agendaTable tbody').on('click', '.btn-detail', function() {
                    const id = $(this).data('id');
                    console.log("Mengambil detail untuk ID:", id);
                    $.ajax({
                        url: 'crud_agenda.php',
                        type: 'GET',
                        data: {
                            action: 'detail',
                            id: id
                        },
                        dataType: 'json',
                        success: function(response) {
                            console.log("Response dari server (detail):", response);
                            if (response.success) {
                                const data = response.data;
                                $('#detailJudul').text(data.judul_rapat);
                                $('#detailJurusan').text(data.jurusan);
                                $('#detailTanggal').text(data.tanggal);
                                $('#detailWaktu').text(data.waktu);
                                $('#detailTempat').html(data.tipe_tempat === 'online' ? `<a href="${data.lokasi}" target="_blank">Link Meeting</a>` : data.lokasi);
                                $('#detailHost').text(data.host);
                                let pesertaHTML = '';
                                data.peserta_detail.forEach(p => {
                                    pesertaHTML += `<li class="list-group-item">${p.nama_peserta}</li>`;
                                });
                                $('#detailPesertaList').html(pesertaHTML);
                                $('#modalDetail').data('agenda-id', id).data('agenda-data', data).modal('show');
                            } else {
                                showToast('Error: ' + response.message, 'error', 'Kesalahan');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Gagal mengambil detail:", error);
                            showToast('Terjadi kesalahan saat menghubungi server. Cek console untuk detail.', 'error', 'Kesalahan');
                        }
                    });
                });
                // --- FUNGSI UNTUK MENAMPILKAN MODAL KONFIRMASI KUSTOM ---
                function showConfirmModal(message, onConfirm) {
                    $('#confirmMessage').text(message);
                    $('#btnConfirmYes').off('click').on('click', function() {
                        $('#modalConfirm').modal('hide');
                        if (typeof onConfirm === 'function') {
                            onConfirm();
                        }
                    });
                    new bootstrap.Modal(document.getElementById('modalConfirm')).show();
                }
                // --- HAPUS ---
                $('#btnHapusDetail').on('click', function() {
                    const agendaId = $('#modalDetail').data('agenda-id');
                    if (!agendaId) return;

                    showConfirmModal('Apakah Anda yakin ingin menghapus agenda ini?', function() {
                        $.ajax({
                            url: 'crud_agenda.php',
                            type: 'POST',
                            data: {
                                action: 'hapus',
                                id: agendaId
                            },
                            dataType: 'json',
                            success: function(response) {
                                if (response.success) {
                                    $('#modalDetail').modal('hide');
                                    table.ajax.reload();
                                    showToast(response.message, 'success', 'Berhasil');
                                } else {
                                    showToast('Error: ' + response.message, 'error', 'Kesalahan');
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error("Gagal menghapus:", error);
                                showToast('Terjadi kesalahan saat menghubungi server. Cek console untuk detail.', 'error', 'Kesalahan');
                            }
                        });
                    });
                });
                // --- EDIT ---
                $('#btnEditDetail').on('click', function() {
                    const data = $('#modalDetail').data('agenda-data');
                    $('#editJudul').val(data.judul_rapat);
                    $('#editJurusan').val(data.jurusan);
                    $('#editTanggal').val(data.tanggal);
                    $('#editWaktu').val(data.waktu);
                    $('#editHost').val(data.host);
                    $('#editTempat').val(data.lokasi);
                    let pesertaHTML = '';
                    data.peserta_detail.forEach(p => {
                        const nama = p.nama_peserta;
                        const status = p.status_kehadiran;
                        pesertaHTML += `<li class="list-group-item d-flex justify-content-between align-items-center"><span>${nama}</span><div><label><input type="radio" name="status_${nama}" value="hadir" ${status === 'hadir' ? 'checked' : ''}> Hadir</label><label><input type="radio" name="status_${nama}" value="tidak_hadir" ${status === 'tidak_hadir' ? 'checked' : ''}> Tidak Hadir</label></div></li>`;
                    });
                    $('#editPesertaList').html(pesertaHTML);
                    $('#modalEdit').data('agenda-id', data.id);
                    $('#modalDetail').modal('hide');
                    $('#modalEdit').modal('show');
                });
                // --- SIMPAN EDIT ---
                $('#btnSimpanEdit').on('click', function() {
                    const agendaId = $('#modalEdit').data('agenda-id');
                    if (!agendaId) return;
                    const pesertaStatus = {};
                    $('#editPesertaList li').each(function() {
                        const nama = $(this).find('span').text();
                        const status = $(this).find('input[type="radio"]:checked').val();
                        if (status) pesertaStatus[nama] = status;
                    });
                    const formData = {
                        action: 'edit',
                        id: agendaId,
                        judul_rapat: $('#editJudul').val(),
                        jurusan: $('#editJurusan').val(),
                        tanggal: $('#editTanggal').val(),
                        waktu: $('#editWaktu').val(),
                        host: $('#editHost').val(),
                        tempat: $('#editTempat').val(),
                        peserta_status: JSON.stringify(pesertaStatus)
                    };
                    $.ajax({
                        url: 'crud_agenda.php',
                        type: 'POST',
                        data: formData,
                        dataType: 'json',
                        success: function(response) {
                            console.log("Response dari server (edit):", response);
                            if (response.success) {
                                $('#modalEdit').modal('hide');
                                table.ajax.reload();
                                showToast(response.message, 'success', 'Berhasil');
                            } else {
                                showToast('Error: ' + response.message, 'error', 'Kesalahan');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Gagal menyimpan edit:", error);
                            showToast('Terjadi kesalahan saat menghubungi server. Cek console untuk detail.', 'error', 'Kesalahan');
                        }
                    });
                });
            });
        </script>
    </body>

    </html>