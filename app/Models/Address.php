<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class Address extends Model
{
     use HasFactory;

    protected $fillable = [
        'user_id', 'type', 'first_name', 'last_name', 'company',
        'address_line_1', 'address_line_2', 'city', 'postal_code',
        'state', 'country', 'phone', 'is_default'
    ];

    protected $casts = [
        'is_default' => 'boolean'
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeShipping($query)
    {
        return $query->where('type', 'shipping');
    }

    public function scopeBilling($query)
    {
        return $query->where('type', 'billing');
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    // Accesseurs
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getFullAddressAttribute()
    {
        $address = $this->address_line_1;
        if ($this->address_line_2) {
            $address .= ', ' . $this->address_line_2;
        }
        $address .= ', ' . $this->postal_code . ' ' . $this->city;
        if ($this->state) {
            $address .= ', ' . $this->state;
        }
        return $address;
    }//
}
