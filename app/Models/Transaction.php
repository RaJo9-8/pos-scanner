<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'invoice_number',
        'user_id',
        'total_amount',
        'cash_amount',
        'change_amount',
        'discount',
        'tax',
        'payment_method',
        'status',
        'notes',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'cash_amount' => 'decimal:2',
        'change_amount' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactionItems()
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function returnTransactions()
    {
        return $this->hasMany(ReturnTransaction::class);
    }

    public function getFormattedTotalAmountAttribute()
    {
        return 'Rp ' . number_format($this->total_amount, 0, ',', '.');
    }

    public function getFormattedCashAmountAttribute()
    {
        return 'Rp ' . number_format($this->cash_amount, 0, ',', '.');
    }

    public function getFormattedChangeAmountAttribute()
    {
        return 'Rp ' . number_format($this->change_amount, 0, ',', '.');
    }

    public function getFormattedDiscountAttribute()
    {
        return 'Rp ' . number_format($this->discount, 0, ',', '.');
    }

    public function getFormattedTaxAttribute()
    {
        return 'Rp ' . number_format($this->tax, 0, ',', '.');
    }

    public function getProfitAttribute()
    {
        return $this->transactionItems->sum(function ($item) {
            return ($item->product->selling_price - $item->product->purchase_price) * $item->quantity;
        });
    }

    public function getFormattedProfitAttribute()
    {
        return 'Rp ' . number_format($this->profit, 0, ',', '.');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
            $transaction->invoice_number = 'INV-' . date('Ymd') . '-' . str_pad(Transaction::count() + 1, 4, '0', STR_PAD_LEFT);
        });
    }
}
