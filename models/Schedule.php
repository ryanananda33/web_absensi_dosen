<?php
require_once(__DIR__ . '/../config/Database.php');

/**
 * Schedule Model
 */
class Schedule {
    private $conn;
    private $table = 'jadwal';

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Get all schedules
     */
    public function getAllSchedules() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY hari, jam_mulai";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        $schedules = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $schedules;
    }

    /**
     * Get schedule by day
     */
    public function getScheduleByDay($day, $kelas = null) {
        // If $kelas provided, return schedules for that class or general ones (kelas IS NULL or empty)
        if ($kelas) {
            $query = "SELECT * FROM " . $this->table . " WHERE hari = ? AND (kelas = ? OR kelas IS NULL OR kelas = '') ORDER BY jam_mulai";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("ss", $day, $kelas);
        } else {
            $query = "SELECT * FROM " . $this->table . " WHERE hari = ? ORDER BY jam_mulai";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("s", $day);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $schedules = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $schedules;
    }

    /**
     * Get today's schedule
     */
    public function getTodaySchedule($kelas = null) {
        $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $today = $days[date('w')];

        return $this->getScheduleByDay($today, $kelas);
    }

    /**
     * Create schedule
     */
    public function createSchedule($data) {
        // optional fields: kelas, jurusan, angkatan
        $query = "INSERT INTO " . $this->table . " (matakuliah, hari, jam_mulai, ruangan, tipe, kelas, jurusan, angkatan) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $kelas = isset($data['kelas']) ? $data['kelas'] : null;
        $jurusan = isset($data['jurusan']) ? $data['jurusan'] : null;
        $angkatan = isset($data['angkatan']) ? $data['angkatan'] : null;
        $stmt->bind_param("ssssssss", $data['matakuliah'], $data['hari'], $data['jam_mulai'], $data['ruangan'], $data['tipe'], $kelas, $jurusan, $angkatan);

        if ($stmt->execute()) {
            $stmt->close();
            return ['success' => true, 'message' => 'Jadwal berhasil ditambahkan'];
        }

        $stmt->close();
        return ['success' => false, 'message' => 'Gagal menambahkan jadwal'];
    }

    /**
     * Update schedule
     */
    public function updateSchedule($id, $data) {
        $query = "UPDATE " . $this->table . " SET matakuliah = ?, hari = ?, jam_mulai = ?, ruangan = ?, tipe = ?, kelas = ?, jurusan = ?, angkatan = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $kelas = isset($data['kelas']) ? $data['kelas'] : null;
        $jurusan = isset($data['jurusan']) ? $data['jurusan'] : null;
        $angkatan = isset($data['angkatan']) ? $data['angkatan'] : null;
        $stmt->bind_param("ssssssssi", $data['matakuliah'], $data['hari'], $data['jam_mulai'], $data['ruangan'], $data['tipe'], $kelas, $jurusan, $angkatan, $id);

        if ($stmt->execute()) {
            $stmt->close();
            return ['success' => true, 'message' => 'Jadwal berhasil diupdate'];
        }

        $stmt->close();
        return ['success' => false, 'message' => 'Gagal mengupdate jadwal'];
    }

    /**
     * Delete schedule
     */
    public function deleteSchedule($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $stmt->close();
            return ['success' => true, 'message' => 'Jadwal berhasil dihapus'];
        }

        $stmt->close();
        return ['success' => false, 'message' => 'Gagal menghapus jadwal'];
    }
}
?>
