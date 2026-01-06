<?php
// Memanggil file session untuk memastikan session aktif
require_once 'session.php';

// Memanggil file functions yang berisi helper seperti redirect()
require_once 'functions.php';

// Menghapus seluruh data session yang sedang aktif
session_unset();

// Menghancurkan session agar user benar-benar logout
session_destroy();

// Mengarahkan user kembali ke halaman login
redirect('login.php');
?>
