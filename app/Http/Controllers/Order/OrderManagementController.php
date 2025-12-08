<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderManagementController extends Controller
{
    // Admin: Update order status
    public function updateOrderStatus(Request $request, $order_id)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled,refunded,completed'
        ]);
        $order = Order::where('order_id', $order_id)
            ->with(['order_items.product'])
            ->firstOrFail();
        $order->status = $request->input('status');
        $order->save();

        // Send Telegram notification to customer if telegram_id exists
        $customer = $order->user;
        if ($customer && !empty($customer->telegram_id)) {
            $customer->notify(new \App\Notifications\OrderStatusUpdatedNotification($order));
        }

        return redirect()->back()->with('success', 'Order status updated to ' . ucfirst($order->status) . '.');
    }

    // Admin: Mark order as delivered
    public function markOrderDelivered($order_id)
    {
        $order = Order::where('order_id', $order_id)->firstOrFail();
        $order->status = 'delivered';
        $order->save();

        return redirect()->back()->with('success', 'Order marked as delivered.');
    }

    public function showOrderDetail($order_id)
    {
        $order = Order::with([
            'order_items.product',
             'payment', 
             'user',
             'shippingMethod'
              ])
            ->where('order_id', $order_id)
            ->firstOrFail();

        return view('order.Admin.order-detail', compact('order'));
    }

    public function adminDashboard(Request $request)
    {
        // Fetch necessary data for the admin dashboard
        $status = $request->input('status');
        $orderQuery = Order::orderBy('created_at', 'desc');
        if ($status) {
            $orderQuery->where('status', $status);
        }
        $recent_orders = $orderQuery->get();

        $total_orders = Order::count();
        $pending_orders = Order::where('status', 'pending')->count();
        $confirmed_orders = Order::where('status', 'confirmed')->count();
        $completed_orders = Order::where('status', 'completed')->count();
        $cancelled_orders = Order::where('status', 'cancelled')->count();
        $shipped_orders = Order::where('status', 'shipped')->count();
        $delivered_orders = Order::where('status', 'delivered')->count();

        return view('order.Admin.dashboard', 
            compact(
                'total_orders',
                'recent_orders',
                'pending_orders',
                'confirmed_orders',
                'completed_orders', 
                'cancelled_orders', 
                'shipped_orders',
                'delivered_orders',
                'status'
            )
        );
    }
}
