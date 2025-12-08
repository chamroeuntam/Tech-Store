<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;


class OrderStatusUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    public function via($notifiable)
    {
        return [\App\Channels\TelegramChannel::class];
    }

    public function toTelegram($notifiable)
    {
        $order = $this->order;

        // Stop if user has no Telegram ID
        if (empty($notifiable->telegram_id)) {
            return [];
        }

        /**
         * -----------------------------
         *  Pretty status labels
         * -----------------------------
         */
        $statusMap = [
            'pending'    => 'Pending ⏳',
            'processing' => 'Processing 🔧',
            'shipped'    => 'Shipped 🚚',
            'delivered'  => 'Delivered 📦',
            'cancelled'  => 'Cancelled ❌',
        ];
        $prettyStatus = $statusMap[strtolower($order->status)] ?? ucfirst($order->status);

        $items = $order->order_items ?? collect();
        $itemsList = $items->map(function ($item) {
            $name = $item->product->name ?? 'Unknown Product';
            $qty = $item->quantity;
            $price = number_format($item->price, 2);
            $subtotal = number_format($item->quantity * $item->price, 2);
            
            return "• *{$name}*\n  Qty: {$qty} × \${$price} = \${$subtotal}";
        })->implode("\n\n");
        if (empty($itemsList)) {
            $itemsList = "_No items found_";
        }

        /**
         * -----------------------------
         *  Build final message
         * -----------------------------
         */
        $message =
            "🔔 *Order Status Updated!*\n" .
            "-----------------------------------------------------------\n" .
            "🧾 *Order ID:* {$order->order_id}\n" .
            "👤 *Customer:* {$order->first_name} {$order->last_name}\n" .
            "💬 *New Status:* {$prettyStatus}\n" .
            "-----------------------------------------------------------\n" .
            "🛍 *Items:*\n{$itemsList}\n" .
            "-----------------------------------------------------------\n" .
            "💵 *Total:* " . number_format($order->total_amount, 2) . "$\n" .
            "🚚 *Shipping Cost:* " . number_format($order->shipping_cost, 2) . "$\n\n" .
            "👉 You can check your updated order in your account.";

        return [
            'chat_id' => $notifiable->telegram_id,
            'text' => $message,
            'parse_mode' => 'Markdown',
        ];
    }
}
