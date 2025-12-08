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
        Schema::create('sales_reports', function (Blueprint $table) {
            $table->id();

            $table->enum('report_type', ['daily', 'weekly', 'monthly', 'yearly', 'custom']);
            $table->date('start_date');
            $table->date('end_date');

            $table->unsignedInteger('total_orders')->default(0);
            $table->decimal('total_revenue', 12, 2)->default(0);
            $table->unsignedInteger('total_items_sold')->default(0);
            $table->decimal('average_order_value', 10, 2)->default(0);

            $table->foreignId('generated_by')->constrained('users')->cascadeOnDelete();
            $table->boolean('is_cached')->default(true);

            $table->timestamps();

            $table->unique(['report_type', 'start_date', 'end_date']);
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_reports');
    }
};
