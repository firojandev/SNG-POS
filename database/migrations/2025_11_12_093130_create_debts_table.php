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
        Schema::create('debts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id')->nullable();
            $table->string('lender');
            $table->date('date');
            $table->decimal('amount', 15, 2);
            $table->text('note')->nullable();
            $table->enum('status', ['Paid', 'Unpaid'])->default('Unpaid');
            $table->timestamps();
            $table->softDeletes();

            $table->index('store_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('debts');
    }
};
