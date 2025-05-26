<?php
namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Mark all the current user’s unread notifications as read.
     *
     * @return RedirectResponse
     */
    public function markAllRead(): RedirectResponse
    {
        Auth::user()->unreadNotifications->markAsRead();
        return back();
    }

    /**
     * Mark a single notification as read, then redirect to its URL.
     *
     * @param  string  $id
     * @return RedirectResponse
     */
    public function markRead(string $id): RedirectResponse
    {
        $note = Auth::user()
            ->unreadNotifications()
            ->findOrFail($id);

        $note->markAsRead();

        return redirect($note->data['url'] ?? '/');
    }
}

