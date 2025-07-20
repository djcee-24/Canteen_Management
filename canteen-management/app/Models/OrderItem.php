<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'menu_item_id',
        'menu_item_name',
        'unit_price',
        'quantity',
        'total_price',
        'special_instructions',
        'customizations',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'quantity' => 'integer',
        'total_price' => 'decimal:2',
        'customizations' => 'array',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class);
    }

    public function getFormattedUnitPriceAttribute()
    {
        return '₱' . number_format($this->unit_price, 2);
    }

    public function getFormattedTotalPriceAttribute()
    {
        return '₱' . number_format($this->total_price, 2);
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($orderItem) {
            $orderItem->total_price = $orderItem->unit_price * $orderItem->quantity;
        });

        static::updating(function ($orderItem) {
            if ($orderItem->isDirty(['unit_price', 'quantity'])) {
                $orderItem->total_price = $orderItem->unit_price * $orderItem->quantity;
            }
        });
    }
}