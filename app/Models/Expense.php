<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Expense extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'expense_category_id',
        'amount',
        'date',
        'note',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }


    protected static function boot(): void
    {
        parent::boot();
        self::creating(function($model){
            $model->store_id =  Auth::user()->store_id;
        });


        // Global scope to filter by store_id
        static::addGlobalScope('store', function (Builder $builder) {
            if (Auth::check() && Auth::user()->store_id) {
                $builder->where('store_id', Auth::user()->store_id);
            }
        });

    }


    /**
     * Scope a query to a specific store.
     */
    public function scopeForStore(Builder $query, $storeId): Builder
    {
        return $query->where('store_id', $storeId);
    }

}
