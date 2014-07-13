<?php namespace Angel\Core;

use Eloquent, App;

class MenuItem extends Eloquent {

	protected $table = 'menus_items';

	protected $softDelete = true;

	// Columns to update/insert on edit/add
	public static function columns()
	{
		return array(
			'child_menu_id'
		);
	}

	///////////////////////////////////////////////
	//               Relationships               //
	///////////////////////////////////////////////
	public function menu()
	{
		return $this->belongsTo(App::make('Menu'));
	}
	public function childMenu()
	{
		return $this->belongsTo(App::make('Menu'), 'child_menu_id');
	}

}