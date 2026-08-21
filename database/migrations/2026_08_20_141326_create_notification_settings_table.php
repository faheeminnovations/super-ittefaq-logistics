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
        Schema::create('notification_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('notification_type'); // save, delete, update, etc.
            $table->string('module'); // billing, customer, vehicle, etc.
            $table->boolean('email_enabled')->default(false);
            $table->boolean('app_enabled')->default(true);
            $table->timestamps();
            
            $table->unique(['user_id', 'notification_type', 'module']);
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_settings');
    }
};
