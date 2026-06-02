<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once(__DIR__ . '/../config/Database.php');
require_once(__DIR__ . '/../config/Response.php');
require_once(__DIR__ . '/../helpers/Validation.php');
require_once(__DIR__ . '/../helpers/Security.php');
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

$method = $_SERVER['REQUEST_METHOD'];
$database = new Database();
$conn = $database->connect();

if (!$conn) {
    http_response_code(500);
    die(Response::error("Database connection failed", 500));
}

// Login endpoint
if ($method === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $nim = isset($_POST['nim_nik']) ? Validation::sanitize($_POST['nim_nik']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (empty($nim) || empty($password)) {
        echo Response::error("NIM dan password harus diisi", 400);
        exit();
    }

    $user = new User($conn);
    $userData = $user->getUserByNIM($nim);

    if (!$userData) {
        echo Response::error("NIM tidak terdaftar", 401);
        exit();
    }

    if (!Security::verifyPassword($password, $userData['password'])) {
        echo Response::error("Password salah", 401);
        exit();
    }

    if ($userData['status_akun'] === 'pending') {
        echo Response::error("Akun Anda sedang menunggu verifikasi admin", 403);
        exit();
    }

    // Auto-bind device on login if database device_id is empty and device_id is passed in request
    $reqDeviceId = isset($_POST['device_id']) ? Validation::sanitize($_POST['device_id']) : '';
    if (!empty($reqDeviceId) && empty($userData['device_id']) && $userData['role'] === 'mahasiswa') {
        $updateQuery = "UPDATE users SET device_id = ? WHERE id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        if ($updateStmt) {
            $updateStmt->bind_param("si", $reqDeviceId, $userData['id']);
            $updateStmt->execute();
            $updateStmt->close();
            $userData['device_id'] = $reqDeviceId;
        }
    }

    echo Response::success("Login berhasil", [
        "id" => (int)$userData['id'],
        "nim_nik" => $userData['nim_nik'],
        "nama" => $userData['nama'],
        "gender" => $userData['gender'],
        "jurusan" => $userData['jurusan'],
        "kelas" => $userData['kelas'],
        "angkatan" => $userData['angkatan'],
        "semester" => $userData['semester'],
        "role" => $userData['role'],
        "device_id" => $userData['device_id']
    ]);
    exit();
}

// Get user profile
if ($method === 'GET' && isset($_GET['action']) && $_GET['action'] === 'profile') {
    $userId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;

    if ($userId === 0) {
        echo Response::error("User ID diperlukan", 400);
        exit();
    }

    $user = new User($conn);
    $userData = $user->getUserByID($userId);

    if (!$userData) {
        echo Response::error("User tidak ditemukan", 404);
        exit();
    }

    echo Response::success("Data user berhasil diambil", $userData);
    exit();
}

echo Response::error("Endpoint tidak ditemukan", 404);
?>
