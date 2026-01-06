<?php
require_once '../admin_side/daftar_agenda/config.php';
require_once 'functions.php';

function login($username, $password) {
    global $conn;
    
    $sql = "SELECT * FROM users WHERE username = ? AND role = 'user'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['jurusan'] = $user['jurusan'];
            $_SESSION['prodi'] = $user['prodi'];
            $_SESSION['user_role'] = $user['role'];
            
            return true;
        }
    }
    
    return false;
}

function register($username, $email, $password, $full_name, $jurusan, $prodi) {
    global $conn;
    
    $sql = "SELECT id FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return false;
    }
    
    $sql = "SELECT id FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return false;
    }
    
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    $sql = "INSERT INTO users (username, email, password, full_name, jurusan, prodi, role) 
            VALUES (?, ?, ?, ?, ?, ?, 'user')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $username, $email, $hashed_password, $full_name, $jurusan, $prodi);
    
    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}
?>