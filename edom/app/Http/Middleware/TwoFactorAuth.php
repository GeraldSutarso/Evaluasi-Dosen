<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class TwoFactorAuth
{
    public function handle($request, Closure $next)
    {
        if (Auth::check() && !Session::get('2fa_verified')) {
            return redirect('2fa');
        }

        return $next($request);
    }
}
