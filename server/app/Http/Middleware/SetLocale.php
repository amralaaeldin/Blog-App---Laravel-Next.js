<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\App;

class SetLocale
{
  public function handle(Request $request, Closure $next): Response
  {
    if ($request->lang && in_array($request->lang, ['ar', 'en', 'ur'])) {
      App::setLocale($request->lang);
    }

    return $next($request);
  }
}
