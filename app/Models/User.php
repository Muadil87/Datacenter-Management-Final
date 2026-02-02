<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    
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
    'role',      // Ajouté
    'is_active', // Ajouté
    'profile_photo', // Nouvelle colonne
    'notification_email', // Nouvelle colonne
    'notification_incidents', // Nouvelle colonne
    'email_verified_at', // For social logins
    'auth_provider', // OAuth provider (google, github, etc)
    'rejected_at', // For rejected users
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
        ];
    }

    /**
     * Check if user can access maintenance (only admin and manager)
     */
    public function canAccessMaintenance(): bool
    {
        return in_array($this->role, ['admin', 'manager']);
    }

    /**
     * Check if user can manage resources (admin and manager)
     */
    public function canManage(): bool
    {
        return in_array($this->role, ['admin', 'manager']);
    }

    /**
     * Check if user can reserve resources (internal users can reserve)
     */
    public function canReserve(): bool
    {
        return $this->role === 'internal';
    }

    // Relationships
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function incidents()
    {
        return $this->hasMany(Incident::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function logs()
    {
        return $this->hasMany(Log::class);
    }
}
