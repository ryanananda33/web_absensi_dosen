<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "absensi_db";

$con = mysqli_connect($host, $user, $pass, $db);

if (mysqli_connect_errno()) {
    echo "Gagal terhubung ke MySQL: " . mysqli_connect_error();
}
date_default_timezone_set('Asia/Jakarta');
?>