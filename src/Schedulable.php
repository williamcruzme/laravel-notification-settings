<?php

namespace Millions\Notifications;

use Millions\Notifications\NotificationType;

trait Schedulable
{
    public function viaQueues()
    {
        $notificationName = get_class($this);

        $settings = cache()->rememberForever("notifications:$notificationName", function () use ($notificationName) {
            return NotificationType::whereName($notificationName)->first();
        });

        $range = optional($settings)->schedule;

        if (empty($range)) {
            return null;
        }

        $currentTime = now('America/New_York');
        $currentHour = $currentTime->hour;

        if ($currentHour >= (int) $range[0] && $currentHour < (int) $range[1]) {
            $this->delay = $currentTime;
        } else {
            // Send to the next business hour
            $this->delay = today('America/New_York')->addDay()->addHours((int) $range[0]);
        }

        return null;
    }
}
