<?php
error_reporting(0);
ini_set('display_errors', 0);
require_once('koneksi.php');

$kelas = isset($_GET['kelas']) ? $_GET['kelas'] : '';
$hari  = isset($_GET['hari']) ? $_GET['hari'] : '';

if (empty($kelas) || empty($hari)) {
    header('Content-Type: application/json');
    echo json_encode([]);
    exit();
}

$query = "SELECT * FROM jadwal WHERE kelas = '$kelas' AND hari = '$hari' ORDER BY jam_mulai ASC";
$result = mysqli_query($con, $query);

$jadwal_array = array();
while ($row = mysqli_fetch_assoc($result)) {
    // Cari nama mata kuliah di kolom 'mata_kuliah' atau 'matakuliah'
    $nama_matkul = isset($row['mata_kuliah']) ? $row['mata_kuliah'] : (isset($row['matakuliah']) ? $row['matakuliah'] : "Tanpa Nama");
    
    $jadwal_array[] = array(
        "id" => (int)$row['id'],
        "mata_kuliah" => $nama_matkul,
        "jam_mulai" => $row['jam_mulai'],
        "kelas" => $row['kelas'],
        "hari" => $row['hari']
    );
}

header('Content-Type: application/json');
echo json_encode($jadwal_array);
?>