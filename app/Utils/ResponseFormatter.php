<?php


namespace App\Utils;

define("ERROR_TYPE_VALIDATION", 422);
define("ERROR_TYPE_COMMON", 500);
define("SUCCESS_TYPE_CREATE", 201);
define("SUCCESS_TYPE_OK", 200);

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

    public static function successResponse($type, $message, $data, $dataName, $hasData)
    {
        if ($hasData == true) {
            return response()->json([
                'success' => true,
                'message' => $message,
                $dataName => $data
            ], $type);
        }

        return response()->json([
            'success' => true,
            'message' => $message
        ], $type);
    }
}
