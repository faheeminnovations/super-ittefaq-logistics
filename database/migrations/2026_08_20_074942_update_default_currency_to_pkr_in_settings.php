<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing records to use PKR and Kilometers
        DB::table('settings')->update([
            'currency' => 'PKR',
            'distance_unit' => 'Kilometers'
        ]);
        
        // Update the default values for future records
        Schema::table('settings', function (Blueprint $table) {
            $table->string('currency')->default('PKR')->change();
            $table->string('distance_unit')->default('Kilometers')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to GBP and Miles
        DB::table('settings')->update([
            'currency' => 'GBP',
            'distance_unit' => 'Miles'
        ]);
        
        // Revert the default values
        Schema::table('settings', function (Blueprint $table) {
            $table->string('currency')->default('GBP')->change();
            $table->string('distance_unit')->default('Miles')->change();
        });
    }
};
