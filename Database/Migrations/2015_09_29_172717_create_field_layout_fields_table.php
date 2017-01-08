<?php

use Laracraft\Core\Database\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFieldLayoutFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('field_layout_fields', function (Blueprint $table) {
            $table->uuid('field_layout_id')->index();
            $table->uuid('field_id')->index();
            $table->uuid('field_layout_tab_id')->index();
            $table->string('name_override')->nullable();
            $table->string('help_override')->nullable();
            $table->boolean('required');
            $table->integer('sequence')->index();
            $table->timestamps();
            $table->tracked();

			$table->primary(['field_layout_id','field_id']);

        });

		Schema::table('field_layout_fields', function(Blueprint $table) {
			$table->foreign('field_layout_id')
				->references('id')->on('field_layouts')
				->onDelete('cascade');

			$table->foreign('field_id')
				->references('id')->on('fields')
				->onDelete('cascade');

			$table->foreign('field_layout_tab_id')
				->references('id')->on('field_layout_tabs')
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

        Schema::drop('field_layout_fields');
    }
}
