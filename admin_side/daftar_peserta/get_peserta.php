<?php
include "koneksi.php";

 $id = $_GET['id'];

 $db = mysqli_query($koneksi, "SELECT * FROM users WHERE id='$id'");
 $data = mysqli_fetch_assoc($db);

echo json_encode($data);
?>