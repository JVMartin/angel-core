<?php namespace Angel\Core;

use Eloquent, App;

class MenuItem extends Eloquent {

	protected $table = 'menus_items';

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