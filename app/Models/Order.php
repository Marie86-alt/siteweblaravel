<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'status',
        'subtotal',
        'shipping_amount',        // ✅ AJOUTÉ
        'total_amount',
        'payment_method',
        'payment_status',
        'notes',
        'billing_address',
        'shipping_address',       // ✅ AJOUTÉ
        // Champs individuels de facturation
        'billing_first_name',
        'billing_last_name',
        'billing_email',
        'billing_phone',
        'billing_city',
        'billing_postal_code',
        'billing_country',
        // Champs individuels de livraison
        'delivery_first_name',
        'delivery_last_name',
        'delivery_address',
        'delivery_city',
        'delivery_postal_code',
        'delivery_country',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relation avec l'utilisateur
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec les articles de commande
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Mutateur pour le statut
     */
    public function setStatusAttribute($value)
    {
        $this->attributes['status'] = strtolower($value);
    }

    /**
     * Accesseur pour le statut formaté
     */
    public function getFormattedStatusAttribute()
    {
        return ucfirst($this->status);
    }

    /**
     * Accesseur pour le montant total formaté
     */
    public function getFormattedTotalAttribute()
    {
        return number_format($this->total_amount, 2, ',', ' ') . '€';
    }

    /**
     * Scope pour filtrer par statut
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope pour les commandes d'un utilisateur
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Calculer le nombre total d'articles
     */
    public function getTotalItemsAttribute()
    {
        return $this->orderItems->sum('quantity');
    }

    /**
     * Vérifier si la commande est modifiable
     */
    public function isEditable()
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }

    /**
     * Vérifier si la commande est annulable
     */
    public function isCancellable()
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }
}
