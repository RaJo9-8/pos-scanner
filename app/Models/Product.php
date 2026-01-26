<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'barcode',
        'description',
        'purchase_price',
        'selling_price',
        'stock',
        'min_stock',
        'unit',
        'category',
        'image',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'stock' => 'integer',
        'min_stock' => 'integer',
    ];

    public function transactionItems()
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function isLowStock()
    {
        return $this->stock <= $this->min_stock;
    }

    public function getFormattedPurchasePriceAttribute()
    {
        return 'Rp ' . number_format($this->purchase_price, 0, ',', '.');
    }

    public function getFormattedSellingPriceAttribute()
    {
        return 'Rp ' . number_format($this->selling_price, 0, ',', '.');
    }

    public function getProfitAttribute()
    {
        return $this->selling_price - $this->purchase_price;
    }

    public function getFormattedProfitAttribute()
    {
        return 'Rp ' . number_format($this->profit, 0, ',', '.');
    }

    public function scopeLowStock($query)
    {
        return $query->where('stock', '<=', 'min_stock');
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
    }
}
