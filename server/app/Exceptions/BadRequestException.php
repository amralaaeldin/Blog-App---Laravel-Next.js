<?php

namespace App\Exceptions;

use Exception;

class BadRequestException extends Exception
{
    public function render($request)
    {
        if (!json_decode($this->getMessage())) {
            return response()->json([
                'message' =>  $this->getMessage()
            ], 400);
        }

        return response()->json([
            'errors' =>  json_decode($this->getMessage())
        ], 400);
    }
}
