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
		$tables = [
			'categories',
			'currencies',
			'customers',
			'suppliers',
			'taxes',
			'units',
			'expense_categories',
			'expenses',
		];

		foreach ($tables as $tableName) {
			Schema::table($tableName, function (Blueprint $table) use ($tableName) {
				// Only add if it doesn't already exist
				if (!Schema::hasColumn($tableName, 'store_id')) {
					// Follow existing convention used in users migration: string and nullable
					$table->string('store_id')->nullable()->after('id');
				}
			});
		}
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		$tables = [
			'categories',
			'currencies',
			'customers',
			'options',
			'products',
			'suppliers',
			'taxes',
			'units',
		];

		foreach ($tables as $tableName) {
			Schema::table($tableName, function (Blueprint $table) use ($tableName) {
				if (Schema::hasColumn($tableName, 'store_id')) {
					$table->dropColumn('store_id');
				}
			});
		}
	}
};


