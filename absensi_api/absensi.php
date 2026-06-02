<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
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

$user_id    = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
$matakuliah = isset($_POST['matakuliah']) ? trim($_POST['matakuliah']) : '';
$keterangan = isset($_POST['keterangan']) ? trim($_POST['keterangan']) : '';
$latitude   = isset($_POST['latitude']) ? trim($_POST['latitude']) : '';
$longitude  = isset($_POST['longitude']) ? trim($_POST['longitude']) : '';
$device_id  = isset($_POST['device_id']) ? trim($_POST['device_id']) : '';

if ($user_id <= 0 || empty($matakuliah) || empty($keterangan) || empty($latitude) || empty($longitude) || empty($device_id)) {
    echo json_encode(["status" => "error", "message" => "Data tidak lengkap"]);
    exit();
}

// 1. Master Security: Validasi HP (Device Binding dengan Auto-Binding)
$cek_device = mysqli_prepare($con, "SELECT device_id, role FROM users WHERE id = ?");
if ($cek_device) {
    mysqli_stmt_bind_param($cek_device, "i", $user_id);
    mysqli_stmt_execute($cek_device);
    $res_device = mysqli_stmt_get_result($cek_device);
    
    if (mysqli_num_rows($res_device) === 0) {
        echo json_encode(["status" => "error", "message" => "User tidak ditemukan"]);
        mysqli_stmt_close($cek_device);
        exit();
    }
    
    $row_device = mysqli_fetch_assoc($res_device);
    mysqli_stmt_close($cek_device);

    $db_device_id = trim($row_device['device_id']);
    
    // Auto-bind device: If empty in DB, bind it to this device ID
    if (empty($db_device_id) && $row_device['role'] === 'mahasiswa') {
        $updateQuery = "UPDATE users SET device_id = ? WHERE id = ?";
        $updateStmt = mysqli_prepare($con, $updateQuery);
        if ($updateStmt) {
            mysqli_stmt_bind_param($updateStmt, "si", $device_id, $user_id);
            mysqli_stmt_execute($updateStmt);
            mysqli_stmt_close($updateStmt);
            $db_device_id = $device_id;
        }
    }

    if ($row_device['role'] === 'mahasiswa' && $db_device_id !== $device_id) {
        echo json_encode(["status" => "error", "message" => "Ilegal: Perangkat Anda tidak cocok dengan akun ini!"]);
        exit();
    }
} else {
    echo json_encode(["status" => "error", "message" => "Database error"]);
    exit();
}

// 2. Anti-Duplikat Check (Check if attended today)
$checkQuery = "SELECT id FROM absensi WHERE user_id = ? AND matakuliah = ? AND tanggal = CURRENT_DATE()";
$checkStmt = mysqli_prepare($con, $checkQuery);
if ($checkStmt) {
    mysqli_stmt_bind_param($checkStmt, "is", $user_id, $matakuliah);
    mysqli_stmt_execute($checkStmt);
    $checkRes = mysqli_stmt_get_result($checkStmt);
    $alreadyAttended = mysqli_num_rows($checkRes) > 0;
    mysqli_stmt_close($checkStmt);

    if ($alreadyAttended) {
        echo json_encode(["status" => "error", "message" => "Gagal: Anda sudah absen di matkul ini hari ini!"]);
        exit();
    }
}

// 3. Simpan Foto (Unified location)
$foto_name = "ABSEN_" . $user_id . "_" . time() . ".jpg";
$path_dir = "../public/uploads/selfie/";
if (!is_dir($path_dir)) {
    mkdir($path_dir, 0777, true);
}

if (isset($_FILES['foto']) && move_uploaded_file($_FILES['foto']['tmp_name'], $path_dir . $foto_name)) {
    // 4. Insert Database
    $query = "INSERT INTO absensi (user_id, matakuliah, keterangan, latitude, longitude, foto, device_id, tanggal) 
              VALUES (?, ?, ?, ?, ?, ?, ?, CURRENT_DATE())";
    $stmt = mysqli_prepare($con, $query);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "issssss", $user_id, $matakuliah, $keterangan, $latitude, $longitude, $foto_name, $device_id);
        
        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(["status" => "success", "message" => "Absensi Berhasil"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Gagal Simpan Absensi: " . mysqli_stmt_error($stmt)]);
        }
        mysqli_stmt_close($stmt);
    } else {
        echo json_encode(["status" => "error", "message" => "Internal database error during statement prep"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Gagal Upload Foto"]);
}
?>