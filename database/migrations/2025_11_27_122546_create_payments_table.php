<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->string('invoice_id')->unique()->nullable();
            $table->unsignedBigInteger('payment_method')->nullable();
            $table->foreign('payment_method')->references('id')->on('payment_methods')->onDelete('set null');
            $table->decimal('amount', 10, 2)->default(0);
            $table->string('status')->default('pending');
            $table->string('name');
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('qr_string')->nullable();
            $table->text('qr_image')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
