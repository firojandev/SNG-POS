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
        Schema::table('invoices', function (Blueprint $table) {
            // Rename subtotal to unit_total
            $table->renameColumn('subtotal', 'unit_total');

            // Add payable_amount after total_amount
            // payable_amount = unit_total + total_vat - discount
            $table->decimal('payable_amount', 10, 2)->default(0)->after('total_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            // Remove payable_amount
            $table->dropColumn('payable_amount');

            // Rename back to subtotal
            $table->renameColumn('unit_total', 'subtotal');
        });
    }
};
