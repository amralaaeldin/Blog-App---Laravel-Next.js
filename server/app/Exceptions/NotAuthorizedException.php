<?php

namespace App\Exceptions;

use Exception;

class NotAuthorizedException extends Exception
{
    public function render($request)
    {
        return response()->json([
            'message' =>  $this->getMessage()
        ], 403);
    }
}
