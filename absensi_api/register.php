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

// 1. Tangkap semua data (11 Parameter POST)
$nim           = isset($_POST['nim_nik']) ? trim($_POST['nim_nik']) : '';
$nama          = isset($_POST['nama']) ? trim($_POST['nama']) : '';
$gender        = isset($_POST['gender']) ? trim($_POST['gender']) : null;
$jurusan       = isset($_POST['jurusan']) ? trim($_POST['jurusan']) : null;
$angkatan      = isset($_POST['angkatan']) ? trim($_POST['angkatan']) : null;
$kelas         = isset($_POST['kelas']) ? trim($_POST['kelas']) : null;
$semester      = isset($_POST['semester']) ? trim($_POST['semester']) : null;
$tempat_lahir  = isset($_POST['tempat_lahir']) ? trim($_POST['tempat_lahir']) : '';
$tanggal_lahir = isset($_POST['tanggal_lahir']) ? trim($_POST['tanggal_lahir']) : '';
$device_id     = isset($_POST['device_id']) ? trim($_POST['device_id']) : '';
$doc_type      = isset($_POST['doc_type']) ? trim($_POST['doc_type']) : '';

if (empty($nim) || empty($nama) || empty($tempat_lahir) || empty($tanggal_lahir) || empty($device_id)) {
    echo json_encode(["status" => "error", "message" => "Data tidak lengkap"]);
    exit();
}

// 2. Logika Master: Password Otomatis dari TTL (Tanpa Spasi)
$raw_pass = str_replace(' ', '', $tempat_lahir) . $tanggal_lahir;
$password_hashed = password_hash($raw_pass, PASSWORD_BCRYPT);

// 3. Penentuan Status Akun (KRS/SK = Pending)
$status = ($doc_type == "KTM (Kartu Tanda Mahasiswa)") ? "aktif" : "pending";

// 4. Pengaturan Folder Upload (Unified to public/uploads)
$path_ktm = "../public/uploads/ktm/";
$path_selfie = "../public/uploads/selfie/";
if (!is_dir($path_ktm)) { mkdir($path_ktm, 0777, true); }
if (!is_dir($path_selfie)) { mkdir($path_selfie, 0777, true); }

// 5. Simpan Foto KTM
$ktm_name = "";
if (isset($_FILES['foto_ktm'])) {
    $ktm_name = "KTM_" . $nim . "_" . time() . ".jpg";
    move_uploaded_file($_FILES['foto_ktm']['tmp_name'], $path_ktm . $ktm_name);
}

// 6. Simpan Foto Selfie (Master Biometrik)
$selfie_name = "";
if (isset($_FILES['foto_selfie'])) {
    $selfie_name = "AUTH_" . $nim . "_" . time() . ".jpg";
    move_uploaded_file($_FILES['foto_selfie']['tmp_name'], $path_selfie . $selfie_name);
}

// 7. Masukkan ke Database (Secure Prepared Statements)
$cekQuery = "SELECT id FROM users WHERE nim_nik = ?";
$cekStmt = mysqli_prepare($con, $cekQuery);
if ($cekStmt) {
    mysqli_stmt_bind_param($cekStmt, "s", $nim);
    mysqli_stmt_execute($cekStmt);
    $cekRes = mysqli_stmt_get_result($cekStmt);
    $exists = mysqli_num_rows($cekRes) > 0;
    mysqli_stmt_close($cekStmt);

    if ($exists) {
        echo json_encode(["status" => "error", "message" => "NIM sudah terdaftar!"]);
    } else {
        $query = "INSERT INTO users (nim_nik, nama, gender, jurusan, kelas, angkatan, semester, tempat_lahir, tanggal_lahir, password, device_id, doc_type, foto_ktm, foto_selfie, status_akun, role) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'mahasiswa')";
        $stmt = mysqli_prepare($con, $query);
        if ($stmt) {
            mysqli_stmt_bind_param(
                $stmt, 
                "sssssssssssssss", 
                $nim, $nama, $gender, $jurusan, $kelas, $angkatan, $semester, 
                $tempat_lahir, $tanggal_lahir, $password_hashed, $device_id, $doc_type, 
                $ktm_name, $selfie_name, $status
            );
            
            if (mysqli_stmt_execute($stmt)) {
                echo json_encode(["status" => "success", "message" => "Terdaftar! Password Anda: $raw_pass"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Gagal Simpan: " . mysqli_stmt_error($stmt)]);
            }
            mysqli_stmt_close($stmt);
        } else {
            echo json_encode(["status" => "error", "message" => "Internal database error during statement prep"]);
        }
    }
} else {
    echo json_encode(["status" => "error", "message" => "Internal database error"]);
}
?>