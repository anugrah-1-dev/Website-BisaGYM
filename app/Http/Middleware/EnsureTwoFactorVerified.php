<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\TwoFactorCodeMail;
use App\Models\ActivityLog;
use Symfony\Component\HttpFoundation\Response;

class EnsureTwoFactorVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            // Exclude OTP verification routes & logout
            if ($request->routeIs('2fa.*') || $request->routeIs('logout')) {
                return $next($request);
            }

            $user = Auth::user();

            if (is_null($user->two_factor_verified_at) || $user->two_factor_verified_at->addHours(24)->isPast()) {
                // Logout active web guard
                Auth::guard('web')->logout();

                // Generate new 2FA code
                $user->two_factor_code = (string) rand(100000, 999999);
                $user->two_factor_expires_at = now()->addMinutes(15);
                $user->save();

                // Send email
                Mail::to($user->email)->send(new TwoFactorCodeMail($user->two_factor_code, $user));

                // Log Activity
                ActivityLog::create([
                    'user_id' => $user->id,
                    'action' => 'REQUEST_OTP',
                    'module' => 'Auth',
                    'description' => 'Masa verifikasi OTP 24 jam telah berakhir. Meminta kode OTP baru.'
                ]);

                // Store session info needed by 2FA form
                $request->session()->put('2fa_user_id', $user->id);

                return redirect()->route('2fa.index')->with('status', 'Masa verifikasi 24 jam (1 hari) Anda telah berakhir. Silakan masukkan kode OTP baru yang dikirim ke email Anda.');
            }
        }

        return $next($request);
    }
}
