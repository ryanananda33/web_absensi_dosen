<?php
require_once(__DIR__ . '/../config/Database.php');

/**
 * Attendance Model
 */
class Attendance {
    private $conn;
    private $table = 'absensi';

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Create attendance record
     */
    public function createAttendance($data) {
        $query = "INSERT INTO " . $this->table . " 
                  (user_id, matakuliah, keterangan, latitude, longitude, foto, device_id, tanggal) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);

        if (!$stmt) {
            return ['success' => false, 'message' => 'Preparation error'];
        }

        $stmt->bind_param(
            "issddss",
            $data['user_id'],
            $data['matakuliah'],
            $data['keterangan'],
            $data['latitude'],
            $data['longitude'],
            $data['foto'],
            $data['device_id'],
            $data['tanggal']
        );

        if ($stmt->execute()) {
            $stmt->close();
            return ['success' => true, 'message' => 'Absensi berhasil', 'attendance_id' => $this->conn->insert_id];
        }

        $error = $stmt->error;
        $stmt->close();
        return ['success' => false, 'message' => 'Database error: ' . $error];
    }

    /**
     * Get user attendance history
     */
    public function getUserAttendance($userId, $limit = 50, $offset = 0) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE user_id = ? 
                  ORDER BY waktu_absen DESC 
                  LIMIT ? OFFSET ?";

        $stmt = $this->conn->prepare($query);

        if (!$stmt) {
            return [];
        }

        $stmt->bind_param("iii", $userId, $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        $records = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $records;
    }

    /**
     * Get attendance by date range
     */
    public function getAttendanceByDateRange($startDate, $endDate, $userId = null) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE tanggal BETWEEN ? AND ?";

        if ($userId) {
            $query .= " AND user_id = ?";
        }

        $query .= " ORDER BY waktu_absen DESC";

        $stmt = $this->conn->prepare($query);

        if (!$stmt) {
            return [];
        }

        if ($userId) {
            $stmt->bind_param("ssi", $startDate, $endDate, $userId);
        } else {
            $stmt->bind_param("ss", $startDate, $endDate);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $records = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $records;
    }

    /**
     * Check if user already attended today
     */
    public function checkTodayAttendance($userId, $matakuliah) {
        $today = date('Y-m-d');
        $query = "SELECT id FROM " . $this->table . " 
                  WHERE user_id = ? AND matakuliah = ? AND tanggal = ? 
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("iss", $userId, $matakuliah, $today);
        $stmt->execute();
        $result = $stmt->get_result();

        $exists = $result->num_rows > 0;
        $stmt->close();

        return $exists;
    }

    /**
     * Get all attendance (for admin)
     */
    public function getAllAttendance($limit = 100, $offset = 0) {
        $query = "SELECT a.*, u.nama, u.nim_nik FROM " . $this->table . " a 
                  JOIN users u ON a.user_id = u.id 
                  ORDER BY a.waktu_absen DESC 
                  LIMIT ? OFFSET ?";

        $stmt = $this->conn->prepare($query);

        if (!$stmt) {
            return [];
        }

        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        $records = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $records;
    }

    /**
     * Delete attendance record
     */
    public function deleteAttendance($id) {
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

    /**
     * Get attendance summary by subject
     */
    public function getAttendanceSummary($userId) {
        $query = "SELECT 
                    matakuliah,
                    COUNT(*) as total,
                    SUM(CASE WHEN keterangan = 'Hadir' THEN 1 ELSE 0 END) as hadir,
                    SUM(CASE WHEN keterangan = 'Izin' THEN 1 ELSE 0 END) as izin,
                    SUM(CASE WHEN keterangan = 'Sakit' THEN 1 ELSE 0 END) as sakit,
                    SUM(CASE WHEN keterangan = 'Alfa' THEN 1 ELSE 0 END) as alfa,
                    SUM(CASE WHEN keterangan = 'Terlambat' THEN 1 ELSE 0 END) as terlambat
                  FROM " . $this->table . " 
                  WHERE user_id = ? 
                  GROUP BY matakuliah";

        $stmt = $this->conn->prepare($query);

        if (!$stmt) {
            return [];
        }

        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $records = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $records;
    }
}
?>
