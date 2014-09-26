<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePagesModulesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pages_modules', function(Blueprint $table) {
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->integer('page_id')->unsigned();
			$table->integer('number')->unsigned();
			$table->string('name');
			$table->text('html');
			$table->text('plaintext');
			$table->timestamps(); // Adds `created_at` and `updated_at` columns

			$table->foreign('page_id')->references('id')->on('pages')->onDelete('cascade');
		});

		if (ToolBelt::mysql_greater('5.6.4')) {
			DB::statement('ALTER TABLE `pages_modules` ADD FULLTEXT search(`name`, `plaintext`)');
		}
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('pages_modules');
	}

}