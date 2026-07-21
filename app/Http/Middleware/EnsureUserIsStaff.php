<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Menutup area /admin dari customer. Customer diarahkan ke dashboard-nya sendiri,
 * bukan diberi 403 — supaya klik nyasar tak terasa seperti error.
 *
 * Pengecualian: Customer boleh akses API customizer, policy-nya akan validasi
 * ownership per-resource (task 1: fase 7e).
 */
class EnsureUserIsStaff
{
    public function handle(Request $request, Closure $next): Response
    {
        $isCustomizerApi = str_contains($request->path(), '/api/invitations') &&
            str_contains($request->path(), '/customizer');

        if ($request->user()?->isCustomer() && ! $isCustomizerApi) {
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
