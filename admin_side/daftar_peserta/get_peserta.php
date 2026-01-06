
<?php
include "koneksi.php";

//mengambil data pengguna berdasarkan id

 $id = $_GET['id'];

 $db = mysqli_query($koneksi, "SELECT * FROM users WHERE id='$id'");
 $data = mysqli_fetch_assoc($db);

echo json_encode($data);
?>