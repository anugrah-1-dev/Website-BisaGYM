<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\TwoFactorCodeMail;

class TwoFactorController extends Controller
{
    /**
     * Show the two-factor authentication form.
     */
    public function index(Request $request)
    {
        if (! $request->session()->has('2fa_user_id')) {
            return redirect()->route('login');
        }

        return view('auth.two-factor');
    }

    /**
     * Verify the two-factor code.
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        if (! $request->session()->has('2fa_user_id')) {
            return redirect()->route('login');
        }

        $user = User::find($request->session()->get('2fa_user_id'));

        if (! $user) {
            return redirect()->route('login');
        }

        if ($request->code !== $user->two_factor_code) {
            return back()->withErrors(['code' => 'Kode verifikasi tidak cocok.']);
        }

        if (now()->greaterThan($user->two_factor_expires_at)) {
            return back()->withErrors(['code' => 'Kode verifikasi sudah kedaluwarsa. Silakan minta kode baru.']);
        }

        // Login user
        Auth::login($user, $request->session()->get('2fa_remember', false));

        // Update verification time
        $user->update([
            'two_factor_code' => null,
            'two_factor_expires_at' => null,
            'two_factor_verified_at' => now(),
        ]);

        $request->session()->forget('2fa_user_id');
        $request->session()->forget('2fa_remember');

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Resend the two-factor code.
     */
    public function resend(Request $request)
    {
        if (! $request->session()->has('2fa_user_id')) {
            return redirect()->route('login');
        }

        $user = User::find($request->session()->get('2fa_user_id'));

        if (! $user) {
            return redirect()->route('login');
        }

        // Generate new code
        $user->two_factor_code = rand(100000, 999999);
        $user->two_factor_expires_at = now()->addMinutes(15);
        $user->save();

        // Send email
        Mail::to($user->email)->send(new TwoFactorCodeMail($user->two_factor_code, $user));

        // Log Activity
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'REQUEST_OTP',
            'module' => 'Auth',
            'description' => 'Meminta kode OTP ulang via email.'
        ]);

        return back()->with('status', 'Kode verifikasi baru telah dikirim ke email Anda.');
    }
}
