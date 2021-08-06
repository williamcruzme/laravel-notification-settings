<?php

namespace Millions\Notifications;

use Millions\Notifications\NotificationType;

trait Schedulable
{
    public function viaQueues()
    {
        $notificationType = get_class($this);
        $settings = cache()->rememberForever("notifications:$notificationType", function () use ($notificationType) {
            return NotificationType::whereName($notificationType)->first();
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
