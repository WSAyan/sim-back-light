<?php

namespace App\Utils;

class RequestFormatter
{
    public static function resolveJsonConfusion($params)
    {
        $resolved = null;
        try {
            $resolved = json_decode($params, true);
        } catch (\Exception $e) {
            $resolved = $params;
        }

        return $resolved;
    }
}
