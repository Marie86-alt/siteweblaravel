<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'birth_date',
        'company',
        'billing_address',
        'billing_city',
        'billing_postal_code',
        'billing_country',
        'delivery_address',
        'delivery_city',
        'delivery_postal_code',
        'delivery_country',
        'is_admin',
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
            'birth_date' => 'date',
            'is_admin' => 'boolean',
        ];
    }

    // Relations existantes
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function defaultShippingAddress()
    {
        return $this->hasOne(Address::class)
            ->where('type', 'shipping')
            ->where('is_default', true);
    }

    public function defaultBillingAddress()
    {
        return $this->hasOne(Address::class)
            ->where('type', 'billing')
            ->where('is_default', true);
    }

    // Scopes existants
    public function scopeAdmins($query)
    {
        return $query->where('is_admin', true);
    }

    public function scopeCustomers($query)
    {
        return $query->where('is_admin', false);
    }

    // Accesseurs améliorés
    public function getFirstNameAttribute($value)
    {
        // Si first_name existe en base, l'utiliser
        if ($value) {
            return $value;
        }

        // Sinon, extraire de 'name'
        return explode(' ', $this->name)[0];
    }

    public function getLastNameAttribute($value)
    {
        // Si last_name existe en base, l'utiliser
        if ($value) {
            return $value;
        }

        // Sinon, extraire de 'name'
        $nameParts = explode(' ', $this->name);
        return count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : '';
    }

    public function getFullNameAttribute()
    {
        if ($this->first_name && $this->last_name) {
            return $this->first_name . ' ' . $this->last_name;
        }
        return $this->name;
    }

    public function getInitialsAttribute()
    {
        if ($this->first_name && $this->last_name) {
            return strtoupper(substr($this->first_name, 0, 1) . substr($this->last_name, 0, 1));
        }

        $nameParts = explode(' ', $this->name);
        if (count($nameParts) >= 2) {
            return strtoupper(substr($nameParts[0], 0, 1) . substr($nameParts[1], 0, 1));
        }

        return strtoupper(substr($this->name, 0, 2));
    }

    public function getTotalOrdersAttribute()
    {
        return $this->orders()->count();
    }

    public function getTotalSpentAttribute()
    {
        return $this->orders()
            ->whereIn('status', ['confirmed', 'preparing', 'shipped', 'delivered'])
            ->sum('total_amount');
    }

    /**
     * Vérifier si l'utilisateur est administrateur
     */
    public function isAdmin()
    {
        return $this->is_admin;
    }
}
