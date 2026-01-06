<?php
session_start();
include "koneksi.php";

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "NO_SESSION"]);
    exit();
}

$id = $_SESSION['user_id'];

$q = mysqli_query($koneksi, "SELECT * FROM users WHERE id='$id'");
$user = mysqli_fetch_assoc($q);

if (!$user) {
    echo json_encode(["success" => false, "message" => "USER_NOT_FOUND"]);
    exit();
}

$username = mysqli_real_escape_string($koneksi, $_POST['username']);
$nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
$email = mysqli_real_escape_string($koneksi, $_POST['email']);
$jurusan = mysqli_real_escape_string($koneksi, $_POST['jurusan']);
$prodi = mysqli_real_escape_string($koneksi, $_POST['prodi']);

$oldPass = $_POST['passwordOld'] ?? "";
$newPass = $_POST['passwordNew'] ?? "";
$confirm = $_POST['passwordConfirm'] ?? "";

if ($username !== $user['username']) {
    $checkUsername = mysqli_query($koneksi, "SELECT id FROM users WHERE username='$username' AND id != '$id'");
    if (mysqli_num_rows($checkUsername) > 0) {
        echo json_encode(["success" => false, "message" => "USERNAME_TAKEN"]);
        exit();
    }
}

$fotoBaru = $user['foto'];

if (!empty($_FILES['foto']['name'])) {
    $folderUpload = __DIR__ . "/uploads/";

    if (!is_dir($folderUpload)) {
        mkdir($folderUpload, 0777, true);
    }

    $namaFile = time() . "_" . basename($_FILES['foto']['name']);
    $tmp = $_FILES['foto']['tmp_name'];
    $targetFile = $folderUpload . $namaFile;

    if ($_FILES['foto']['error'] !== 0) {
        echo json_encode(["success" => false, "message" => "UPLOAD_ERROR"]);
        exit();
    }

    if (!move_uploaded_file($tmp, $targetFile)) {
        echo json_encode(["success" => false, "message" => "MOVE_FAILED"]);
        exit();
    }

    $fotoBaru = $namaFile;
}

$updatePasswordSQL = "";

if ($oldPass != "" || $newPass != "" || $confirm != "") {
    if ($oldPass == "") {
        echo json_encode(["success" => false, "message" => "OLD_EMPTY"]);
        exit();
    }

    if (!password_verify($oldPass, $user['password'])) {
        echo json_encode(["success" => false, "message" => "WRONG_OLD"]);
        exit();
    }

    if ($newPass != $confirm) {
        echo json_encode(["success" => false, "message" => "NEW_MISMATCH"]);
        exit();
    }

    $hashedNewPassword = password_hash($newPass, PASSWORD_DEFAULT);
    $updatePasswordSQL = ", password='$hashedNewPassword'";
}

$sql = "UPDATE users SET 
            username='$username',
            full_name='$nama',
            email='$email',
            jurusan='$jurusan',
            prodi='$prodi',
            foto='$fotoBaru'
            $updatePasswordSQL
        WHERE id='$id'";

if (mysqli_query($koneksi, $sql)) {
    $_SESSION['username'] = $username;
    $_SESSION['full_name'] = $nama;
    $_SESSION['email'] = $email;
    $_SESSION['jurusan'] = $jurusan;
    $_SESSION['prodi'] = $prodi;
    $_SESSION['foto'] = $fotoBaru;
    
    echo json_encode(["success" => true, "message" => "OK"]);
} else {
    echo json_encode(["success" => false, "message" => "DB_ERROR"]);
}
?>