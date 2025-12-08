<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;

class PaymentMethodSeeder extends Seeder
{
    public function run()
    {
        PaymentMethod::create([
            'name' => 'Credit Card',
            'description' => 'Pay with Visa, MasterCard, or Amex.',
            'is_active' => true,
        ]);
        PaymentMethod::create([
            'name' => 'PayPal',
            'description' => 'Pay securely using PayPal.',
            'is_active' => true,
        ]);
        PaymentMethod::create([
            'name' => 'Bank Transfer',
            'description' => 'Direct bank transfer.',
            'is_active' => true,
        ]);
        PaymentMethod::create([
            'name' => 'KHQR',
            'description' => 'Pay with Bakong KHQR code.',
            'is_active' => true,
        ]);
    }
}
