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

    /**
     * Send the given notification.
     *
     * @param  mixed  $instance
     * @return void
     */
    public function notify($instance)
    {
        $notificationType = get_class($instance);

        if (NotificationType::isEnabled($notificationType) && $this->canReceive($notificationType)) {
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
        $notificationType = get_class($instance);

        if (NotificationType::isEnabled($notificationType) && $this->canReceive($notificationType)) {
            $this->_notifyNow($instance, $channels);
        }
    }

    /**
     * Check if user wants recieve the notification.
     *
     * @param  string  $notificationType
     * @return bool
     */
    public function canReceive($notificationType)
    {
        $setting = $this->notificationSettings->where('name', $notificationType)->first();

        return ! $setting || $setting->pivot->status;
    }
}
