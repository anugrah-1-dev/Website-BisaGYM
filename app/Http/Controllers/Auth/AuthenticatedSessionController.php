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

        // Pengecekan Lokasi (Geofencing GPS) jika diaktifkan oleh Developer untuk user ini
        if ($user->is_location_restricted) {
            $lat = $request->input('latitude');
            $lng = $request->input('longitude');

            if (is_null($lat) || is_null($lng) || $lat === '' || $lng === '') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Akses lokasi (GPS) wajib diaktifkan pada browser/perangkat Anda untuk login ke akun ini.',
                ])->onlyInput('email');
            }

            $target = $user->getGeofenceTarget();
            $distance = $this->calculateHaversineDistance((float)$lat, (float)$lng, (float)$target['latitude'], (float)$target['longitude']);

            if ($distance > $target['radius']) {
                Auth::logout();

                \App\Models\ActivityLog::create([
                    'user_id' => $user->id,
                    'action' => 'LOGIN_FAILED_GEOFENCE',
                    'module' => 'Auth',
                    'description' => "Login ditolak karena posisi berada di luar radius (" . round($distance) . "m dari lokasi target, maks {$target['radius']}m)."
                ]);

                return back()->withErrors([
                    'email' => "Login ditolak: Posisi Anda berada di luar area yang diizinkan (jarak: " . round($distance) . "m, batas maksimal: {$target['radius']}m).",
                ])->onlyInput('email');
            }
        }

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
     * Menghitung jarak antara dua koordinat latitude/longitude dalam satuan meter (Haversine Formula).
     */
    private function calculateHaversineDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000; // Radius bumi dalam meter

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
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
