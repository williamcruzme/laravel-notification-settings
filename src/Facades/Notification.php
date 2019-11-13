<?php

namespace williamcruzme\NotificationSettings\Facades;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Facade;

class Notification extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'notification';
    }

    /**
     * Register the typical notifications routes for an application.
     *
     * @return void
     */
    public static function routes($namespace = '\\williamcruzme\\NotificationSettings\\Http\\Controllers')
    {
        Route::namespace($namespace)->group(function () {
            // Notifications
            Route::prefix('notifications')->group(function () {
                Route::get('/', 'NotificationController@index');
                Route::patch('/markAsRead', 'NotificationController@markAsRead');
                Route::delete('/', 'NotificationController@destroy');
            });

            // Notification Settings
            Route::prefix('settings/notifications')->group(function () {
                Route::get('/', 'NotificationSettingController@index');
                Route::patch('/{notificationType}', 'NotificationSettingController@update');
            });
        });
    }
}
