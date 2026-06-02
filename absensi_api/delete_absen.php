<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once('koneksi.php');

// Parse JSON body if Content-Type is application/json
if (isset($_SERVER['CONTENT_TYPE']) && strpos(strtolower($_SERVER['CONTENT_TYPE']), 'application/json') !== false) {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    if (is_array($data)) {
        $_POST = array_merge($_POST, $data);
    }
}

$id_absensi = isset($_POST['id_absensi']) ? (int)$_POST['id_absensi'] : 0;

if ($id_absensi <= 0) {
    echo json_encode(["status" => "error", "message" => "ID Absensi tidak valid"]);
    exit();
}

$query = "DELETE FROM absensi WHERE id = ?";
$stmt = mysqli_prepare($con, $query);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $id_absensi);
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(["status" => "success", "message" => "Data berhasil dihapus"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Gagal menghapus data: " . mysqli_stmt_error($stmt)]);
    }
    mysqli_stmt_close($stmt);
} else {
    echo json_encode(["status" => "error", "message" => "Internal database error"]);
}
?>