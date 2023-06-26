<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class EnsureYourselfOrCan
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = User::where('id', $request->id)->select('id')->first();
        if (
            !$request->user()->hasAnyRole($roles) &&
            $request->user()->id != $user?->id
        ) {
            throw new \App\Exceptions\NotAuthorizedException(__("You are not authorized to do that."));
        }

        return $next($request);
    }
}
