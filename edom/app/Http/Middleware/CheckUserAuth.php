<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckUserAuth
{
    public function handle($request, Closure $next)
    {
        if (!Auth::check()) {
            // User is not logged in, redirect or handle accordingly
            return redirect('login');
        }

        return $next($request);
    }
}