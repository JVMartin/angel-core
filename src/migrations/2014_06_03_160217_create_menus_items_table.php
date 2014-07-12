<?php

use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenusItemsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('menus_items', function(Blueprint $table) {
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->integer('order')->unsigned();
			$table->integer('menu_id')->unsigned();
			$table->integer('child_menu_id')->unsigned()->nullable();
			$table->string('fmodel');
			$table->integer('fid')->unsigned();
			$table->timestamps(); // Adds `created_at` and `updated_at` columns
			$table->softDeletes(); // Adds `deleted_at` column

			$table->foreign('menu_id')->references('id')->on('menus')->onDelete('cascade');
			$table->foreign('child_menu_id')->references('id')->on('menus');
		});

		// Create the home page menu item
		DB::table('menus_items')->insert(
			array(
				'order'			=> 0,
				'menu_id'		=> 1,
				'fmodel'		=> 'Page',
				'fid'			=> 1,
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
		Schema::drop('menus_items');
	}

}