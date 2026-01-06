<?php
date_default_timezone_set('Asia/Jakarta'); 

require_once 'session.php';
require_once 'auth.php';

 $error = '';
 $success = '';
 $token = $_GET['token'] ?? '';

if (empty($token)) {
    $error = 'Token reset password tidak valid.';
    $show_form = false;
} else {
    $sql = "SELECT user_id, email FROM password_resets WHERE token = ? AND expiry > NOW() AND used = 0";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows !== 1) {
        $error = 'Token tidak valid atau telah kadaluarsa.';
        $show_form = false;
    } else {
        $reset_data = $result->fetch_assoc();
        $show_form = true;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password Baru</title>
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
    </style>
</head>
<body>
    <?php if ($show_form): ?>
        <div class="form-container">
            <form action="reset_password_confirm.php?token=<?php echo htmlspecialchars($token); ?>" method="post">
                <img width="150" height="150" src="../admin_side/format_gambar/logo.png" alt="Logo">
                <h3>Set Password Baru</h3>
                
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" placeholder="Password Baru" required>
                </div>
                
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Konfirmasi Password Baru" required>
                </div>
                
                <button type="submit">Reset Password</button>
            </form>
        </div>
    <?php else: ?>
        <div class="form-container">
            <img width="150" height="150" src="../admin_side/format_gambar/logo.png" alt="Logo">
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <div class="back-to-login">
                <a href="login.php">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Login
                </a>
            </div>
        </div>
    <?php endif; ?>
</body>
</html>