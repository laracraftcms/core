<?php

use Laracraft\Core\Database\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSectionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sections', function (Blueprint $table) {
			$table->uuid('id')->primary();
            $table->string('name')->index();
            $table->string('handle')->unique();
            $table->string('type')->index();
            $table->boolean('has_urls');
            $table->string('view');
            $table->boolean('has_versions');
            $table->boolean('default_enabled');
            $table->string('url_format');
            $table->string('nested_url_format')->nullable();
            $table->integer('max_levels')->nullable();
            $table->enableable(true);
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
        Schema::drop('sections');
    }
}
