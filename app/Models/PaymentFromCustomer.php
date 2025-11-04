<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentFromCustomer extends Model
{
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
}
