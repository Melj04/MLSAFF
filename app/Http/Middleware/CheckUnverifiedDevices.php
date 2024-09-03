<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\UserDevice;

class CheckUnverifiedDevices
{
    /**
     * Handle an incoming request.
     *
    * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        // Check for unverified devices
        if ($user && UserDevice::where('user_id', $user->id)->whereNull('verified_at')->exists()) {
            return redirect()->route('device.show');
        }else{
            return $next($request);
        }

    }
}
