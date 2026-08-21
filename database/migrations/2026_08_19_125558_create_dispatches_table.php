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
        Schema::create('dispatches', function (Blueprint $table) {
            $table->id();
            $table->string('job_number')->unique();
            $table->string('job_description');
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles');
            $table->foreignId('driver_id')->nullable()->constrained('drivers');
            $table->text('pickup_location')->nullable();
            $table->text('delivery_location')->nullable();
            $table->enum('status', ['unassigned', 'assigned', 'in_transit', 'delivered', 'cancelled'])->default('unassigned');
            $table->dateTime('dispatch_time')->nullable();
            $table->dateTime('completion_time')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('job_id')->nullable()->constrained('transport_jobs');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dispatches');
    }
};
