<?php
/**
 * Database Configuration
 * Secure database connection class
 */
class Database {
    private $host = "localhost";
    private $db_name = "absensi_db";
    private $user = "root";
    private $password = "";
    private $conn;

    public function connect() {
        $this->conn = mysqli_connect(
            $this->host,
            $this->user,
            $this->password,
            $this->db_name
        );

        if (mysqli_connect_errno()) {
            die(json_encode([
                "status" => "error",
                "message" => "Database connection error: " . mysqli_connect_error()
            ]));
        }

        // Set charset
        mysqli_set_charset($this->conn, 'utf8mb4');
        date_default_timezone_set('Asia/Jakarta');

        return $this->conn;
    }

    public function getConnection() {
        return $this->conn;
    }

    public function closeConnection() {
        if ($this->conn) {
            mysqli_close($this->conn);
        }
    }
}
?>
