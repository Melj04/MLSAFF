<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\UserDevice;

class admin
{
    /**
     * Handle an incoming request.
     *
    * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Check for user role
        if (Auth::check() && Auth::user()->role === 1) {
            // Allow the request to proceed if user is admin
            return $next($request);
        }

        return redirect('/')->with('error', 'You do not have admin access.');

    }
}
