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
        Schema::create('pods', function (Blueprint $table) {
            $table->id();
            $table->string('job_number');
            $table->foreignId('customer_id')->constrained('customers');
            $table->foreignId('driver_id')->constrained('drivers');
            $table->dateTime('delivery_datetime');
            $table->boolean('has_signature')->default(false);
            $table->boolean('has_photo')->default(false);
            $table->enum('status', ['complete', 'missing_signature', 'missing_photo', 'pending'])->default('pending');
            $table->string('signature_path')->nullable();
            $table->string('photo_path')->nullable();
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
        Schema::dropIfExists('pods');
    }
};
