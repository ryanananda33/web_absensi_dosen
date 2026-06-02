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

$database = new Database();
$conn = $database->connect();

if (!$conn) {
    http_response_code(500);
    die(Response::error("Database connection failed", 500));
}

$method = $_SERVER['REQUEST_METHOD'];
$user = new User($conn);

// Handle POST actions: register, update, delete
if ($method === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : 'register';

    // Delete user
    if ($action === 'delete') {
        $userId = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
        if ($userId === 0) {
            echo Response::error("User ID diperlukan", 400);
            exit();
        }

        if ($user->deleteUser($userId)) {
            echo Response::success("User berhasil dihapus");
        } else {
            echo Response::error("Gagal menghapus user", 400);
        }
        exit();
    }

    // Update user
    if ($action === 'update') {
        $data = $_POST;
        $userId = isset($data['user_id']) ? (int)$data['user_id'] : 0;
        if ($userId === 0) {
            echo Response::error("User ID diperlukan", 400);
            exit();
        }

        $allowed = ['nama','gender','jurusan','kelas','angkatan','semester','tempat_lahir','tanggal_lahir','device_id','doc_type','status_akun','role','nim_nik'];
        $updateData = [];
        foreach ($allowed as $f) {
            if (isset($data[$f])) {
                $updateData[$f] = Validation::sanitize($data[$f]);
            }
        }

        // handle optional file uploads for update
        if (isset($_FILES['foto_ktm'])) {
            $uploadDir = __DIR__ . '/../public/uploads/ktm/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            $fotoKTMName = 'KTM_' . ($updateData['nim_nik'] ?? $userId) . '_' . time() . '.jpg';
            if (move_uploaded_file($_FILES['foto_ktm']['tmp_name'], $uploadDir . $fotoKTMName)) {
                $updateData['foto_ktm'] = $fotoKTMName;
            }
        }
        if (isset($_FILES['foto_selfie'])) {
            $uploadDir = __DIR__ . '/../public/uploads/selfie/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            $fotoSelfieName = 'SELFIE_' . ($updateData['nim_nik'] ?? $userId) . '_' . time() . '.jpg';
            if (move_uploaded_file($_FILES['foto_selfie']['tmp_name'], $uploadDir . $fotoSelfieName)) {
                $updateData['foto_selfie'] = $fotoSelfieName;
            }
        }

        $res = $user->updateUser($userId, $updateData);
        if ($res) {
            echo Response::success("User berhasil diperbarui");
        } else {
            echo Response::error("Gagal memperbarui user", 400);
        }
        exit();
    }

    // Default: register new user
    $data = $_POST;

    // Validation
    if (empty($data['nim_nik']) || empty($data['nama']) || empty($data['tanggal_lahir']) || 
        empty($data['tempat_lahir']) || empty($data['device_id'])) {
        echo Response::error("Data tidak lengkap", 400);
        exit();
    }

    // Determine role and account status
    $role = isset($data['role']) ? strtolower(trim($data['role'])) : 'mahasiswa';
    if (!in_array($role, ['mahasiswa', 'dosen'])) {
        echo Response::error("Role tidak valid. Gunakan 'mahasiswa' atau 'dosen'", 400);
        exit();
    }

    // Generate password from birthplace + birthdate
    $rawPassword = str_replace(' ', '', $data['tempat_lahir']) . $data['tanggal_lahir'];
    $hashedPassword = Security::hashPassword($rawPassword);

    // Determine account status
    if ($role === 'dosen') {
        $status = 'aktif';
        $data['doc_type'] = isset($data['doc_type']) ? $data['doc_type'] : 'Dosen';
    } else {
        $status = (isset($data['doc_type']) && $data['doc_type'] === 'KTM (Kartu Tanda Mahasiswa)') ? 'aktif' : 'pending';
    }

    // Handle file uploads
    $fotoKTM = '';
    $fotoSelfie = '';

    if (isset($_FILES['foto_ktm'])) {
        $uploadDir = __DIR__ . '/../public/uploads/ktm/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fotoKTMName = 'KTM_' . $data['nim_nik'] . '_' . time() . '.jpg';
        if (move_uploaded_file($_FILES['foto_ktm']['tmp_name'], $uploadDir . $fotoKTMName)) {
            $fotoKTM = $fotoKTMName;
        }
    }

    if (isset($_FILES['foto_selfie'])) {
        $uploadDir = __DIR__ . '/../public/uploads/selfie/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fotoSelfieName = 'SELFIE_' . $data['nim_nik'] . '_' . time() . '.jpg';
        if (move_uploaded_file($_FILES['foto_selfie']['tmp_name'], $uploadDir . $fotoSelfieName)) {
            $fotoSelfie = $fotoSelfieName;
        }
    }

    $registrationData = [
        'nim_nik' => Validation::sanitize($data['nim_nik']),
        'nama' => Validation::sanitize($data['nama']),
        'gender' => isset($data['gender']) ? Validation::sanitize($data['gender']) : null,
        'jurusan' => isset($data['jurusan']) ? Validation::sanitize($data['jurusan']) : null,
        'kelas' => isset($data['kelas']) ? Validation::sanitize($data['kelas']) : null,
        'angkatan' => isset($data['angkatan']) ? Validation::sanitize($data['angkatan']) : null,
        'semester' => isset($data['semester']) ? Validation::sanitize($data['semester']) : null,
        'tempat_lahir' => Validation::sanitize($data['tempat_lahir']),
        'tanggal_lahir' => $data['tanggal_lahir'],
        'password' => $hashedPassword,
        'device_id' => Validation::sanitize($data['device_id']),
        'doc_type' => isset($data['doc_type']) ? Validation::sanitize($data['doc_type']) : ($role === 'dosen' ? 'Dosen' : null),
        'foto_ktm' => $fotoKTM,
        'foto_selfie' => $fotoSelfie,
        'status_akun' => $status,
        'role' => $role
    ];

    $result = $user->register($registrationData);

    if ($result['success']) {
        echo Response::success($result['message'], [
            'user_id' => $result['user_id'],
            'default_password' => $rawPassword,
            'status' => $status
        ], 201);
    } else {
        echo Response::error($result['message'], 400);
    }
    exit();
}

// Get all users (admin only)
if ($method === 'GET' && isset($_GET['action']) && $_GET['action'] === 'list') {
    $role = isset($_GET['role']) ? $_GET['role'] : null;
    $users = $user->getAllUsers($role);
    echo Response::success("Daftar user berhasil diambil", $users);
    exit();
}

echo Response::error("Endpoint tidak ditemukan", 404);
?>
