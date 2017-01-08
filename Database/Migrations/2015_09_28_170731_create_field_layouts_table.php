<?php

use Laracraft\Core\Database\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFieldLayoutsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('field_layouts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
			$table->string('handle')->unique();
            $table->timestamps();
            $table->tracked();
        });
    }

    /**composer require alsofronie/eloquent-uuid:dev-master
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('field_layouts');
    }
}
