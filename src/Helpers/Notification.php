<?php

namespace Millions\Notifications\Helpers;

use Illuminate\Notifications\ChannelManager;

class Notification extends ChannelManager
{
    /**
     * Send the given notification to the given notifiable entities.
     *
     * @param  \Illuminate\Support\Collection|array|mixed  $notifiables
     * @param  mixed  $notification
     * @return void
     */
    public function send($notifiables, $notification)
    {
        $notificationName = get_class($notification);

        $notifiables = collect($notifiables)->filter(function ($notifiable) use ($notificationName) {
            return $notifiable->canReceive($notificationName);
        });

        return parent::send($notifiables, $notification);
    }

    /**
     * Send the given notification immediately.
     *
     * @param  \Illuminate\Support\Collection|array|mixed  $notifiables
     * @param  mixed  $notification
     * @param  array|null  $channels
     * @return void
     */
    public function sendNow($notifiables, $notification, array $channels = null)
    {
        $notificationName = get_class($notification);

        $notifiables = collect($notifiables)->filter(function ($notifiable) use ($notificationName) {
            return $notifiable->canReceive($notificationName);
        });

        return parent::send($notifiables, $notification, $channels);
    }
}
