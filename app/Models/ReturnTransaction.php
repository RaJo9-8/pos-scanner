<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReturnTransaction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'return_number',
        'transaction_id',
        'user_id',
        'total_amount',
        'reason',
        'description',
        'status',
        'notes',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFormattedTotalAmountAttribute()
    {
        return 'Rp ' . number_format($this->total_amount, 0, ',', '.');
    }

    public function getReasonTextAttribute()
    {
        $reasons = [
            'defective' => 'Barang Rusak',
            'wrong_item' => 'Salah Barang',
            'customer_request' => 'Permintaan Customer',
            'expired' => 'Kadaluarsa',
            'other' => 'Lainnya',
        ];

        return $reasons[$this->reason] ?? 'Unknown';
    }

    public function getStatusTextAttribute()
    {
        $statuses = [
            'pending' => 'Menunggu',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
        ];

        return $statuses[$this->status] ?? 'Unknown';
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($return) {
            $return->return_number = 'RET-' . date('Ymd') . '-' . str_pad(ReturnTransaction::count() + 1, 4, '0', STR_PAD_LEFT);
        });
    }
}
