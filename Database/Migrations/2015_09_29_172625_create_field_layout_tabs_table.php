<?php

use Laracraft\Core\Database\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFieldLayoutTabsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('field_layout_tabs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('field_layout_id')->index();
            $table->string('name');
            $table->integer('sequence')->index();
            $table->timestamps();
            $table->softDeletes();
            $table->tracked();
        });

		Schema::table('field_layout_tabs', function(Blueprint $table) {
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

        Schema::drop('field_layout_tabs');
    }
}
