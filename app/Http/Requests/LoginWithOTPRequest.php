<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\OTP;
use Illuminate\Support\Facades\Mail;
use App\Mail\OTPMail;

class LoginWithOTPRequest extends LoginRequest
{
    public function authenticate()
    {
        parent::authenticate();

        // Generate and store OTP
        $user = Auth::user();
        $otpCode = rand(100000, 999999);
        OTP::create([
            'user_id' => $user->id,
            'otp' => $otpCode,
            'expires_at' => now()->addMinutes(10),
        ]);

        // Send OTP via email
        Mail::to($user->email)->send(new OTPMail($otpCode));

        // Log the user out temporarily
        Auth::logout();
        session(['user_id' => $user->id]);
    }
}
