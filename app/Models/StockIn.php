<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockIn extends Model
{
    protected $fillable = [
        'code',
        'product_id',
        'user_id',
        'quantity',
        'purchase_price',
        'total_price',
        'supplier',
        'notes',
        'date',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'date' => 'date',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFormattedPurchasePriceAttribute()
    {
        return number_format($this->purchase_price, 0, ',', '.');
    }

    public function getFormattedTotalPriceAttribute()
    {
        return number_format($this->total_price, 0, ',', '.');
    }

    public static function generateCode()
    {
        $date = now()->format('Ymd');
        $lastCode = self::whereDate('created_at', now()->format('Y-m-d'))
            ->orderBy('id', 'desc')
            ->first();

        if ($lastCode) {
            $lastNumber = (int) substr($lastCode->code, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return 'IN' . $date . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}
