<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTwoFactorPending
{
    public function handle(Request $request, Closure $next): Response
    {
        // Si no pasó primero por el login con credenciales correctas, fuera
        if (! $request->session()->has('two_factor.user_id')) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}
