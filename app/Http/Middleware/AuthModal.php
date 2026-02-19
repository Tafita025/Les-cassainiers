<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthModal
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            if ($request->header('X-Inertia')) {
                return back()->with('authRequired', true);
            }

            return redirect()->route('home.index');
        }

        return $next($request);
    }
}