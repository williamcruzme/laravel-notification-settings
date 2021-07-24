<?php

namespace Millions\Notifications;

use Illuminate\Http\Request;
use Millions\Notifications\NotificationType;

trait ManageNotificationSettings
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $types = NotificationType::all();
        $settings = $this->guard()->user()->notificationSettings;

        $types->each(function ($type) use ($settings) {
            $setting = $settings->find($type->id);
            $type->status = $setting ? $setting->pivot->status : true;
        });

        return $this->sendResponse($types);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Millions\Notifications\NotificationType  $notificationType
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, NotificationType $notificationType)
    {
        $request->validate($this->rules(), $this->validationErrorMessages());

        $this->guard()->user()->notificationSettings()->syncWithoutDetaching($notificationType, [
            'status' => $request->boolean('status'),
        ]);

        return response()->json([
            'message' => 'Success',
        ]);
    }

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

    /**
     * Get the guard to be used during notification settings.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return auth()->guard();
    }
}
