<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderPlacedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    /**
     * Notification channels
     */
    public function via(object $notifiable): array
    {
        return [
            'mail',
            'database',
            \App\Channels\TelegramChannel::class
        ];
    }

    /**
     * Mail Notification
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Order Placed')
            ->greeting('Hello!')
            ->line('A new order has been placed.')
            ->line('Order ID: ' . $this->order->order_id)
            ->line('Customer: ' . $this->order->first_name . ' ' . $this->order->last_name)
            ->line('Total: $' . number_format($this->order->total_amount, 2))
            ->action('View Order', url('/orders/' . $this->order->id))
            ->line('Thank you!');
    }

    /**
     * Database Notification
     */
    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->order_id,
            'customer' => "{$this->order->first_name} {$this->order->last_name}",
            'total' => $this->order->total_amount,
            'order_url' => url('/orders/' . $this->order->id),
        ];
    }

    /**
     * Telegram Notification
     */
    public function toTelegram($notifiable)
    {
        $order = $this->order;

        /**
         * ------------------------------------------------
         *  Build detailed product list (name, qty, price)
         * ------------------------------------------------
         */
        $items = $order->order_items ?? collect();

        $itemsList = $items->map(function ($item) {
            $name = $item->product->name ?? 'Unknown Product';
            $qty = $item->quantity;
            $price = number_format($item->price, 2);
            $subtotal = number_format($item->quantity * $item->price, 2);

            return "• *{$name}*\n  Qty: {$qty} × \${$price} = \${$subtotal}";
        })->implode("\n\n");

        if ($items->count() === 0) {
            $itemsList = "_No items found_";
        }

        /**
         * ------------------------------------------------
         *  STAFF / ADMIN TELEGRAM GROUP MESSAGE
         * ------------------------------------------------
         */
        if (in_array($notifiable->role, ['admin', 'staff'])) {

            $chatId = env('TELEGRAM_STAFF_GROUP_ID');

            $message =
                "📦 *New Order Placed*\n" .
                "--------------------------------------------------\n" .
                "🧾 *Order ID:* {$order->order_id}\n" .
                "👤 *Customer:* {$order->first_name} {$order->last_name}\n" .
                "🛍 *Items:*\n{$itemsList}\n" .
                "--------------------------------------------------\n" .
                "💵 *Total:* " . number_format($order->total_amount, 2) . "$\n" .
                "🚚 *Shipping:* {$order->shippingMethod->name} (" . number_format($order->shippingMethod->cost, 2) . "$)\n" .
                "🔗 View Order: " . url('/admin/orders/' . $order->id);

            return [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'Markdown'
            ];
        }

        /**
         * ------------------------------------------------
         *  CUSTOMER TELEGRAM MESSAGE
         * ------------------------------------------------
         */
        if (!empty($notifiable->telegram_id)) {

            $message =
                "✅ *Order Confirmed!*\n" .
                "----------------------------------------------------------\n" .
                "🧾 *Order ID:* {$order->order_id}\n" .
                "👤 *Name:* {$order->first_name} {$order->last_name}\n" .
                "💬 *Items:*\n{$itemsList}\n" .
                "----------------------------------------------------------\n" .
                "💵 *Total:* " . number_format($order->total_amount, 2) . "$\n" .
                "🚚 *Shipping:* {$order->shippingMethod->name} (" . number_format($order->shippingMethod->cost, 2) . "$)\n\n" .
                "Thank you for shopping with us! We will notify you when your order is shipped.";

            return [
                'chat_id' => $notifiable->telegram_id,
                'text' => $message,
                'parse_mode' => 'Markdown'
            ];
        }

        return [];
    }
}
