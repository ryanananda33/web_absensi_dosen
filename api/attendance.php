<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once(__DIR__ . '/../config/Database.php');
require_once(__DIR__ . '/../config/Response.php');
require_once(__DIR__ . '/../helpers/Validation.php');
require_once(__DIR__ . '/../helpers/Security.php');
require_once(__DIR__ . '/../models/Attendance.php');
require_once(__DIR__ . '/../models/User.php');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Parse JSON body if Content-Type is application/json
if (isset($_SERVER['CONTENT_TYPE']) && strpos(strtolower($_SERVER['CONTENT_TYPE']), 'application/json') !== false) {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    if (is_array($data)) {
        $_POST = array_merge($_POST, $data);
    }
}

$database = new Database();
$conn = $database->connect();

if (!$conn) {
    http_response_code(500);
    die(Response::error("Database connection failed", 500));
}

$method = $_SERVER['REQUEST_METHOD'];
$attendance = new Attendance($conn);
$userModel = new User($conn);

// Create attendance (POST)
if ($method === 'POST') {
    $data = $_POST;

    // Validation
    if (empty($data['user_id']) || empty($data['matakuliah']) || 
        empty($data['keterangan']) || empty($data['latitude']) || 
        empty($data['longitude']) || empty($data['device_id'])) {
        echo Response::error("Data tidak lengkap", 400);
        exit();
    }

    // Validate coordinates
    if (!Validation::validateCoordinates($data['latitude'], $data['longitude'])) {
        echo Response::error("Koordinat tidak valid", 400);
        exit();
    }

    // Verify device binding
    if (!Security::validateDeviceBinding($data['user_id'], $data['device_id'], $conn)) {
        echo Response::error("Perangkat tidak sesuai dengan akun", 403);
        exit();
    }

    // Check if user already attended today
    if ($attendance->checkTodayAttendance($data['user_id'], $data['matakuliah'])) {
        echo Response::error("Anda sudah absen untuk matakuliah ini hari ini", 409);
        exit();
    }

    // Handle file upload
    $fotoName = '';
    if (isset($_FILES['foto'])) {
        $uploadDir = __DIR__ . '/../public/uploads/selfie/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fotoName = 'ABSEN_' . $data['user_id'] . '_' . time() . '.jpg';
        if (!move_uploaded_file($_FILES['foto']['tmp_name'], $uploadDir . $fotoName)) {
            echo Response::error("Gagal upload foto", 400);
            exit();
        }
    }

    $attendanceData = [
        'user_id' => (int)$data['user_id'],
        'matakuliah' => Validation::sanitize($data['matakuliah']),
        'keterangan' => Validation::sanitize($data['keterangan']),
        'latitude' => (float)$data['latitude'],
        'longitude' => (float)$data['longitude'],
        'foto' => $fotoName,
        'device_id' => Validation::sanitize($data['device_id']),
        'tanggal' => date('Y-m-d')
    ];

    $result = $attendance->createAttendance($attendanceData);

    if ($result['success']) {
        echo Response::success($result['message'], [
            'attendance_id' => $result['attendance_id'],
            'timestamp' => date('Y-m-d H:i:s')
        ], 201);
    } else {
        echo Response::error($result['message'], 400);
    }
    exit();
}

// Get user attendance history (GET)
if ($method === 'GET' && isset($_GET['action']) && $_GET['action'] === 'history') {
    $userId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
    $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

    if ($userId === 0) {
        echo Response::error("User ID diperlukan", 400);
        exit();
    }

    $records = $attendance->getUserAttendance($userId, $limit, $offset);
    echo Response::success("Riwayat absensi berhasil diambil", $records);
    exit();
}

// Get attendance summary (GET)
if ($method === 'GET' && isset($_GET['action']) && $_GET['action'] === 'summary') {
    $userId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;

    if ($userId === 0) {
        echo Response::error("User ID diperlukan", 400);
        exit();
    }

    $summary = $attendance->getAttendanceSummary($userId);
    echo Response::success("Ringkasan absensi berhasil diambil", $summary);
    exit();
}

// Get all attendance (admin)
if ($method === 'GET' && isset($_GET['action']) && $_GET['action'] === 'all') {
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 100;
    $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

    $records = $attendance->getAllAttendance($limit, $offset);
    echo Response::success("Semua data absensi berhasil diambil", $records);
    exit();
}

// Delete attendance (DELETE)
if ($method === 'DELETE' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    if ($attendance->deleteAttendance($id)) {
        echo Response::success("Data absensi berhasil dihapus");
    } else {
        echo Response::error("Gagal menghapus data absensi", 400);
    }
    exit();
}

echo Response::error("Endpoint tidak ditemukan", 404);
?>
