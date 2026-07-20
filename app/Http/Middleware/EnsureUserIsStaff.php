<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Menutup area /admin dari customer. Customer diarahkan ke dashboard-nya sendiri,
 * bukan diberi 403 — supaya klik nyasar tak terasa seperti error.
 */
class EnsureUserIsStaff
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->isCustomer()) {
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
