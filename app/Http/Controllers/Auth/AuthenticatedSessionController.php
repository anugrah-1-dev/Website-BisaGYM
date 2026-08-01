<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

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
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = $request->user();

        if (is_null($user->two_factor_verified_at) || $user->two_factor_verified_at->addHours(24)->isPast()) {
            Auth::logout();

            $user->two_factor_code = (string) rand(100000, 999999);
            $user->two_factor_expires_at = now()->addMinutes(15);
            $user->save();

            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\TwoFactorCodeMail($user->two_factor_code, $user));

            \App\Models\ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'REQUEST_OTP',
                'module' => 'Auth',
                'description' => 'Meminta kode OTP via email.'
            ]);

            $request->session()->put('2fa_user_id', $user->id);
            $request->session()->put('2fa_remember', $request->boolean('remember'));

            return redirect()->route('2fa.index');
        }

        $request->session()->regenerate();
        $user->update(['last_session_id' => $request->session()->getId()]);

        return redirect()->intended(route('dashboard', absolute: false));
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
