<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use App\Models\UserDevice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;

class DeviceVerificationController extends Controller
{
    public function showVerifyForm()
    {
        $user = Auth::user();
        $device = UserDevice::where('user_id', $user->id)
        ->where('verified_at', null) // Only if not verified
        ->first();

    $isExpired = $device ? $device->otp_expires_at <= now() : false;

    return view('auth.otp', [
        'device' => $device,
        'isExpired' => $isExpired
    ]);
    }

    public function verify(Request $request)
    {  $request->validate([
        'otp' => 'required|digits:6',
    ]);

    $otp = $request->otp;

    // Find the device with the provided OTP that has not expired
    $device = UserDevice::where('otp', $otp)
        ->where('otp_expires_at', '>', now())
        ->first();

    if ($device) {

        // Update the device to mark it as verified
        $device->update([
            'verified_at' => now(),
            'otp' => null,
            'otp_expires_at' => null,
        ]);

        // Log in the user
        Auth::login($device->user);

        // Redirect to intended route
        return redirect()->intended();
    }

    Log::warning('Invalid or expired OTP:', ['otp' => $otp]);

    return back()->withErrors(['otp' => 'Invalid or expired OTP.']);
    }

    //resend OTP
    public function resend(Request $request)
    {
     // Get the currently authenticated user
    $user = Auth::user();

    // Find the user's device that has not been verified yet
    $device = UserDevice::where('user_id', $user->id)
        ->where('verified_at', null) // Only resend if not verified
        ->first();

    if ($device) {
        // Generate a new OTP and expiry time
        $device->otp = rand(100000, 999999); // Generate a 6-digit OTP
        $device->otp_expires_at = now()->addMinutes(3); // Set expiry time (e.g., 10 minutes from now)
        $device->save();

        // Send the OTP to the user
        Mail::to($user->email)->send(new OtpMail($device->otp));

        return redirect()->route('device.show')->with('status', 'OTP has been resent. Please check your email.');
    }

    return redirect()->route('device.show')->withErrors(['email' => 'No unverified devices found for this user.']);

    }

}
