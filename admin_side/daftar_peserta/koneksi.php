<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "db_rapat";
$koneksi = mysqli_connect(
    $host, $user, $pass, $db);
    if(!$koneksi){
        echo "Gagal konek: " . die(mysqli_error($koneksi));
}