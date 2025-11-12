<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Lend extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'borrower',
        'date',
        'amount',
        'note',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();

        // Auto-set store_id when creating
        self::creating(function($model){
            $model->store_id = Auth::user()->store_id;
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
