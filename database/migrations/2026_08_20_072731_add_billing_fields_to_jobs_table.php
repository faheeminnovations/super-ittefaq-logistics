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
        Schema::table('transport_jobs', function (Blueprint $table) {
            $table->integer('bags')->nullable()->after('quoted_price');
            $table->decimal('rent', 10, 2)->nullable()->after('bags');
            $table->decimal('advance', 10, 2)->nullable()->after('rent');
            $table->date('advance_date')->nullable()->after('advance');
            $table->decimal('dues', 10, 2)->nullable()->after('advance_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transport_jobs', function (Blueprint $table) {
            $table->dropColumn(['bags', 'rent', 'advance', 'advance_date', 'dues']);
        });
    }
};
