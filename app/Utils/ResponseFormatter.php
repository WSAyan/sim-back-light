<?php


namespace App\Utils;

define("ERROR_TYPE_VALIDATION", 422);
define("ERROR_TYPE_COMMON", 500);

define("COMMON_ERROR_MESSAGE", "Something went wrong!");
define("VALIDATION_ERROR_MESSAGE", "Validation failed!");

class ResponseFormatter
{
    public static function errorResponse($type, $message, $errors)
    {
        switch ($type) {
            case ERROR_TYPE_VALIDATION:
                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'errors' => $errors
                ], ERROR_TYPE_VALIDATION);

            case ERROR_TYPE_COMMON:
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], ERROR_TYPE_COMMON);
        }
    }
}
