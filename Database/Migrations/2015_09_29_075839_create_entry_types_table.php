<?php

use Laracraft\Core\Database\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEntryTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entry_types', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('field_layout_id')->nullable()->index();
            $table->string('name');
            $table->string('handle')->unique();
            $table->boolean('has_title_field')->default(true);
            $table->string('title_config')->nullable();
            $table->timestamps();
            $table->tracked();
        });

		Schema::table('entry_types', function(Blueprint $table) {
			$table->foreign('field_layout_id')
				->references('id')->on('field_layouts')
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

        Schema::drop('entry_types');
    }
}
