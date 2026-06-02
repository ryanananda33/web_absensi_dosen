<?php
/**
 * Input Validation Helper
 */
class Validation {
    public static function sanitize($data) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = self::sanitize($value);
            }
            return $data;
        }
        
        return htmlspecialchars(stripslashes(trim($data)), ENT_QUOTES, 'UTF-8');
    }

    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function validatePassword($password) {
        return strlen($password) >= 6;
    }

    public static function validateNIM($nim) {
        return preg_match('/^[0-9]{10,12}$/', $nim);
    }

    public static function validatePhone($phone) {
        return preg_match('/^(\+62|0)[0-9]{9,12}$/', $phone);
    }

    public static function validateCoordinates($lat, $lng) {
        return is_numeric($lat) && is_numeric($lng) && 
               $lat >= -90 && $lat <= 90 && 
               $lng >= -180 && $lng <= 180;
    }

    public static function validateDate($date, $format = 'Y-m-d') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    public static function validateFileUpload($file, $allowedTypes = [], $maxSize = 5242880) {
        if (!isset($file) || $file['error'] != UPLOAD_ERR_OK) {
            return false;
        }

        if ($file['size'] > $maxSize) {
            return false;
        }

        if (!empty($allowedTypes)) {
            $fileType = mime_content_type($file['tmp_name']);
            if (!in_array($fileType, $allowedTypes)) {
                return false;
            }
        }

        return true;
    }
}
?>
