<?php

use Laracraft\Core\Database\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEditLocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('edit_locks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->morphsWithUUID('lockable');
            $table->timestamp('expires_at')->index();
            $table->trackCreate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('edit_locks');
    }
}
