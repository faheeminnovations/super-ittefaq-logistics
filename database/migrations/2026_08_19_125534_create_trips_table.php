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
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->string('trip_number')->unique();
            $table->string('job_number')->nullable();
            $table->foreignId('vehicle_id')->constrained('vehicles');
            $table->foreignId('driver_id')->constrained('drivers');
            $table->dateTime('pickup_time');
            $table->dateTime('delivery_time')->nullable();
            $table->enum('status', ['pickup', 'in_transit', 'delivered', 'delayed', 'cancelled'])->default('pickup');
            $table->text('pickup_location')->nullable();
            $table->text('delivery_location')->nullable();
            $table->decimal('distance', 8, 2)->nullable();
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
        Schema::dropIfExists('trips');
    }
};
