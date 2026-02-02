<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockOut extends Model
{
    protected $table = 'stock_out';
    
    protected $fillable = [
        'code',
        'product_id',
        'user_id',
        'quantity',
        'reason',
        'notes',
        'date',
    ];

    protected $casts = [
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

        return 'OUT' . $date . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}
