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

	///////////////////////////////////////////////
	//                   Other                   //
	///////////////////////////////////////////////
	/**
	 * Get foreign model item (an instance of the object that the MenuItem links to)
	 */
	public function item()
	{
		$fmodel = $this->fmodel;
		return $fmodel::find($this->fid);
	}

}