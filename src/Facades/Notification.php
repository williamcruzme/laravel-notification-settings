<?php

namespace Millions\Notifications\Facades;

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Route;
use Millions\Notifications\ChannelManager;

class Notification extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return ChannelManager::class;
    }

    /**
     * Register the typical notifications routes for an application.
     *
     * @param  string  $namespace
     * @return void
     */
    public static function routesForSettings($namespace = '\\Millions\\Notifications\\Http\\Controllers')
    {
        if (! str_starts_with('\\', $namespace)) {
            $namespace = "\\$namespace";
        }

        Route::prefix('settings/notifications')->namespace($namespace)->group(function () {
            Route::get('/', 'NotificationSettingController@index');
            Route::patch('{notificationType}', 'NotificationSettingController@update');
        });
    }
}
