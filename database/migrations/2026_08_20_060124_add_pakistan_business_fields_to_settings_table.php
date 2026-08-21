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
        Schema::table('settings', function (Blueprint $table) {
            $table->string('ntn_number')->nullable()->after('vat_number');
            $table->string('strn_number')->nullable()->after('ntn_number');
            $table->string('website')->nullable()->after('email');
            $table->decimal('gst_rate', 5, 2)->default(17.00)->after('document_reminder_window');
            $table->string('invoice_prefix')->default('INV-')->after('gst_rate');
            $table->string('quotation_prefix')->default('QUO-')->after('invoice_prefix');
            $table->string('bank_name')->nullable()->after('quotation_prefix');
            $table->string('bank_account_number')->nullable()->after('bank_name');
            $table->string('bank_iban')->nullable()->after('bank_account_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'ntn_number',
                'strn_number',
                'website',
                'gst_rate',
                'invoice_prefix',
                'quotation_prefix',
                'bank_name',
                'bank_account_number',
                'bank_iban',
            ]);
        });
    }
};
