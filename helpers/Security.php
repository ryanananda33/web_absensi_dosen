<?php
/**
 * Security Helper Functions
 */
class Security {
    /**
     * Hash password dengan bcrypt
     */
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
    }

    /**
     * Verify password
     */
    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    /**
     * Generate random token
     */
    public static function generateToken($length = 32) {
        return bin2hex(random_bytes($length));
    }

    /**
     * Sanitize string untuk database
     */
    public static function sanitizeInput($data, $conn = null) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = self::sanitizeInput($value, $conn);
            }
            return $data;
        }

        if ($conn) {
            return mysqli_real_escape_string($conn, trim($data));
        }

        return htmlspecialchars(stripslashes(trim($data)), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Validate device binding untuk anti-fraud
     */
    public static function validateDeviceBinding($userId, $deviceId, $conn) {
        $query = "SELECT device_id FROM users WHERE id = ? AND role = 'mahasiswa'";
        $stmt = $conn->prepare($query);
        
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            return false;
        }

        $row = $result->fetch_assoc();
        $stmt->close();

        $dbDeviceId = trim($row['device_id']);

        // Auto-binding: If the database device_id is empty, bind it to this device ID
        if (empty($dbDeviceId)) {
            $updateQuery = "UPDATE users SET device_id = ? WHERE id = ?";
            $updateStmt = $conn->prepare($updateQuery);
            if ($updateStmt) {
                $updateStmt->bind_param("si", $deviceId, $userId);
                $updateStmt->execute();
                $updateStmt->close();
                return true;
            }
        }

        return $dbDeviceId === $deviceId;
    }

    /**
     * Generate JWT Token (simplified)
     */
    public static function generateJWT($userId, $role) {
        $header = json_encode(['alg' => 'HS256', 'typ' => 'JWT']);
        $payload = json_encode([
            'user_id' => $userId,
            'role' => $role,
            'iat' => time(),
            'exp' => time() + (24 * 60 * 60) // 24 jam
        ]);

        $header_b64 = rtrim(strtr(base64_encode($header), '+/', '-_'), '=');
        $payload_b64 = rtrim(strtr(base64_encode($payload), '+/', '-_'), '=');
        $signature = hash_hmac('sha256', "$header_b64.$payload_b64", "secret_key", true);
        $signature_b64 = rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');

        return "$header_b64.$payload_b64.$signature_b64";
    }
}
?>
