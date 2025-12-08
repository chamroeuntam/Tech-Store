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
        Schema::create('product_sales_reports', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sales_report_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();

            $table->unsignedInteger('quantity_sold')->default(0);
            $table->decimal('total_revenue', 10, 2)->default(0);
            $table->decimal('average_price', 10, 2)->default(0);

            $table->timestamps();

            $table->unique(['sales_report_id', 'product_id']);
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_sales_reports');
    }
};
