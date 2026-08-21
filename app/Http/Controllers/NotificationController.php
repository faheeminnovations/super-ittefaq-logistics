<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\NotificationSetting;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index()
    {
        $notifications = $this->notificationService->getUserNotifications(auth()->id());
        $unreadCount = $this->notificationService->getUnreadCount(auth()->id());
        
        return view('pages.notifications', compact('notifications', 'unreadCount'));
    }

    public function getNotifications(): JsonResponse
    {
        $notifications = $this->notificationService->getUserNotifications(auth()->id(), 5);
        $unreadCount = $this->notificationService->getUnreadCount(auth()->id());
        
        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    public function markAsRead(Request $request): JsonResponse
    {
        $request->validate([
            'notification_id' => 'required|exists:notifications,id',
        ]);

        $result = $this->notificationService->markAsRead($request->notification_id);
        
        return response()->json([
            'success' => $result,
            'unread_count' => $this->notificationService->getUnreadCount(auth()->id()),
        ]);
    }

    public function markAllAsRead(): JsonResponse
    {
        $this->notificationService->markAllAsRead(auth()->id());
        
        return response()->json([
            'success' => true,
            'unread_count' => 0,
        ]);
    }

    public function settings()
    {
        $settings = $this->notificationService->getUserNotificationSettings(auth()->id());
        $modules = ['billing', 'customer', 'vehicle', 'driver', 'job', 'trip', 'dispatch', 'expense', 'invoice'];
        $types = ['save', 'delete', 'update'];
        
        return view('pages.notification-settings', compact('settings', 'modules', 'types'));
    }

    public function updateSettings(Request $request): JsonResponse
    {
        $request->validate([
            'settings' => 'required|array',
            'settings.*.notification_type' => 'required|in:save,delete,update',
            'settings.*.module' => 'required',
            'settings.*.email_enabled' => 'boolean',
            'settings.*.app_enabled' => 'boolean',
            'settings.*.whatsapp_enabled' => 'boolean',
        ]);

        foreach ($request->settings as $setting) {
            $this->notificationService->updateNotificationSetting(
                auth()->id(),
                $setting['notification_type'],
                $setting['module'],
                $setting['email_enabled'] ?? false,
                $setting['app_enabled'] ?? true,
                $setting['whatsapp_enabled'] ?? false
            );
        }

        return response()->json(['success' => true]);
    }

    public function delete(Request $request): JsonResponse
    {
        $request->validate([
            'notification_id' => 'required|exists:notifications,id',
        ]);

        $notification = Notification::where('id', $request->notification_id)
            ->where('user_id', auth()->id())
            ->first();

        if ($notification) {
            $notification->delete();
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 403);
    }
}
