<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Invoice extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uuid',
        'invoice_number',
        'customer_id',
        'store_id',
        'date',
        'unit_total',
        'total_vat',
        'total_amount',
        'discount_type',
        'discount_value',
        'discount_amount',
        'payable_amount',
        'paid_amount',
        'due_amount',
        'status',
        'note'
    ];

    protected $casts = [
        'date' => 'date',
        'unit_total' => 'decimal:2',
        'total_vat' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'payable_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $appends = [
        'formatted_unit_total',
        'formatted_total_vat',
        'formatted_total_amount',
        'formatted_discount_amount',
        'formatted_payable_amount',
        'formatted_paid_amount',
        'formatted_due_amount'
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    protected static function boot(): void
    {
        parent::boot();

        // Auto-set uuid and store_id when creating
        self::creating(function($model){
            $model->uuid = Str::uuid()->toString();
            $model->store_id = Auth::user()->store_id;

            // Generate unique invoice number using store_id and timestamp
            $storeId = $model->store_id;
            $timestamp = date('YmdHis'); // Format: 20250115143025
            $model->invoice_number = 'INV-' . $storeId . '-' . $timestamp;
        });

        // Global scope to filter by store_id
        static::addGlobalScope('store', function (Builder $builder) {
            if (Auth::check() && Auth::user()->store_id) {
                $builder->where('invoices.store_id', Auth::user()->store_id);
            }
        });
    }

    /**
     * Get the customer that owns the invoice.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the store that owns the invoice.
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Get the invoice items for the invoice.
     */
    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    /**
     * Scope a query to a specific store.
     */
    public function scopeForStore(Builder $query, $storeId): Builder
    {
        return $query->where('store_id', $storeId);
    }

    /**
     * Scope a query to only active invoices.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only returned invoices.
     */
    public function scopeReturned(Builder $query): Builder
    {
        return $query->where('status', 'returned');
    }

    /**
     * Scope a query to only cancelled invoices.
     */
    public function scopeCancelled(Builder $query): Builder
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Get formatted unit total
     */
    public function getFormattedUnitTotalAttribute()
    {
        return get_option('app_currency') . ' ' . number_format($this->unit_total, 2);
    }

    /**
     * Get formatted total vat
     */
    public function getFormattedTotalVatAttribute()
    {
        return get_option('app_currency') . ' ' . number_format($this->total_vat, 2);
    }

    /**
     * Get formatted discount amount
     */
    public function getFormattedDiscountAmountAttribute()
    {
        return get_option('app_currency') . ' ' . number_format($this->discount_amount, 2);
    }

    /**
     * Get formatted total amount
     */
    public function getFormattedTotalAmountAttribute()
    {
        return get_option('app_currency') . ' ' . number_format($this->total_amount, 2);
    }

    /**
     * Get formatted payable amount
     */
    public function getFormattedPayableAmountAttribute()
    {
        return get_option('app_currency') . ' ' . number_format($this->payable_amount, 2);
    }

    /**
     * Get formatted paid amount
     */
    public function getFormattedPaidAmountAttribute()
    {
        return get_option('app_currency') . ' ' . number_format($this->paid_amount, 2);
    }

    /**
     * Get formatted due amount
     */
    public function getFormattedDueAmountAttribute()
    {
        return get_option('app_currency') . ' ' . number_format($this->due_amount, 2);
    }
}
