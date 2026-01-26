<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionItem extends Model
{
    protected $fillable = [
        'transaction_id',
        'product_id',
        'product_name',
        'quantity',
        'price',
        'subtotal',
        'discount',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'quantity' => 'integer',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    public function getFormattedSubtotalAttribute()
    {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
    }

    public function getFormattedDiscountAttribute()
    {
        return 'Rp ' . number_format($this->discount, 0, ',', '.');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($item) {
            if ($item->product) {
                $item->product_name = $item->product->name;
            }
        });

        static::created(function ($item) {
            if ($item->product) {
                $item->product->decrement('stock', $item->quantity);
            }
        });

        static::deleted(function ($item) {
            if ($item->product) {
                $item->product->increment('stock', $item->quantity);
            }
        });
    }
}
