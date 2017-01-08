<?php

use Laracraft\Core\Database\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('section_id')->index();
            $table->uuid('entry_type_id')->index();
            $table->string('title');
			$table->string('slug');
            $table->enableable(true);
            $table->publishable();
            $table->timestamps();
            $table->tracked();
        });

		Schema::table('entries', function(Blueprint $table) {
			$table->foreign('section_id')
				->references('id')->on('sections')
				->onDelete('cascade');

			$table->foreign('entry_type_id')
				->references('id')->on('entry_types')
				->onDelete('cascade');

		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::drop('entries');
    }
}
