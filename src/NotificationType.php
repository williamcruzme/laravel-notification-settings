<?php

namespace Millions\Notifications;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\DatabaseNotification;

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
            cache()->tags('notification_settings')->forget("notifications:$notificationType->name");
        };

        static::saved($callback);
        static::deleted($callback);
    }

    /**
     * Get the notifications for the notification type.
     */
    public function notifications()
    {
        return $this->hasMany(DatabaseNotification::class, 'type', 'name');
    }

    /**
     * Check if the notification type is enabled.
     *
     * @param  string  $notificationType
     * @return bool
     */
    public static function isEnabled($notificationType)
    {
        $settings = cache()->tags('notification_settings')->tarememberForever("notifications:$notificationType", function () use ($notificationType) {
            return NotificationType::whereName($notificationType)->first();
        });

        return ! $settings || $settings->status;
    }
}
