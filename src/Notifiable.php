<?php

namespace Millions\Notifications;

use Illuminate\Notifications\Notifiable as BaseNotifiable;
use Millions\Notifications\NotificationType;

trait Notifiable
{
    use BaseNotifiable {
        BaseNotifiable::notify as protected _notify;
        BaseNotifiable::notifyNow as protected _notifyNow;
    }

    /**
     * Send the given notification.
     *
     * @param  mixed  $instance
     * @return void
     */
    public function notify($instance)
    {
        $notificationName = get_class($instance);

        if ($this->canReceive($notificationName)) {
            $this->_notify($instance);
        }
    }

    /**
     * Send the given notification immediately.
     *
     * @param  mixed  $instance
     * @param  array|null  $channels
     * @return void
     */
    public function notifyNow($instance, array $channels = null)
    {
        $notificationName = get_class($instance);

        if ($this->canReceive($notificationName)) {
            $this->_notifyNow($instance, $channels);
        }
    }

    public function canReceive($notification)
    {
        $setting = $this->notificationSettings()->whereName($notification)->first();

        // Ensure that user wants recieve the notification
        return !$setting || ($setting && $setting->status && $setting->pivot->status);
    }

    /**
     * Get the notification settings of the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function notificationSettings()
    {
        return $this
            ->morphToMany(NotificationType::class, 'user', 'notification_settings')
            ->withPivot('status');
    }
}
