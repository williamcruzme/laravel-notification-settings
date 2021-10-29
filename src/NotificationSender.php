<?php

namespace Millions\Notifications;

use Illuminate\Notifications\NotificationSender as BaseNotificationSender;
use Illuminate\Notifications\SendQueuedNotifications;
use Illuminate\Support\Str;

class NotificationSender extends BaseNotificationSender
{
    /**
     * Send the given notification to the given notifiable via a channel.
     *
     * @param  mixed  $notifiable
     * @param  string  $id
     * @param  mixed  $notification
     * @param  string  $channel
     * @return void
     */
    protected function sendToNotifiable($notifiable, $id, $notification, $channel)
    {
        $notification->timezone = $notifiable->timezone;

        parent::sendToNotifiable($notifiable, $id, $notification, $channel);
    }

    /**
     * Queue the given notification instances.
     *
     * @param  mixed  $notifiables
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    protected function queueNotification($notifiables, $notification)
    {
        $notifiables = $this->formatNotifiables($notifiables);

        $original = clone $notification;

        foreach ($notifiables as $notifiable) {
            $notificationId = Str::uuid()->toString();

            foreach ((array) $original->via($notifiable) as $channel) {
                $notification = clone $original;

                $notification->id = $notificationId;
                $notification->timezone = $notifiable->timezone;

                if (! is_null($this->locale)) {
                    $notification->locale = $this->locale;
                }

                $queue = $notification->queue;

                if (method_exists($notification, 'viaQueues')) {
                    $queue = $notification->viaQueues()[$channel] ?? null;
                }

                $this->bus->dispatch(
                    (new SendQueuedNotifications($notifiable, $notification, [$channel]))
                            ->onConnection($notification->connection)
                            ->onQueue($queue)
                            ->delay(is_array($notification->delay) ?
                                    ($notification->delay[$channel] ?? null)
                                    : $notification->delay
                            )
                            ->through(
                                array_merge(
                                    method_exists($notification, 'middleware') ? $notification->middleware() : [],
                                    $notification->middleware ?? []
                                )
                            )
                );
            }
        }
    }
}
