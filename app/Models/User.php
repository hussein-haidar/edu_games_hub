<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'points',
        'subscribed_until',
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

    public function progres()
    {
        return $this->hasMany(\App\Models\Progres::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isSubscribed(): bool
    {
        return $this->subscribed_until !== null
            && $this->subscribed_until->isFuture();
    }

    public function hasAccessTo(\App\Models\Game $game): bool
    {
        return $this->isSubscribed() || $this->isAdmin();
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'subscribed_until' => 'datetime',
            'points' => 'integer',
            'password' => 'hashed',
        ];
    }
}
