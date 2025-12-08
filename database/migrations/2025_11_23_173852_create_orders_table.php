<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            // ✅ Public Order UUID
            $table->uuid('order_id')->unique();

            // ✅ User Foreign Key
            $table->foreignId('user_id')
                  ->constrained()
                  ->cascadeOnDelete();

            // ✅ Order Status
            $table->string('status', 20)->default('pending');

            // ✅ Billing Info
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('email');
            $table->string('phone', 20);

            // ✅ Shipping Info
            $table->string('shipping_first_name', 100)->nullable();
            $table->string('shipping_last_name', 100)->nullable();
            $table->string('shipping_phone', 20)->nullable();
            $table->string('shipping_email', 255)->nullable();
            $table->text('shipping_address');

            // ✅ FIXED SHIPPING METHOD FOREIGN KEY
            $table->foreignId('shipping_method_id')
                  ->nullable()
                  ->constrained('shipping_methods')
                  ->nullOnDelete();

            // ✅ FIXED PAYMENT METHOD FOREIGN KEY
            $table->foreignId('payment_method_id')
                  ->nullable()
                  ->constrained('payment_methods')
                  ->nullOnDelete();

            // ✅ Amounts
            $table->decimal('total_amount', 10, 2);
            $table->decimal('shipping_cost', 10, 2)->default(0);

            // ✅ Notes
            $table->text('order_notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
