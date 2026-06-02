<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once(__DIR__ . '/../config/Database.php');
require_once(__DIR__ . '/../config/Response.php');
require_once(__DIR__ . '/../models/Schedule.php');
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
        $_GET = array_merge($_GET, $data);
    }
}

$database = new Database();
$conn = $database->connect();

if (!$conn) {
    http_response_code(500);
    die(Response::error("Database connection failed", 500));
}

$method = $_SERVER['REQUEST_METHOD'];
$schedule = new Schedule($conn);

// Get all schedules
if ($method === 'GET' && (!isset($_GET['action']) || $_GET['action'] === 'all')) {
    $schedules = $schedule->getAllSchedules();
    echo Response::success("Jadwal berhasil diambil", $schedules);
    exit();
}

// Get today's schedule
if ($method === 'GET' && isset($_GET['action']) && $_GET['action'] === 'today') {
    // Optional: filter by kelas or by user_id
    $kelas = isset($_GET['kelas']) ? $_GET['kelas'] : null;
    $userId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;

    if ($userId > 0) {
        $userModel = new User($conn);
        $userData = $userModel->getUserByID($userId);
        if ($userData && isset($userData['kelas'])) {
            $kelas = $userData['kelas'];
        }
    }

    $schedules = $schedule->getTodaySchedule($kelas);
    echo Response::success("Jadwal hari ini berhasil diambil", $schedules);
    exit();
}

// Get schedule by day
if ($method === 'GET' && isset($_GET['action']) && $_GET['action'] === 'by_day') {
    $day = isset($_GET['day']) ? $_GET['day'] : '';
    
    if (empty($day)) {
        echo Response::error("Hari diperlukan", 400);
        exit();
    }

    $schedules = $schedule->getScheduleByDay($day);
    echo Response::success("Jadwal berhasil diambil", $schedules);
    exit();
}

// Create schedule (admin)
if ($method === 'POST') {
    $data = $_POST;

    if (empty($data['matakuliah']) || empty($data['hari']) || empty($data['jam_mulai'])) {
        echo Response::error("Data tidak lengkap", 400);
        exit();
    }

    // allowed optional fields: kelas, jurusan, angkatan
    $result = $schedule->createSchedule($data);

    if ($result['success']) {
        echo Response::success($result['message'], null, 201);
    } else {
        echo Response::error($result['message'], 400);
    }
    exit();
}

// Update schedule (admin)
if ($method === 'PUT' && isset($_GET['id'])) {
    $input = file_get_contents("php://input");
    if (isset($_SERVER['CONTENT_TYPE']) && strpos(strtolower($_SERVER['CONTENT_TYPE']), 'application/json') !== false) {
        $data = json_decode($input, true);
    } else {
        parse_str($input, $data);
    }
    $id = (int)$_GET['id'];

    if (empty($data['matakuliah']) || empty($data['hari']) || empty($data['jam_mulai'])) {
        echo Response::error("Data tidak lengkap", 400);
        exit();
    }

    // accept optional kelas/jurusan/angkatan in $data
    $result = $schedule->updateSchedule($id, $data);

    if ($result['success']) {
        echo Response::success($result['message']);
    } else {
        echo Response::error($result['message'], 400);
    }
    exit();
}

// Delete schedule (admin)
if ($method === 'DELETE' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $result = $schedule->deleteSchedule($id);

    if ($result['success']) {
        echo Response::success($result['message']);
    } else {
        echo Response::error($result['message'], 400);
    }
    exit();
}

echo Response::error("Endpoint tidak ditemukan", 404);
?>
