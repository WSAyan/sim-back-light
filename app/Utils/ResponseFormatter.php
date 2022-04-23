<?php


namespace App\Utils;

define("ERROR_TYPE_VALIDATION", 422);
define("ERROR_TYPE_NOT_FOUND", 404);
define("ERROR_TYPE_COMMON", 500);
define("ERROR_TYPE_UNAUTHORIZED", 401);
define("SUCCESS_TYPE_CREATE", 201);
define("SUCCESS_TYPE_OK", 200);

define("COMMON_ERROR_MESSAGE", "Something went wrong!");
define("VALIDATION_ERROR_MESSAGE", "Validation failed!");

class ResponseFormatter
{
    public static function errorResponse($type, $message, $errors = null)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], $type);
    }

    public static function successResponse($type, $message, $data = null, $dataName = "data", $hasData = true)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            $dataName => $data
        ], $type);
    }
}
