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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->date('expense_date');
            $table->string('category');
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles');
            $table->decimal('amount', 10, 2);
            $table->string('submitted_by');
            $table->enum('status', ['approved', 'pending_review', 'rejected'])->default('pending_review');
            $table->text('description')->nullable();
            $table->string('receipt_url')->nullable();
            $table->foreignId('driver_id')->nullable()->constrained('drivers');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
