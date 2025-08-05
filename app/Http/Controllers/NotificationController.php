<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()->notifications()->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * แสดงรายการ unread notifications
     */
    public function unread()
    {
        $notifications = Auth::user()->unreadNotifications()->paginate(20);

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => Auth::user()->unreadNotifications()->count()
        ]);
    }

    /**
     * ทำเครื่องหมาย notification เป็น read
     */
    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'unread_count' => Auth::user()->unreadNotifications()->count()
        ]);
    }

    /**
     * ทำเครื่องหมาย notifications ทั้งหมดเป็น read
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications()->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'unread_count' => 0
        ]);
    }

    /**
     * ลบ notification
     */
    public function destroy($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->delete();

        return response()->json([
            'success' => true,
            'unread_count' => Auth::user()->unreadNotifications()->count()
        ]);
    }

    /**
     * ลบ notifications ทั้งหมด
     */
    public function destroyAll()
    {
        Auth::user()->notifications()->delete();

        return response()->json([
            'success' => true,
            'unread_count' => 0
        ]);
    }
}
