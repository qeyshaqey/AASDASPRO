<?php
require_once 'session.php';
require_once 'auth.php';

if (is_logged_in()) {
    redirect('../user_side/dashboard_p/dashboard_pengguna.php');
}

 $error = '';
 $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $full_name = $_POST['full_name'];
    $jurusan = $_POST['jurusan'];
    $prodi = $_POST['prodi'];
    
    if (empty($username) || empty($email) || empty($password) || empty($full_name) || empty($jurusan) || empty($prodi)) {
        $error = 'Semua field wajib diisi';
    } elseif ($password !== $confirm_password) {
        $error = 'Password dan konfirmasi password tidak cocok';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter';
    } else {
        if (register($username, $email, $password, $full_name, $jurusan, $prodi)) {
            redirect('login.php?registered=1');
        } else {
            $error = 'Registrasi gagal. Username atau email mungkin sudah digunakan.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Sign In - IF1A2 TaskMeet</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: url(https://images.unsplash.com/photo-1557804506-669a67965ba0) no-repeat center center fixed;
            background-size: cover;
            padding: 20px;
        }
        form {
            width: 35%;
            margin: 30px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            font-family: Arial, sans-serif;
        }
        h3 {
            display: block;
            margin-top: 3px;
            margin-bottom: 10px;
            font-size: 1.17em; 
            font-weight: bold;
        }
        img {
            display: block;
            margin: 0 auto 10px;
            max-width: 100%;
            height: auto;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: normal;
        }
        .input-group {
            position: relative;
            margin-bottom: 15px;
        }
        .input-group i {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #777;
            z-index: 10;
        }
        .input-group input,
        .input-group select {
            width: 100%;
            padding: 10px;
            padding-left: 35px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 14px;
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #784B84;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #702963;
        }
        .sign p {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 20px;
            margin-bottom: 0;
        }
        .terms {
            font-size: 12px;
            color: #666;
            margin-bottom: 15px;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }
        .terms input {
            margin-right: 0;
        }
        .terms label {
            margin-bottom: 0;
            display: inline;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <form action="signin.php" method="post">
        <img width="150" height="150" src="../admin_side/format_gambar/logo.png" alt="Logo">
        <h3 class="text-center">Daftar ke sesi Anda</h3>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="input-group">
            <i class="fas fa-envelope"></i>
            <input type="email" id="email" name="email" placeholder="Email" required>
        </div>
        
        <div class="input-group">
            <i class="fas fa-user-circle"></i>
            <input type="text" id="username" name="username" placeholder="Nama Pengguna" required>
        </div>

        <div class="input-group">
            <i class="fas fa-lock"></i>
            <input type="password" id="password" name="password" placeholder="Kata Sandi" required>
        </div>

        <div class="input-group">
            <i class="fas fa-lock"></i>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Konfirmasi Kata Sandi" required>
        </div>

        <div class="input-group">
            <i class="fas fa-user"></i>
            <input type="text" id="full_name" name="full_name" placeholder="Nama Lengkap" required>
        </div>

        <div class="input-group">
            <i class="fas fa-graduation-cap"></i>
            <select id="jurusan" name="jurusan" required>
                <option value="" disabled selected>-- Pilih Jurusan --</option>
                <option value="Teknik Informatika">Teknik Informatika</option>
                <option value="Teknik Mesin">Teknik Mesin</option>
                <option value="Teknik Elektro">Teknik Elektro</option>
                <option value="Manajemen Bisnis">Manajemen Bisnis</option>
            </select>
        </div>

        <div class="input-group">
            <i class="fas fa-book"></i>
            <select id="prodi" name="prodi" required>
                <option value="" disabled selected>-- Pilih Program Studi --</option>
                <!-- Opsi akan diisi secara dinamis oleh JavaScript -->
            </select>
        </div>

        <button type="submit">Daftar</button>
        
        <div class="sign">
            <p>Sudah ada akun?<a href="login.php"> Masuk</a></p>
        </div>
    </form>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('jurusan').addEventListener('change', function() {
            loadProdiOptions();
        });

        function loadProdiOptions() {
            const jurusan = document.getElementById('jurusan').value;
            const prodiSelect = document.getElementById('prodi');
            
            prodiSelect.innerHTML = '<option value="">-- Pilih Program Studi --</option>';
            
            if (jurusan) {
                const prodiData = {
                    'Teknik Informatika': [
                        'Teknik Informatika',
                        'Teknologi Geomatika',
                        'Animasi',
                        'Teknologi Rekayasa Multimedia',
                        'Rekayasa Keamanan Siber',
                        'Rekayasa Perangkat Lunak',
                        'Teknik Komputer',
                        'Teknologi Permainan'
                    ],
                    'Teknik Mesin': [
                        'Teknik Mesin',
                        'Teknik Perawatan Pesawat Udara',
                        'Teknologi Rekayasa Konstruksi Perkapalan',
                        'Teknologi Rekayasa Pengelasan dan Fabrikasi',
                        'Program Profesi Insinyur (PSPPI)',
                        'Teknologi Rekayasa Metalurgi'
                    ],
                    'Teknik Elektro': [
                        'Teknik Elektronika Manufaktur',
                        'Teknologi Rekayasa Elektronika',
                        'Teknik Instrumentasi',
                        'Teknik Mekatronika',
                        'Teknologi Rekayasa Pembangkit Energi',
                        'Teknologi Rekayasa Robotika'
                    ],
                    'Manajemen Bisnis': [
                        'Akuntansi',
                        'Akuntansi Manajerial',
                        'Administrasi Bisnis Terapan',
                        'Logistik Perdagangan Internasional',
                        'Distribusi Barang'
                    ]
                };

                if (prodiData[jurusan]) {
                    prodiData[jurusan].forEach(prodi => {
                        const option = document.createElement('option');
                        option.value = prodi;
                        option.textContent = prodi;
                        prodiSelect.appendChild(option);
                    });
                }
            }
        }

        document.addEventListener('DOMContentLoaded', loadProdiOptions);
    </script>
</body>
</html>