<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class EnsureItsUser
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request["haveRoles"]) {
            throw new \App\Exceptions\NotAuthorizedException(__("You are not authorized to do that."));
        }

        return $next($request);
    }
}
