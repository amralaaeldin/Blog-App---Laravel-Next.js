<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsOwnerOrCan
{
    public function handle(Request $request, Closure $next, $modelName, ...$roles): Response
    {
        $Model = '\App\Models' . '\\' . ucfirst($modelName);

        if (
            !$request->user()->hasAnyRole($roles) &&
            $request->user()->id != $Model::where('id', $request->id)->first()?->user?->id &&
            $request->user()->id != $Model::where('id', $request->id)->first()?->post?->user?->id
        ) {
            throw new \App\Exceptions\NotAuthorizedException(__("You are not authorized to do that."));
        }

        return $next($request);
    }
}
