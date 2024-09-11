<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

use App\Models\UserDevice;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;
use App\Models\User;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        $user = User::where('email', $credentials['email'])->first();

        // Check if user exists and is active
        if ($user && $user->status == 1) {
            // Attempt authentication
            if (Auth::attempt($credentials)) {
                $user = Auth::user();

                // Ensure these variables are capturing values correctly
                $deviceName = $request->header('User-Agent');
                $deviceIp = $request->ip();

                // Check if the device already exists
                $device = UserDevice::where('user_id', $user->id)
                    ->where('device_ip', $deviceIp)
                    ->where('device_name', $deviceName)
                    ->first();

                if (!$device) {
                    // Create a new device entry
                    $otp = mt_rand(100000, 999999);
                    $device = UserDevice::create([
                        'user_id' => $user->id,
                        'device_name' => $deviceName,
                        'device_ip' => $deviceIp,
                        'otp' => $otp,
                        'otp_expires_at' => now()->addMinutes(3),
                    ]);

                    // Send OTP email
                    Mail::to($user->email)->send(new OtpMail($otp));

                    return redirect()->route('device.show')->with('status', 'OTP sent to your email. Please verify your device.');
                } elseif (!$device->verified_at) {
                    return redirect()->route('device.verify');
                }

                return redirect()->intended();
            }
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records or your account is inactive.',
        ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
