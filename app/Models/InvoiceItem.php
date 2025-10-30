<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'invoice_id',
        'product_id',
        'unit_price',
        'quantity',
        'vat_amount',
        'unit_total',
        'item_discount_type',
        'item_discount_value',
        'item_discount_amount'
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'quantity' => 'integer',
        'vat_amount' => 'decimal:2',
        'unit_total' => 'decimal:2',
        'item_discount_value' => 'decimal:2',
        'item_discount_amount' => 'decimal:2',
    ];

    /**
     * Get the invoice that owns the invoice item.
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Get the product that owns the invoice item.
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
        return get_option('app_currency') . number_format($this->unit_price, 2);
    }

    /**
     * Get formatted vat amount
     */
    public function getFormattedVatAmountAttribute()
    {
        return get_option('app_currency') . number_format($this->vat_amount, 2);
    }

    /**
     * Get formatted unit total
     */
    public function getFormattedUnitTotalAttribute()
    {
        return get_option('app_currency') . number_format($this->unit_total, 2);
    }

    /**
     * Get formatted item discount amount
     */
    public function getFormattedItemDiscountAmountAttribute()
    {
        return get_option('app_currency') . number_format($this->item_discount_amount, 2);
    }
}
