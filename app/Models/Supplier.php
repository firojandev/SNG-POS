<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'contact_person',
        'phone',
        'email',
        'address',
        'photo',
        'about',
        'balance',
        'is_active',
        'uuid',
    ];

    protected static function boot(): void
    {
        parent::boot();

        // Auto-set uuid and store_id when creating
        self::creating(function($model){
            $model->uuid = Str::uuid()->toString();
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
