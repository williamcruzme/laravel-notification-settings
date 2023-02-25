<?php

namespace Millions\Notifications;

use Millions\Notifications\NotificationType;

trait Schedulable
{
    public function viaQueues()
    {
        if (! $this->shouldSchedule()) {
            return null;
        }

        $notificationType = get_class($this);
        $settings = cache()->tags('notification_settings')->rememberForever("notifications:$notificationType", function () use ($notificationType) {
            return NotificationType::whereName($notificationType)->first();
        });

        $range = optional($settings)->schedule;

        if (empty($range)) {
            return null;
        }

        $timezone = $this->timezone ?? config('app.timezone');
        $currentHour = now($timezone)->hour;

        // Send immediately if the current hour is in the range
        if ($currentHour >= (int) $range[0] && $currentHour < (int) $range[1]) {
            $this->delay = null;
        } else {
            // Send to the next business hour
            $this->delay = today($timezone)->addHour((int) $range[0]);

            // Send to the next day if we're past the end of the range
            if ($currentHour >= (int) $range[1]) {
                $this->delay->addDay();
            };
        }

        return null;
    }

    public function shouldSchedule()
    {
        return true;
    }
}
