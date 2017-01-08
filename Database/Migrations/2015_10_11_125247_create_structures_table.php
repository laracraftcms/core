<?php

use Laracraft\Core\Database\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStructuresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('structures', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->morphsWithUUID('structured');
            $table->morphsWithUUID('entry');
            $table->baumNode();
            $table->timestamps();
            $table->tracked();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('structures');
    }
}
