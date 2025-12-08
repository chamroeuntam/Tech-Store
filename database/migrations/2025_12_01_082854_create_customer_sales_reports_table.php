<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('customer_sales_reports', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sales_report_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->unsignedInteger('total_orders')->default(0);
            $table->decimal('total_spent', 10, 2)->default(0);
            $table->decimal('average_order_value', 10, 2)->default(0);

            $table->timestamps();

            $table->unique(['sales_report_id', 'user_id']);
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_sales_reports');
    }
};
