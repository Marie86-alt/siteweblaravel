<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItem extends Model
{
    use HasFactory;

    // ✅ AJOUTEZ CETTE LIGNE !
    protected $table = 'orders_items';

    protected $fillable = [
        'order_id', 'product_id', 'product_name', 'product_sku',
        'price', 'quantity', 'total'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'total' => 'decimal:2'
    ];

    // Relations
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Accesseurs
    public function getFormattedPriceAttribute()
    {
        return number_format($this->price, 2, ',', ' ') . ' €';
    }

    public function getFormattedTotalAttribute()
    {
        return number_format($this->total, 2, ',', ' ') . ' €';
    }
}

