<?php

use Laracraft\Core\Database\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSectionEntryTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('section_entry_types', function (Blueprint $table) {
            $table->uuid('section_id')->index();
            $table->uuid('entry_type_id')->index();
            $table->integer('sequence')->index()->default(0);
            $table->timestamps();
            $table->tracked();
        });

		Schema::table('section_entry_types', function(Blueprint $table) {
			$table->foreign('section_id')
				->references('id')->on('sections')
				->onDelete('cascade');

			$table->foreign('entry_type_id')
				->references('id')->on('entry_types')
				->onDelete('cascade');

			$table->primary(['section_id','entry_type_id']);
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::drop('section_entry_types');
    }
}
