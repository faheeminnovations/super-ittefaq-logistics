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
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('licence_no')->unique();
            $table->string('category');
            $table->date('cpc_expiry');
            $table->string('phone');
            $table->enum('status', ['on_trip', 'on_duty', 'on_leave', 'suspended', 'licence_expired'])->default('on_duty');
            $table->text('address')->nullable();
            $table->date('licence_expiry')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};
