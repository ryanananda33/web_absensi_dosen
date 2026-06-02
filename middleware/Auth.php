<?php
/**
 * Authentication Middleware
 */
class Auth {
    public static function checkToken() {
        $headers = apache_request_headers();
        $token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;

        if (!$token) {
            http_response_code(401);
            echo json_encode(["status" => "error", "message" => "Token required"]);
            exit;
        }

        // Verify token (simplified)
        return self::verifyToken($token);
    }

    public static function verifyToken($token) {
        // This is a simplified token verification
        // In production, use JWT library
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return false;
        }

        return true;
    }

    public static function requireRole($allowedRoles = []) {
        $headers = apache_request_headers();
        $role = isset($headers['X-User-Role']) ? $headers['X-User-Role'] : null;

        if (!in_array($role, $allowedRoles)) {
            http_response_code(403);
            echo json_encode(["status" => "error", "message" => "Access forbidden"]);
            exit;
        }

        return true;
    }
}
?>
