<?php

namespace App\Exceptions;

use Exception;

class QueryDBException extends Exception
{
    public function render($request)
    {
        return response()->json([
            'message' => $this->getMessage()
        ], 500);
    }
}
