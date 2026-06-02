<?php
/**
 * API Response Handler
 */
class Response {
    public static function success($message = "Success", $data = null, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        
        $response = [
            "status" => "success",
            "message" => $message
        ];
        
        if ($data !== null) {
            $response["data"] = $data;
        }
        
        return json_encode($response, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    public static function error($message = "Error", $statusCode = 400) {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        
        return json_encode([
            "status" => "error",
            "message" => $message
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    public static function validation($errors) {
        http_response_code(422);
        header('Content-Type: application/json; charset=utf-8');
        
        return json_encode([
            "status" => "validation_error",
            "message" => "Validation failed",
            "errors" => $errors
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}
?>
