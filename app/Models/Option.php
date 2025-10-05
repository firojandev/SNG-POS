<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    protected $fillable = ['key', 'value'];

    /**
     * Get option value by key
     */
    public static function get($key, $default = null)
    {
        $option = self::where('key', $key)->first();
        return $option ? $option->value : $default;
    }

    /**
     * Set option value by key
     */
    public static function set($key, $value)
    {
        return self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }
}
