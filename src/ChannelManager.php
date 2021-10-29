<?php

namespace Millions\Notifications;

use Illuminate\Contracts\Bus\Dispatcher as Bus;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Notifications\ChannelManager as BaseChannelManager;

class ChannelManager extends BaseChannelManager
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
        $notificationType = get_class($notification);

        if (! NotificationType::isEnabled($notificationType)) {
            return;
        }

        $notifiables = $this->notifiables($notifiables, $notificationType);

        (new NotificationSender(
            $this, $this->container->make(Bus::class), $this->container->make(Dispatcher::class), $this->locale)
        )->send($notifiables, $notification);
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
        $notificationType = get_class($notification);

        if (! NotificationType::isEnabled($notificationType)) {
            return;
        }

        $notifiables = $this->notifiables($notifiables, $notificationType);

        (new NotificationSender(
            $this, $this->container->make(Bus::class), $this->container->make(Dispatcher::class), $this->locale)
        )->sendNow($notifiables, $notification, $channels);
    }

    /**
     * Get all the notifiables for a notification type.
     *
     * @param  \Illuminate\Support\Collection|array|mixed  $notifiables
     * @param  string  $notificationType
     * @return bool
     */
    public static function notifiables($notifiables, $notificationType)
    {
        $notifiables->load('notificationSettings');

        return $notifiables->filter(function ($notifiable) use ($notificationType) {
            return $notifiable->canReceive($notificationType);
        });
    }
}
