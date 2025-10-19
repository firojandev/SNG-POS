<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'sku',
        'purchase_price',
        'sell_price',
        'description',
        'stock_quantity',
        'store_id',
        'category_id',
        'unit_id',
        'tax_id',
        'vat_id',
        'image',
        'is_active'
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'sell_price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'is_active' => 'boolean'
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function tax(): BelongsTo
    {
        return $this->belongsTo(Tax::class);
    }

    public function vat(): BelongsTo
    {
        return $this->belongsTo(Vat::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'store_id', 'store_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByStore($query, $storeId)
    {
        return $query->where('store_id', $storeId);
    }

    public function getFormattedPurchasePriceAttribute()
    {
        return get_option('app_currency', '$') . number_format($this->purchase_price, 2);
    }

    public function getFormattedSellPriceAttribute()
    {
        return get_option('app_currency', '$') . number_format($this->sell_price, 2);
    }

    public function getStockStatusAttribute()
    {
        if ($this->stock_quantity == 0) {
            return 'Out of Stock';
        } elseif ($this->stock_quantity < 10) {
            return 'Low Stock';
        }
        return 'In Stock';
    }

    protected static function boot(): void
    {
        parent::boot();
        self::creating(function($model){
            $model->uuid =  Str::uuid()->toString();
            $model->store_id =  Auth::user()->store_id;
        });
    }

}
