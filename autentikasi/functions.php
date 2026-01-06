<?php
function clean_input($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    if (isset($conn)) {
        $data = $conn->real_escape_string($data);
    }
    return $data;
}

//
//melakukan pengalihan halaman
function redirect($url) {
    header("Location: $url");
    exit();
}

//menampilkan pesan notifikasi
function display_message($type, $message) {
    return "<div class='alert alert-$type'>$message</div>";
}

//mengecek status login 
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

//mengecek peran pengguna
function is_admin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}
?>