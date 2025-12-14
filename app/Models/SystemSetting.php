<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = [
        'pricing_tier_1',
        'pricing_tier_2',
        'trial_days',
        'payment_qr_code',
        'payment_upi_id',
        'support_email',
        'support_phone',
        'tutorial_video_url',
    ];

    public static function instance(): self
    {
        return static::first() ?? static::create();
    }
}
