<?php

use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLanguagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (Config::get('core::languages')) {
			Schema::create('languages', function(Blueprint $table) {
				$table->engine = 'InnoDB';

				$table->increments('id');
				$table->string('name');
				$table->string('uri');
				$table->timestamps(); // Adds `created_at` and `updated_at` columns
			});

			DB::table('languages')->insert(
				array(
					'uri'			=> 'en',
					'name'			=> 'English',
					'created_at'	=> Carbon::now(),
					'updated_at' 	=> Carbon::now()
				)
			);
		}
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		if (Config::get('core::languages')) {
			Schema::drop('languages');
		}
	}

}