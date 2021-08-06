<?php

namespace Millions\Notifications;

use Illuminate\Database\Eloquent\Model;

class NotificationType extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'display_text',
        'schedule',
        'status',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'schedule' => 'array',
        'status' => 'boolean',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'schedule',
    ];

    /**
     * Bootstrap the model and its traits.
     *
     * @return void
     */
    public static function boot() {
        parent::boot();

        $callback = function ($notificationType) {
            cache()->forget("notifications:$notificationType->name");
        };

        static::saved($callback);
        static::deleted($callback);
    }

    /**
     * Check if the notification type is enabled.
     *
     * @param  string  $notificationType
     * @return bool
     */
    public static function isEnabled($notificationType)
    {
        $settings = cache()->rememberForever("notifications:$notificationType", function () use ($notificationType) {
            return NotificationType::whereName($notificationType)->first();
        });

        return ! $settings || $settings->status;
    }
}
