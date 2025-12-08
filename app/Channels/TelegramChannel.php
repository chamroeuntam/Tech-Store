<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;


class TelegramChannel
{
    /**
     * @param object $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     * @return void
     */
    public function send($notifiable, $notification)
    {
        if (!method_exists($notification, 'toTelegram')) {
            return;
        }
        $data = $notification->toTelegram($notifiable);
        if (empty($data['chat_id']) || empty($data['text'])) {
            return;
        }
        $token = config('services.telegram.bot_token') ?? env('TELEGRAM_BOT_TOKEN');
        if (!$token) return;
        $url = "https://api.telegram.org/bot{$token}/sendMessage";
        try {
            $payload = [
                'chat_id' => $data['chat_id'],
                'text' => $data['text'],
            ];
            if (!empty($data['parse_mode'])) {
                $payload['parse_mode'] = $data['parse_mode'];
            }
            $response = Http::post($url, $payload);
            logger()->info('Telegram API response', [
                'chat_id' => $data['chat_id'],
                'text' => $data['text'],
                'response' => $response->json(),
                'status' => $response->status(),
            ]);
        } catch (\Exception $e) {
            logger()->error('Telegram send error', [
                'chat_id' => $data['chat_id'],
                'text' => $data['text'],
                'error' => $e->getMessage(),
            ]);
        }
    }
}
