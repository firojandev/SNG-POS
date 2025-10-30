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
        // Add discount fields to invoice_items table
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->enum('item_discount_type', ['percentage', 'flat'])->nullable()->after('unit_total');
            $table->decimal('item_discount_value', 10, 2)->default(0)->after('item_discount_type');
            $table->decimal('item_discount_amount', 10, 2)->default(0)->after('item_discount_value');
        });

        // Add discount_type to invoices table
        Schema::table('invoices', function (Blueprint $table) {
            $table->enum('discount_type', ['percentage', 'flat'])->default('flat')->after('total_amount');
            $table->decimal('discount_value', 10, 2)->default(0)->after('discount_type');
            // Rename 'discount' to 'discount_amount' for clarity
            $table->renameColumn('discount', 'discount_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropColumn(['item_discount_type', 'item_discount_value', 'item_discount_amount']);
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->renameColumn('discount_amount', 'discount');
            $table->dropColumn(['discount_type', 'discount_value']);
        });
    }
};
