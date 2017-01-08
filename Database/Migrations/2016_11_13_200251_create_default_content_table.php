<?php

use Laracraft\Core\Database\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDefaultContentTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('default_content', function (Blueprint $table) {
			$table->morphsWithUUID('entity');
			$table->string('locale')->index();
			$table->string('title')->nullable();
			$table->index(["entity_id", "locale"]);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop("default_content");
	}

}
