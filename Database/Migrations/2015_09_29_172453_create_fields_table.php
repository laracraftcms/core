<?php

use Laracraft\Core\Database\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fields', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('field_group_id')->index();
            $table->string('name');
            $table->string('handle')->unique();
            $table->string('help');
            $table->string('type');
            $table->text('settings');
            $table->timestamps();
            $table->tracked();
        });

		Schema::table('fields', function(Blueprint $table) {
			$table->foreign('field_group_id')
				->references('id')->on('field_groups')
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

        Schema::drop('fields');
    }
}
