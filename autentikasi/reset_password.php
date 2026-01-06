<?php
require_once 'session.php';
require_once 'auth.php';

 $error = '';
 $success = '';
 $reset_link = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    if (empty($email)) {
        $error = 'Email harus diisi';
    } else {
        $sql = "SELECT id, username, email FROM users WHERE email = ? AND role = 'user'";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            $token = bin2hex(random_bytes(32));
            $expiry = date('Y-m-d H:i:s', strtotime('+24 hours'));

            $sql = "INSERT INTO password_resets (user_id, email, token, expiry) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isss", $user['id'], $user['email'], $token, $expiry);

            if ($stmt->execute()) {
                $reset_link = "http://localhost/PBL-FIX-3/autentikasi/reset_password_confirm.php?token=" . $token;
                $success = 'Link reset password telah dibuat. Silakan klik link di bawah ini:';
            } else {
                $error = 'Terjadi kesalahan. Silakan coba lagi.';
            }
        } else {
            $success = 'Jika email terdaftar, link reset password akan dibuat.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        .form-container {
            width: 35%;
            max-width: 450px;
            margin: 30px auto;
            padding: 30px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            font-family: Arial, sans-serif;
        }
        h3 {
            text-align: center;
            font-size: 1.17em; 
            font-weight: bold;
            margin-bottom: 20px;
        }
        img {
            display: block;
            margin: 0 auto 20px;
        }
        .input-group {
            position: relative;
            margin-bottom: 15px;
        }
        .input-group i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #777;
            z-index: 10;
        }
        .input-group input {
            width: 100%;
            padding: 12px;
            padding-left: 40px;
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
        .back-to-login {
            text-align: center;
            margin-top: 20px;
        }
        .back-to-login a {
            color: #784B84;
            text-decoration: none;
            font-size: 14px;
        }
        .back-to-login a:hover {
            text-decoration: underline;
        }
        /* Style untuk link reset */
        .reset-link-box {
            background-color: #e9ecef;
            border: 1px solid #ced4da;
            border-radius: 4px;
            padding: 10px;
            word-break: break-all;
            margin-top: 15px;
        }
        .reset-link-box a {
            color: #007bff;
            text-decoration: none;
        }
        .reset-link-box a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <img width="150" height="150" src="../admin_side/format_gambar/logo.png" alt="Logo">
        <h3>Atur Ulang Kata Sandi Pengguna</h3>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
            
            <?php if (!empty($reset_link)): ?>
                <div class="reset-link-box">
                    <a href="<?php echo $reset_link; ?>" target="_blank"><?php echo $reset_link; ?></a>
                </div>
            <?php endif; ?>
        <?php endif; ?>        
        <?php if (empty($reset_link)): ?>
            <form action="reset_password.php" method="post">
                <div class="input-group">
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="email" name="email" placeholder="Masukkan email Anda" required>
                </div>
                
                <button type="submit">Buat Link Reset</button>
            </form>
        <?php endif; ?>
        <div class="back-to-login">
            <a href="login.php">
                <i class="fas fa-arrow-left me-1"></i> Kembali masuk
            </a>
        </div>
    </div>
</body>
</html>