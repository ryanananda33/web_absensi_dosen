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

$nim_nik  = isset($_POST['nim_nik']) ? trim($_POST['nim_nik']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$req_device_id = isset($_POST['device_id']) ? trim($_POST['device_id']) : '';

if (empty($nim_nik) || empty($password)) {
    echo json_encode(["status" => "error", "message" => "NIM dan password harus diisi"]);
    exit();
}

$query = "SELECT * FROM users WHERE nim_nik = ? LIMIT 1";
$stmt = mysqli_prepare($con, $query);
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "s", $nim_nik);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        
        if (password_verify($password, $row['password'])) {
            if ($row['status_akun'] == 'pending') {
                echo json_encode(["status" => "error", "message" => "Akun Anda sedang diverifikasi Admin"]);
                exit();
            }

            // Auto-bind device on login if database device_id is empty and device_id is provided in request
            if (!empty($req_device_id) && empty($row['device_id']) && $row['role'] === 'mahasiswa') {
                $updateQuery = "UPDATE users SET device_id = ? WHERE id = ?";
                $updateStmt = mysqli_prepare($con, $updateQuery);
                if ($updateStmt) {
                    mysqli_stmt_bind_param($updateStmt, "si", $req_device_id, $row['id']);
                    mysqli_stmt_execute($updateStmt);
                    mysqli_stmt_close($updateStmt);
                    $row['device_id'] = $req_device_id;
                }
            }

            echo json_encode([
                "status" => "success",
                "message" => "Login Berhasil",
                "data" => [
                    "id" => (int)$row['id'],
                    "nim_nik" => $row['nim_nik'],
                    "nama" => $row['nama'],
                    "gender" => $row['gender'],
                    "jurusan" => $row['jurusan'],
                    "kelas" => $row['kelas'],
                    "angkatan" => $row['angkatan'],
                    "semester" => $row['semester'],
                    "role" => $row['role'],
                    "device_id" => $row['device_id'],
                    "jalur" => isset($row['jalur']) ? $row['jalur'] : null
                ]
            ]);
        } else {
            echo json_encode(["status" => "error", "message" => "Password Salah"]);
        }
    } else {
        mysqli_stmt_close($stmt);
        echo json_encode(["status" => "error", "message" => "NIM belum terdaftar"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Internal server error: database statement error"]);
}
?>