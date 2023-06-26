<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsOwner
{
    public function handle(Request $request, Closure $next, $modelName): Response
    {
        $Model = '\App\Models' . '\\' . ucfirst($modelName);

        if ($request->user()->id != $Model::where('id', $request->id)->select('id', 'user_id')->first()?->user->id) {
            throw new \App\Exceptions\NotAuthorizedException(__("You are not authorized to do that."));
        }

        return $next($request);
    }
}
