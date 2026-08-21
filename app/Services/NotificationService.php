<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\NotificationSetting;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    public function createNotification($userId, $type, $title, $message, $module, $data = null)
    {
        $user = User::find($userId);
        if (!$user) {
            return false;
        }

        // Check user notification settings
        $setting = NotificationSetting::where('user_id', $userId)
            ->where('notification_type', $type)
            ->where('module', $module)
            ->first();

        // If no specific setting, check if user wants notifications by default
        $appEnabled = $setting ? $setting->app_enabled : true;
        $emailEnabled = $setting ? $setting->email_enabled : false;
        $whatsappEnabled = $setting ? $setting->whatsapp_enabled : false;

        // Create notification if app notification is enabled
        if ($appEnabled) {
            $notification = Notification::create([
                'user_id' => $userId,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'module' => $module,
                'data' => $data,
                'is_read' => false,
                'sent_email' => false,
                'sent_whatsapp' => false,
            ]);

            // Send email if enabled
            if ($emailEnabled && $user->email) {
                $this->sendEmailNotification($user, $title, $message, $data);
                $notification->update(['sent_email' => true]);
            }

            // Send WhatsApp if enabled
            if ($whatsappEnabled && $user->whatsapp_number) {
                $this->sendWhatsAppNotification($user, $title, $message, $data);
                $notification->update(['sent_whatsapp' => true]);
            }

            return $notification;
        }

        // Even if app notification is disabled, send email/WhatsApp if enabled
        $sentEmail = false;
        $sentWhatsApp = false;

        if ($emailEnabled && $user->email) {
            $this->sendEmailNotification($user, $title, $message, $data);
            $sentEmail = true;
        }

        if ($whatsappEnabled && $user->whatsapp_number) {
            $this->sendWhatsAppNotification($user, $title, $message, $data);
            $sentWhatsApp = true;
        }

        // Still create notification record but mark as not shown in app
        if ($sentEmail || $sentWhatsApp) {
            return Notification::create([
                'user_id' => $userId,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'module' => $module,
                'data' => $data,
                'is_read' => false,
                'sent_email' => $sentEmail,
                'sent_whatsapp' => $sentWhatsApp,
            ]);
        }

        return false;
    }

    public function sendEmailNotification($user, $title, $message, $data = null)
    {
        try {
            Mail::raw($message, function ($message) use ($user, $title) {
                $message->to($user->email)
                    ->subject($title);
            });
            return true;
        } catch (\Exception $e) {
            \Log::error('Email notification failed: ' . $e->getMessage());
            return false;
        }
    }

    public function sendWhatsAppNotification($user, $title, $message, $data = null)
    {
        try {
            // Here you can integrate with WhatsApp API services like:
            // - Twilio WhatsApp API
            // - WhatsApp Business API
            // - Third-party services like MessageBird, Vonage, etc.
            
            // For demo purposes, we'll just log the notification
            // In production, replace this with actual WhatsApp API call
            
            $whatsappNumber = $this->formatWhatsAppNumber($user->whatsapp_number);
            
            // Example using Twilio (requires twilio/sdk package):
            /*
            $sid = env('TWILIO_SID');
            $token = env('TWILIO_TOKEN');
            $twilio = new Client($sid, $token);
            
            $message = $twilio->messages->create(
                "whatsapp:$whatsappNumber",
                [
                    "from" => "whatsapp:" . env('TWILIO_WHATSAPP_FROM'),
                    "body" => "*$title*\n\n$message"
                ]
            );
            */
            
            // Log the notification for demo
            \Log::info("WhatsApp notification to {$whatsappNumber}: {$title} - {$message}");
            
            return true;
        } catch (\Exception $e) {
            \Log::error('WhatsApp notification failed: ' . $e->getMessage());
            return false;
        }
    }

    private function formatWhatsAppNumber($number)
    {
        // Remove all non-numeric characters
        $number = preg_replace('/[^0-9]/', '', $number);
        
        // Add country code if not present (assuming Pakistan +92)
        if (strlen($number) === 10 && strpos($number, '0') === 0) {
            $number = '92' . substr($number, 1);
        }
        
        return $number;
    }

    public function markAsRead($notificationId)
    {
        $notification = Notification::find($notificationId);
        if ($notification && $notification->user_id === auth()->id()) {
            $notification->update(['is_read' => true]);
            return true;
        }
        return false;
    }

    public function markAllAsRead($userId)
    {
        Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    public function getUnreadCount($userId)
    {
        return Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->count();
    }

    public function getUserNotifications($userId, $limit = 10)
    {
        return Notification::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function updateNotificationSetting($userId, $notificationType, $module, $emailEnabled, $appEnabled, $whatsappEnabled = false)
    {
        return NotificationSetting::updateOrCreate(
            [
                'user_id' => $userId,
                'notification_type' => $notificationType,
                'module' => $module,
            ],
            [
                'email_enabled' => $emailEnabled,
                'app_enabled' => $appEnabled,
                'whatsapp_enabled' => $whatsappEnabled,
            ]
        );
    }

    public function getUserNotificationSettings($userId)
    {
        return NotificationSetting::where('user_id', $userId)->get();
    }
}