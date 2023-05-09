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
    public function handle(Request $request, Closure $next): Response
    {
        if (
            !$request->user()->hasAnyRole(['admin', 'super-admin']) ||
            $request->user()->id != User::where('id', $request->id)->select('id')->first()->id
        ) {
            return abort(403, 'You are not authorized to access this resource.');
        }

        return $next($request);
    }
}
