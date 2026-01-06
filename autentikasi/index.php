<?php
//
//memeriksa status login pengguna
require_once 'session.php';
require_once 'functions.php';

if (!is_logged_in()) {
    redirect('login.php');
} else {
    redirect('../admin_side/dashboard/dashboard.php');
}
?>