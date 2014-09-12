<?php

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

			$table->foreign('menu_id')->references('id')->on('menus')->onDelete('cascade');
			$table->foreign('child_menu_id')->references('id')->on('menus');
		});

		$MenuItem = App::make('MenuItem');
		$menuItem = new $MenuItem;
		$menuItem->skipEvents = true;
		$menuItem->menu_id    = 1;
		$menuItem->order      = 0;
		$menuItem->fmodel     = 'Page';
		$menuItem->fid        = 1;
		$menuItem->save();
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