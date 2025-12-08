<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'username',
        'first_name',
        'last_name',
        'role',
        'email',
        'password',
        'date_of_birth',
        'address',
        'profile_picture',
        'phone_number',
        'telegram_id', // Use telegram_id instead of username
    ];

    /**
     * The attributes that should be hidden.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Laravel Accessor for full name.
     */
    public function getFullNameAttribute()
    {
        if ($this->first_name && $this->last_name) {
            return "{$this->first_name} {$this->last_name}";
        }

        return $this->first_name ?? $this->last_name ?? null;
    }

    /**
     * Display-friendly role text.
     */
    public function getRoleDisplayAttribute()
    {
        $roles = [
            'admin' => 'Administrator',
            'staff' => 'Staff',
            'customer' => 'Customer',
        ];

        return $roles[$this->role] ?? ucfirst($this->role ?: 'User');
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isStaff()
    {
        return in_array($this->role, ['staff', 'admin']);
    }

    public function isCustomer()
    {
        return $this->role === 'customer';
    }
}
