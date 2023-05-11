<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class EnsureYourselfOrCan
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = User::where('id', $request->id)->select('id')->first();
        if (
            !$request->user()->hasAnyRole($roles) &&
            $request->user()->id != $user?->id
        ) {
            return abort(403, 'You are not authorized to access this resource.');
        }

        $request["haveRoles"] = $user?->getRoleNames()->count();
        return $next($request);
    }
}
