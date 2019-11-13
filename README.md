
<h1 align="center" style="text-align:center">Laravel Notification Settings</h1>

<p align="center">
  <a href="https://laravel.com/"><img src="https://badgen.net/badge/Laravel/5.5.x/red" alt="Laravel"></a>
  <a href="https://laravel.com/"><img src="https://badgen.net/badge/Laravel/6.x/red" alt="Laravel"></a>
  <a href="https://github.com/williamcruzme/laravel-notification-settings"><img src="https://img.shields.io/github/license/williamcruzme/laravel-notification-settings" alt="GitHub"></a>
</p>

<br>

laravel-notification-settings is a [Laravel](https://laravel.com/) package that allows you to check the notification settings before send them.

- [Installation](#-installation)
- [Getting Started](#-getting-started)
- [Usage](#-usage)
- [Routes](#-routes)
- [Customizing](#-customizing)

## 💿 Installation

```bash
composer require williamcruzme/laravel-notification-settings
```

## 🏁 Getting Started

### 1. Adding trait

In your user model add the `Notifiable` trait. This trait supports custom guards:

```php
<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use williamcruzme\NotificationSettings\Traits\Notifiable;

class User extends Authenticatable
{
    use Notifiable;
}
```

### 2. Running migrations

```bash
php artisan migrate
```

Sometimes you may need to customize the migrations. Using the `vendor:publish` command you can export the migrations:

```bash
php artisan vendor:publish --tag=migrations
```

### 3. Creating seeder

Add all notifications that require settings. Notification that are not added will be sent without verification:

```bash
php artisan make:seeder NotificationTypesTableSeeder
```

```php
/**
 * Run the database seeds.
 *
 * @return void
 */
public function run()
{
    DB::table('notification_types')->insert([
        'name' => 'App\\Notifications\\Welcome',
        'display_text' => 'Welcome message',
        'status' => true,
    ]);
}
```

### 4. Adding routes
Using the `Notification` facade to import the routes for manage the settings:

```php
Notification::routes();
```

## 🚀 Usage

### Using The Notifiable Trait

This trait contains one method that may be used to send notifications: `notify`. The `notify` method check if the user wants to receive it, and expects to receive a notification instance:

```php
use App\Notifications\InvoicePaid;

$user->notify(new InvoicePaid($invoice));
```

### Using The Notification Facade

Alternatively, you may send notifications via the `Notification` facade. This is useful primarily when you need to send a notification to multiple notifiable entities such as a collection of users. To send notifications using the facade, pass all of the notifiable entities and the notification instance to the `send` method:

```php
use williamcruzme\NotificationSettings\Facades\Notification;

Notification::send($users, new InvoicePaid($invoice));
```

## 🌐 Routes

### Get notifications

| Method |       URI        |
| ------ | ---------------- |
| GET    | `/notifications` |

### Mark as read

| Method |             URI             |
| ------ | --------------------------- |
| PATCH  | `/notifications/markAsRead` |

### Delete notification

| Method |                URI                |
| ------ | --------------------------------- |
| DELETE | `/notifications/{notificationId}` |

### Get notification settings

| Method |            URI            |
| ------ | ------------------------- |
| GET    | `/settings/notifications` |

### Update notification setting

| Method |                      URI                       |
| ------ | ---------------------------------------------- |
| PATCH  | `/settings/notifications/{notificationTypeId}` |

#### Body Params

```json
{
    "status": true, // false
}
```

## 🎨 Customizing

First of all, create your own `NotificationController` `NotificationSettingController` controllers and add the `ManageNotifications` `ManageNotificationSettings` traits.

Second, modify the namespace of the `Notification` facade routes:

```php
Notification::routes('App\Http\Controllers');
```

### Custom request validations

The `rules` `validationErrorMessages` methods in the `NotificationSettingController` allows you override the default request validations:

```php
<?php

namespace App\Http\Controllers;

use williamcruzme\NotificationSettings\Traits\ManageNotificationSettings;

class NotificationSettingController extends Controller {

    use ManageNotificationSettings;
    
    /**
     * Get the notification settings validation rules.
     *
     * @return array
     */
    protected function rules()
    {
        return [
            'status' => ['required', 'boolean'],
        ];
    }

    /**
     * Get the notification settings validation error messages.
     *
     * @return array
     */
    protected function validationErrorMessages()
    {
        return [];
    }
}
```

### Custom response

The `sendResponse` method in the `NotificationController` `NotificationSettingController` allows you override the default response:

```php
<?php

namespace App\Http\Controllers;

use williamcruzme\NotificationSettings\Traits\ManageNotifications;

class NotificationController extends Controller {

    use ManageNotifications;
    
    /**
     * Get the response for a successful listing notification settings.
     *
     * @param  array  $response
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sendResponse($response)
    {
        return response()->json($response);
    }
}
```

### Custom guards

The `guard` method in the `NotificationController` `NotificationSettingController` allows you override the default guard:

```php
<?php

namespace App\Http\Controllers;

use williamcruzme\NotificationSettings\Traits\ManageNotifications;

class NotificationController extends Controller {

    use ManageNotifications;
    
    /**
     * Get the guard to be used during notifications management.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return auth('admin')->guard();
    }
}
```

## 🚸 Contributing

You are welcome to contribute to this project, but before you do, please make sure you read the [contribution guide](CONTRIBUTING.md).

## 🔒 License

MIT
