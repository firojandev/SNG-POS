<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmtpSetting extends Model
{
    protected $fillable = [
        'mail_driver',
        'mail_host',
        'mail_port',
        'mail_username',
        'mail_password',
        'mail_encryption',
        'mail_from_address',
        'mail_from_name',
        'is_active'
    ];

    protected $casts = [
        'mail_port' => 'integer',
        'is_active' => 'boolean'
    ];

    /**
     * Get the active SMTP configuration
     */
    public static function getActiveConfig()
    {
        return self::where('is_active', true)->first();
    }
}
