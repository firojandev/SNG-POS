<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Purchase extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uuid',
        'invoice_number',
        'supplier_id',
        'total_amount',
        'paid_amount',
        'due_amount',
        'note',
        'store_id'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $appends = [
        'formatted_total_amount',
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
            $model->invoice_number = 'PUR-' . date('Y') . '-' . str_pad(Purchase::whereYear('created_at', date('Y'))->count() + 1, 6, '0', STR_PAD_LEFT);
        });

        // Global scope to filter by store_id
        static::addGlobalScope('store', function (Builder $builder) {
            if (Auth::check() && Auth::user()->store_id) {
                $builder->where('store_id', Auth::user()->store_id);
            }
        });
    }

    /**
     * Get the supplier that owns the purchase.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the purchase items for the purchase.
     */
    public function items(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }

    /**
     * Scope a query to a specific store.
     */
    public function scopeForStore(Builder $query, $storeId): Builder
    {
        return $query->where('store_id', $storeId);
    }

    /**
     * Get formatted total amount
     */
    public function getFormattedTotalAmountAttribute()
    {
        return get_option('app_currency') . number_format($this->total_amount, 2);
    }

    /**
     * Get formatted paid amount
     */
    public function getFormattedPaidAmountAttribute()
    {
        return get_option('app_currency') . number_format($this->paid_amount, 2);
    }

    /**
     * Get formatted due amount
     */
    public function getFormattedDueAmountAttribute()
    {
        return get_option('app_currency') . number_format($this->due_amount, 2);
    }
}