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
        Schema::create('lends', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id')->nullable();
            $table->string('borrower');
            $table->date('date');
            $table->decimal('amount', 15, 2);
            $table->text('note')->nullable();
            $table->enum('status', ['Due', 'Received'])->default('Due');
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
        Schema::dropIfExists('lends');
    }
};
