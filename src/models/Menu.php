<?php namespace Angel\Core;

use Eloquent, App, Config, View;

class Menu extends Eloquent {

	protected $softDelete = true;

	// Columns to update/insert on edit/add
	public static function columns()
	{
		$columns = array(
			'name'
		);
		if (Config::get('core::languages')) $columns[] = 'language_id';
		return $columns;
	}

	///////////////////////////////////////////////
	//               Relationships               //
	///////////////////////////////////////////////
	public function menuItems()
	{
		return $this->morphMany(App::make('MenuItem'), 'linkable')->with('childMenu')->orderBy('order', 'asc');
	}
	public function language()
	{
		return $this->belongsTo(App::make('Language'));
	}


	///////////////////////////////////////////////
	//               View-Related                //
	///////////////////////////////////////////////
	public static function display($id)
	{
		$menuModel = App::make('Menu');

		$menu = $menuModel::with('menuItems')->findOrFail($id);

		$models = $menuModel::get_models($menu->menuItems);

		return View::make('core::menus.render', array('models'=>$models));
	}
}

?>