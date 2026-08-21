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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('report_name');
            $table->string('report_type');
            $table->date('report_date');
            $table->decimal('revenue', 12, 2)->nullable();
            $table->decimal('expenses', 12, 2)->nullable();
            $table->decimal('profit', 12, 2)->nullable();
            $table->integer('total_mileage')->nullable();
            $table->integer('total_jobs')->nullable();
            $table->integer('completed_jobs')->nullable();
            $table->text('generated_data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
