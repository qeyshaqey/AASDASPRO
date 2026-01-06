<?php
require_once 'session.php';
require_once 'auth.php';

 $success_message = '';
if (isset($_GET['registered']) && $_GET['registered'] == 1) {
    $success_message = 'Registrasi berhasil! Silakan login dengan akun Anda.';
}
if (is_logged_in()) {
    redirect('../admin_side/dashboard/dashboard.php');
}

 $error = '';
 $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $user_type = $_POST['jenis'];
    
    if (empty($username) || empty($password)) {
        $error = 'Username dan password harus diisi';
    } else {
        if ($user_type === 'Admin') {
            $admin_username = 'admin';
            $admin_password = 'admin123';
            
            if ($username === $admin_username && $password === $admin_password) {
                $_SESSION['user_id'] = 0;
                $_SESSION['username'] = $admin_username;
                $_SESSION['full_name'] = 'Administrator';
                $_SESSION['user_role'] = 'admin';
                
                if (isset($_POST['remember']) && $_POST['remember'] === 'on') {
                    setcookie('remember_admin', $admin_username, time() + (86400 * 30), "/"); // 30 days
                }
                
                redirect('../admin_side/dashboard/dashboard.php');
            } else {
                $error = 'Username atau password admin salah';
            }
        } else {
            if (login($username, $password)) {
                if (isset($_POST['remember']) && $_POST['remember'] === 'on') {
                    setcookie('remember_user', $username, time() + (86400 * 30), "/"); // 30 days
                }
                
                redirect('../user_side/dashboard_p/dashboard_pengguna.php');
            } else {
                $error = 'Username atau password salah';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Login</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: url(https://images.unsplash.com/photo-1557804506-669a67965ba0) no-repeat center center fixed;
            background-size: cover;
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
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: normal;
        }
        .vertical-area {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        input[type="email"],
        input[type="text"],
        input[type="password"],
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 14px;
        }
        .sign p {
            display: flex;
            justify-content: center;
            align-items: center;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #784B84;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #702963;
        }
        .error-message {
            color: red;
            font-size: 12px;
            margin-bottom: 10px;
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
            padding-left: 35px;
        }        
        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        .remember-me {
            display: flex;
            align-items: center;
        }
        .remember-me input[type="checkbox"] {
            margin-right: 8px;
            width: auto;
        }
        .remember-me label {
            margin-bottom: 0;
            font-size: 14px;
            cursor: pointer;
        }
        .forgot-password {
            font-size: 14px;
            color: #784B84;
            text-decoration: none;
        }
        .forgot-password:hover {
            text-decoration: underline;
            color: #702963;
        }        
        @media (max-width: 1200px) {
            form {
                width: 40%;
            }
        }
        @media (max-width: 992px) {
            form {
                width: 50%;
            }
        }
        @media (max-width: 768px) {
            form {
                width: 70%;
            }
            .form-options {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
        }
        @media (max-width: 576px) {
            form {
                width: 90%;
            }
        }
    </style>
</head>
<body>
    <form action="login.php" method="post">
        <img width="150" height="150" src="../admin_side/format_gambar/logo.png" alt="Logo">
        <h3 style="text-align: center;">Masuk ke sesi Anda</h3>
        
        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="input-group">
            <i class="fas fa-user"></i>
            <select name="jenis">
                <option value="" disabled selected>-- Pilih Jenis User --</option>
                <option value="Admin">Admin</option>
                <option value="User">Pengguna</option>
            </select>
        </div>
        
        <div class="input-group">
            <i class="fas fa-user-circle"></i>
            <input type="text" id="username" name="username" placeholder="Nama Pengguna" required>
        </div>

        <div class="input-group">
            <i class="fas fa-lock"></i>
            <input type="password" id="password" name="password" placeholder="Kata Sandi" required>
        </div>        
        <div class="form-options">
            <div class="remember-me">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Ingat Saya</label>
            </div>
            <a href="reset_password.php" class="forgot-password">Lupa Kata Sandi?</a>
        </div>
        
        <button type="submit">Masuk</button>
        
        <div class="sign">
            <p>Belum ada akun?<a href="signin.php"> Daftar</a></p>
        </div>
    </form>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const rememberCheckbox = document.getElementById('remember');
            const usernameInput = document.getElementById('username');
            const jenisSelect = document.querySelector('select[name="jenis"]');
            
            const rememberedAdmin = localStorage.getItem('rememberedAdmin');
            const rememberedUser = localStorage.getItem('rememberedUser');
            
            if (rememberedAdmin) {
                usernameInput.value = rememberedAdmin;
                jenisSelect.value = 'Admin';
                rememberCheckbox.checked = true;
            } else if (rememberedUser) {
                usernameInput.value = rememberedUser;
                jenisSelect.value = 'User';
                rememberCheckbox.checked = true;
            }
            
            document.querySelector('form').addEventListener('submit', function(e) {
                if (rememberCheckbox.checked) {
                    if (jenisSelect.value === 'Admin') {
                        localStorage.setItem('rememberedAdmin', usernameInput.value);
                        localStorage.removeItem('rememberedUser');
                    } else {
                        localStorage.setItem('rememberedUser', usernameInput.value);
                        localStorage.removeItem('rememberedAdmin');
                    }
                } else {
                    localStorage.removeItem('rememberedAdmin');
                    localStorage.removeItem('rememberedUser');
                }
            });
        });
    </script>
</body>
</html>