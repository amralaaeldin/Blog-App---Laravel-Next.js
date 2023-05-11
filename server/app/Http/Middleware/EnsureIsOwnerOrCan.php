<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsOwnerOrCan
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $modelName, ...$roles): Response
    {
        $Model = '\App\Models' . '\\' . ucfirst($modelName);

        if (
            !$request->user()->hasAnyRole($roles) &&
            $request->user()->id != $Model::where('id', $request->id)->select('id', 'user_id')->first()?->user->id &&
            $request->user()->id != $Model::where('id', $request->id)->select('id', 'post_id')->first()?->post->user->id
        ) {
            return abort(403, 'You are not authorized to access this resource.');
        }

        return $next($request);
    }
}
