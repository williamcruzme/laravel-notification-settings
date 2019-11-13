<?php

namespace williamcruzme\NotificationSettings\Traits;

trait ManageNotifications
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $notifications = $this->guard()->user()->notifications()->paginate();

        return $this->sendResponse($notifications);
    }

    /**
     * Mark as read all resources from storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsRead()
    {
        $this->guard()->user()->unreadNotifications->markAsRead();

        return response()->json([
            'message' => 'Success',
        ]);
    }

    /**
     * Remove all resources from storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy()
    {
        $this->guard()->user()->notifications()->delete();

        return response()->json('', 204);
    }

    /**
     * Get the response for a successful listing notifications.
     *
     * @param  array  $response
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sendResponse($response)
    {
        return response()->json($response);
    }

    /**
     * Get the guard to be used during notifications management.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return auth()->guard();
    }
}
