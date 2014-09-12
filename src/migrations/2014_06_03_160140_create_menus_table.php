<?php

use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenusTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('menus', function(Blueprint $table) {
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->string('name');
			$table->timestamps(); // Adds `created_at` and `updated_at` columns
		});

		// Create the main menu
		DB::table('menus')->insert(
			array(
				'name' 			=> 'Main',
				'created_at'	=> Carbon::now(),
				'updated_at' 	=> Carbon::now()
			)
		);
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('menus');
	}

}