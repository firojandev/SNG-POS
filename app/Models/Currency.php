<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Currency extends Model
{
    protected $fillable = ['name', 'symbol'];

    protected static function boot(): void
    {
        parent::boot();
        self::creating(function($model){
            $model->store_id =  Auth::user()->store_id;
        });
    }

}
