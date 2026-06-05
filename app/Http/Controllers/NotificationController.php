<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Get all notifications for the authenticated user
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $notifications = auth()->user()->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Use cached unread count to avoid extra query
        $unreadCount = \Cache::remember(
            "user.{$request->user()->id}.unread_notifications_count",
            60, // cache for 1 minute
            function () use ($request) {
                return $request->user()->notifications()
                    ->where('is_read', false)
                    ->count();
            }
        );

        return response()->json([
            'success' => true,
            'data' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Get a specific notification by ID
     * 
     * @param Notification $notification
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Notification $notification)
    {
        // Verify the notification belongs to the authenticated user
        if ($notification->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'data' => $notification,
        ]);
    }

    /**
     * Mark a single notification as read
     * 
     * @param Notification $notification
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsRead(Notification $notification)
    {
        // Verify the notification belongs to the authenticated user
        if ($notification->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read',
            'data' => $notification,
        ]);
    }

    /**
     * Mark all notifications as read for the authenticated user
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAllAsRead()
    {
        auth()->user()->notifications()
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read',
        ]);
    }

    /**
     * Mark only the currently visible notifications as read.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function markVisibleAsRead(Request $request)
    {
        $ids = array_filter((array) $request->input('ids', []), fn($id) => is_numeric($id));

        if (empty($ids)) {
            return response()->json([
                'success' => true,
                'message' => 'No visible notifications to update',
            ]);
        }

        auth()->user()->notifications()
            ->whereIn('id', $ids)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Visible notifications marked as read',
        ]);
    }

    /**
     * Delete a notification
     * 
     * @param Notification $notification
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Notification $notification)
    {
        // Verify the notification belongs to the authenticated user
        if ($notification->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted',
        ]);
    }

    /**
     * Get unread notifications count
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function unreadCount()
    {
        $count = auth()->user()->notifications()
            ->where('is_read', false)
            ->count();

        return response()->json([
            'success' => true,
            'unread_count' => $count,
        ]);
    }

    /**
     * Get latest unread notifications (for header dropdown)
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function latestUnread()
    {
        $notifications = auth()->user()->notifications()
            ->where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $unreadCount = auth()->user()->notifications()
            ->where('is_read', false)
            ->count();

        return response()->json([
            'success' => true,
            'data' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }
}
