<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use KHQR\BakongKHQR;
use KHQR\Models\MerchantInfo;
use KHQR\Helpers\KHQRData;
use App\Models\Payment;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\User;
use App\Notifications\OrderPlacedNotification;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\ErrorCorrectionLevel;

class PaymentController extends Controller
{
    
    // Create payment + Base64 QR
    public function create(Request $request)
    {
        $invoiceId = 'INV' . strtoupper(Str::random(5));
        // Get amount from request if present, fallback to 1
        $amount = $request->input('amount', 1);
        $expiresAt = Carbon::now()->addMinutes(5);

        // --- Generate KHQR string ---
        $merchant = new MerchantInfo(
            'chamroeun_tam@wing',
            'Tech Store, By RylRaoen',
            'PHNOM PENH',
            'MID001',
            'Dev Bank',
            null,
            KHQRData::CURRENCY_KHR,
            $amount
        );

        $result = BakongKHQR::generateMerchant($merchant);
        if (!isset($result->data['qr'])) {
            abort(500, "Cannot generate KHQR string");
        }
        $qrString = $result->data['qr'];

        // --- Build QR using Endroid v6+ API ---
        $builder = new Builder(new PngWriter());
        $resultQr = $builder->build(
            data: $qrString,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::Low,
            size: 300,
            margin: 10,
        );

        $base64 = base64_encode($resultQr->getString());
        logger()->info('QR base64 preview', [
            'first_30' => substr($base64, 0, 30),
            'length' => strlen($base64),
        ]);

        // --- Save payment record ---
        // Get order_id from request (should be numeric primary key)
        $orderId = $request->input('order_id');
        if (!$orderId) {
            abort(400, 'Missing order_id for payment creation');
        }
        $existingPayment = Payment::where('order_id', $orderId)
            ->where('payment_method', $request->input('payment_method', 1))
            ->where('status', 'pending')
            ->first();
        if ($existingPayment) {
            // Always set expires_at if missing or expired
            try {
                $needsUpdate = false;
                $updates = [];
                $now = Carbon::now();
                $expired = $existingPayment->expires_at && $now->gt($existingPayment->expires_at);
                if ($expired || empty($existingPayment->expires_at)) {
                    $updates['expires_at'] = $now->addMinutes(5);
                    $updates['status'] = 'pending';
                    $needsUpdate = true;
                }
                if ($expired || empty($existingPayment->qr_string) || empty($existingPayment->qr_image)) {
                    $updates['qr_string'] = $qrString;
                    $updates['qr_image'] = $base64;
                    $needsUpdate = true;
                }
                if ($needsUpdate) {
                    $existingPayment->update($updates);
                    logger()->info('Regenerated QR and expiry for reused payment', ['payment_id' => $existingPayment->id, 'expires_at' => $updates['expires_at'] ?? null]);
                }
            } catch (\Exception $e) {
                logger()->error('Failed to update existing payment QR/expiry', ['id' => $existingPayment->id, 'err' => $e->getMessage()]);
            }

            // Reuse existing pending payment
            return redirect()->route('pay.view', ['invoice' => $existingPayment->invoice_id]);
        }
        $payment = Payment::create([
            'order_id'   => $orderId,
            'payment_method' => $request->input('payment_method', 1),
            'invoice_id' => $invoiceId,
            'amount'     => $amount,
            'qr_string'  => $qrString,
            'qr_image'   => $base64,
            'expires_at' => $expiresAt,
            'status'     => 'pending',
            'name'       => 'KHQR Payment',
            'description'=> 'Payment via KHQR',
            'is_active'  => true,
        ]);
        return redirect()->route('pay.view', ['invoice' => $invoiceId]);
    }

    // Show QR page
    public function view($invoice)
    {
        $payment = Payment::where('invoice_id', $invoice)->firstOrFail();
        return view('pay', compact('payment'));
    }

    // Check payment status
    public function check($invoice)
    {
        $payment = Payment::where('invoice_id', $invoice)->firstOrFail();

        // If already in a terminal state, return current status (but ensure expired is set when past expiry)
        if ($payment->status !== 'pending') {
            if ($payment->expires_at && Carbon::now()->gt($payment->expires_at) && $payment->status !== 'expired') {
                $payment->update(['status' => 'expired']);
                return response()->json(['status' => 'expired']);
            }

            return response()->json(['status' => $payment->status]);
        }

        // Ensure we do not mark a payment as paid after expiry
        if ($payment->expires_at && Carbon::now()->gt($payment->expires_at)) {
            $payment->update(['status' => 'expired']);
            return response()->json(['status' => 'expired']);
        }

        // If a Bakong token is configured, use the official API to verify transaction by MD5
        $token = env('BAKONG_TOKEN') ?: config('services.bakong.token') ?: null;

        if ($token) {
            try {
                $bk = new BakongKHQR($token);
                $md5 = md5($payment->qr_string);
                $res = $bk->checkTransactionByMD5($md5);

                // Inspect response for clear paid/completed indications
                $paid = $this->responseIndicatesPaid($res);

                if ($paid) {
                    $payment->update(['status' => 'paid', 'paid_at' => Carbon::now()]);
                    // Send notifications
                    $order = Order::find($payment->order_id);
                    if ($order) {
                        $recipients = User::whereIn('role', ['admin', 'staff'])->get();
                        foreach ($recipients as $recipient) {
                            $recipient->notify(new OrderPlacedNotification($order));
                        }
                        $customer = User::find($order->user_id);
                        if ($customer) {
                            $customer->notify(new OrderPlacedNotification($order));
                        }
                    }
                    return response()->json(['status' => 'paid', 'raw' => $res]);
                }

                return response()->json(['status' => $payment->status, 'raw' => $res]);
            } catch (\Exception $e) {
                // Log and fall back to previous behavior (do not mark paid automatically)
                logger()->error('Bakong check failed: '.$e->getMessage());
            }
        }

        // No token configured or API failed — keep existing behavior but do NOT mark expired as paid
        // Only mark as paid if confirmed by Bakong API (above)
        return response()->json(['status' => $payment->status]);
    }

    /**
     * Recursive scan of API response to detect paid/completed status indicators.
     * This is a best-effort heuristic — replace with exact logic based on your provider response schema.
     *
     * @param  array<mixed>|mixed  $res
     */
    private function responseIndicatesPaid(mixed $res): bool
    {
        if (! is_array($res)) {
            return false;
        }

        $searchStrings = ['paid', 'success', 'completed', 'settled'];

        $stack = [$res];
        while ($stack) {
            $current = array_pop($stack);
            if (is_array($current)) {
                foreach ($current as $k => $v) {
                    if (is_string($v)) {
                        $lower = strtolower($v);
                        foreach ($searchStrings as $s) {
                            if (str_contains($lower, $s)) {
                                return true;
                            }
                        }
                    } elseif (is_array($v)) {
                        $stack[] = $v;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Simulate a payment for testing: marks pending -> paid if not expired.
     * POST /pay/{invoice}/simulate-pay
     */
    public function simulate(Request $request, $invoice)
    {
        $payment = Payment::where('invoice_id', $invoice)->firstOrFail();

        // If already terminal, just redirect back
        if ($payment->status !== 'pending') {
            return response()->json(['status' => $payment->status]);
        }

        // Prevent marking as paid after expiry
        if (Carbon::now()->gt($payment->expires_at)) {
            $payment->update(['status' => 'expired']);
            return response()->json(['status' => 'expired']);
        }

        $payment->update(['status' => 'paid', 'paid_at' => Carbon::now()]);

        return response()->json(['status' => 'paid']);
    }

    /**
     * Show success page for a paid invoice.
     */
    public function success($invoice)
    {
        $payment = Payment::where('invoice_id', $invoice)->firstOrFail();
        if ($payment->status !== 'paid') {
            // If not paid, redirect back to view
            return redirect()->route('pay.view', ['invoice' => $invoice]);
        }
        // Redirect to user order detail page for consistent experience
        if ($payment->order_id) {
            // Detect if order_id is numeric or UUID
            if (is_numeric($payment->order_id)) {
                return redirect()->route('orders.order_detail_by_id', ['id' => $payment->order_id]);
            } else {
                return redirect()->route('orders.order_detail', ['order_id' => $payment->order_id]);
            }
        }
        // If no order, show payment info only
        return view('order.order-detail', ['order' => null, 'payment' => $payment, 'invoice' => $invoice]);
    }
}
