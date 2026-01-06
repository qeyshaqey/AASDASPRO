<?php
 $password_to_hash = 'admin123';
 $hashed_password = password_hash($password_to_hash, PASSWORD_DEFAULT);

echo "Password Asli: " . $password_to_hash . "<br>";
echo "Hashed Password: " . $hashed_password;
?>