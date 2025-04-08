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
		Schema::create('dishes', function (Blueprint $table) {
			$table->id();

			$table->string('name'); // ex: "carbonara"
			$table->text('menu_snippet')->nullable(); // portion of the menu where it was found
			$table->string('restaurant_url'); // base URL like "trattoriapippo.it"
			$table->string('source_link'); // specific page where it was found
			$table->timestamp('scraped_at'); // when it was scraped

			$table->timestamps(); // Laravel created_at / updated_at
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('dishes');
	}
};
