<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'last_login_at',
        'is_active',
        'two_factor_code',
        'two_factor_expires_at',
        'two_factor_verified_at',
        'last_session_id',
        'is_location_restricted',
        'allowed_latitude',
        'allowed_longitude',
        'allowed_radius_meters',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_expires_at' => 'datetime',
            'two_factor_verified_at' => 'datetime',
            'is_location_restricted' => 'boolean',
            'allowed_latitude' => 'float',
            'allowed_longitude' => 'float',
            'allowed_radius_meters' => 'integer',
        ];
    }

    /**
     * Mendapatkan koordinat target dan radius yang diperbolehkan untuk user.
     */
    public function getGeofenceTarget(): array
    {
        return [
            'latitude' => $this->allowed_latitude ?? config('app.gym_latitude', -7.33405),
            'longitude' => $this->allowed_longitude ?? config('app.gym_longitude', 112.78255),
            'radius' => $this->allowed_radius_meters ?? config('app.gym_allowed_radius', 500),
        ];
    }
}
