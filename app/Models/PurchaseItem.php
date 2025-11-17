<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseItem extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'purchase_id',
        'product_id',
        'unit_price',
        'quantity',
        'tax_amount',
        'unit_total'
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'quantity' => 'integer',
        'tax_amount' => 'decimal:2',
        'unit_total' => 'decimal:2',
    ];

    /**
     * Get the purchase that owns the purchase item.
     */
    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }

    /**
     * Get the product that owns the purchase item.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get formatted unit price
     */
    public function getFormattedUnitPriceAttribute()
    {
        return get_option('app_currency') . ' ' . number_format($this->unit_price, 2);
    }

    /**
     * Get formatted tax amount
     */
    public function getFormattedTaxAmountAttribute()
    {
        return get_option('app_currency') . ' ' . number_format($this->tax_amount, 2);
    }

    /**
     * Get formatted unit total
     */
    public function getFormattedUnitTotalAttribute()
    {
        return get_option('app_currency') . ' ' . number_format($this->unit_total, 2);
    }
}