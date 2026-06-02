<?php
require_once(__DIR__ . '/../config/Database.php');

/**
 * User Model
 */
class User {
    private $conn;
    private $table = 'users';

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Ambil user by NIM
     */
    public function getUserByNIM($nim) {
        $query = "SELECT * FROM " . $this->table . " WHERE nim_nik = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("s", $nim);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $stmt->close();
            return false;
        }

        $user = $result->fetch_assoc();
        $stmt->close();

        return $user;
    }

    /**
     * Ambil user by ID
     */
    public function getUserByID($id) {
        $query = "SELECT id, nim_nik, nama, gender, jurusan, kelas, angkatan, semester, tempat_lahir, tanggal_lahir, device_id, role, status_akun FROM " . $this->table . " WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $stmt->close();
            return false;
        }

        $user = $result->fetch_assoc();
        $stmt->close();

        return $user;
    }

    /**
     * Register user baru
     */
    public function register($data) {
        $checkQuery = "SELECT nim_nik FROM " . $this->table . " WHERE nim_nik = ?";
        $stmt = $this->conn->prepare($checkQuery);
        
        if (!$stmt) {
            return ['success' => false, 'message' => 'Database error'];
        }

        $stmt->bind_param("s", $data['nim_nik']);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        if ($result->num_rows > 0) {
            return ['success' => false, 'message' => 'NIM sudah terdaftar'];
        }

        $query = "INSERT INTO " . $this->table . " 
                  (nim_nik, nama, gender, jurusan, kelas, angkatan, semester, 
                   tempat_lahir, tanggal_lahir, password, device_id, doc_type, 
                   foto_ktm, foto_selfie, status_akun, role) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);

        if (!$stmt) {
            return ['success' => false, 'message' => 'Preparation error'];
        }

        $stmt->bind_param(
            "ssssssssssssssss",
            $data['nim_nik'],
            $data['nama'],
            $data['gender'],
            $data['jurusan'],
            $data['kelas'],
            $data['angkatan'],
            $data['semester'],
            $data['tempat_lahir'],
            $data['tanggal_lahir'],
            $data['password'],
            $data['device_id'],
            $data['doc_type'],
            $data['foto_ktm'],
            $data['foto_selfie'],
            $data['status_akun'],
            $data['role']
        );

        if ($stmt->execute()) {
            $stmt->close();
            return ['success' => true, 'message' => 'Registrasi berhasil', 'user_id' => $this->conn->insert_id];
        }

        $error = $stmt->error;
        $stmt->close();
        return ['success' => false, 'message' => 'Database error: ' . $error];
    }

    /**
     * Update user
     */
    public function updateUser($id, $data) {
        $fields = [];
        $types = "";
        $values = [];

        foreach ($data as $key => $value) {
            $fields[] = "$key = ?";
            $values[] = $value;
            $types .= is_int($value) ? 'i' : 's';
        }

        $values[] = $id;
        $types .= 'i';

        $query = "UPDATE " . $this->table . " SET " . implode(", ", $fields) . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param($types, ...$values);
        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }

    /**
     * Get all users (for admin)
     */
    public function getAllUsers($role = null) {
        $query = "SELECT id, nim_nik, nama, jurusan, kelas, role, status_akun FROM " . $this->table;

        if ($role) {
            $query .= " WHERE role = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("s", $role);
        } else {
            $stmt = $this->conn->prepare($query);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $users = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $users;
    }

    /**
     * Delete user by ID
     */
    public function deleteUser($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }
}
?>
