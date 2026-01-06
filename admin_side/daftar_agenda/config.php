<?php
error_reporting(0);
ini_set('display_errors', 0);

$host = "localhost";
$user = "root";
$pass = ""; 
$dbname = "db_rapat";

try {
    $conn = new mysqli($host, $user, $pass, $dbname);
    
    if ($conn->connect_error) {
        error_log("Database connection failed: " . $conn->connect_error);
        $conn = null;
    } else {
        $conn->set_charset("utf8mb4");
    }
} catch (Exception $e) {
    error_log("Database connection exception: " . $e->getMessage());
    $conn = null;
}

return $conn;
?>