<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class PaymentFromCustomer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'store_id',
        'customer_id',
        'invoice_id',
        'payment_date',
        'amount',
        'note',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    protected static function boot(): void
    {
        parent::boot();

        // Auto-set store_id on creation
        self::creating(function($model){
            if (Auth::check() && Auth::user()->store_id) {
                $model->store_id = Auth::user()->store_id;
            }
        });

        // Global scope to filter by store_id
        static::addGlobalScope('store', function ($builder) {
            if (Auth::check() && Auth::user()->store_id) {
                $builder->where('payment_from_customers.store_id', Auth::user()->store_id);
            }
        });
    }
}
