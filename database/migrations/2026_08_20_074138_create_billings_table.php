<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('billings', function (Blueprint $table) {
            $table->id();
            $table->integer('sr')->nullable();
            $table->date('date');
            $table->string('vehicle_no');
            $table->string('customer_name');
            $table->string('contact_number')->nullable();
            $table->integer('bags')->default(0);
            $table->string('delivery_point');
            $table->decimal('km_covered', 8, 2)->default(0);
            $table->decimal('rent', 10, 2)->default(0);
            $table->decimal('advance', 10, 2)->default(0);
            $table->date('advance_date')->nullable();
            $table->string('guarantor')->nullable();
            $table->decimal('dues', 10, 2)->default(0);
            $table->enum('status', ['Pending', 'Paid', 'Partial'])->default('Pending');
            $table->string('billing_month')->nullable(); // Format: Y-m for filtering
            $table->timestamps();
            
            // Indexes for better performance
            $table->index('vehicle_no');
            $table->index('customer_name');
            $table->index('billing_month');
            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billings');
    }
};
