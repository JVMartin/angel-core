<?php namespace Angel\Core;

use Session, Input, App, Redirect;

class AdminAngelController extends AngelController {

	public function __construct()
	{
		parent::__construct();

		// Grab the menu we're currently working with when creating content
		// from the menu link wizard
		if (Session::has('menu_id')) {
			$this->data['menu_id'] = Session::get('menu_id');
		} else if (Input::old('menu_id')) {
			$this->data['menu_id'] = Input::old('menu_id');
		}
	}

	/**
	 * Handle adding new menu items when creating content (such as pages) from within the menu system.
	 *
	 * @param string $fmodel - Name of the model.
	 * @param int $fid - ID of the model.
	 * @return Redirect to the menu index with success message.
	 */
	protected function also_add_menu_item($fmodel, $fid)
	{
		$MenuItem = App::make('MenuItem');

		$menuItem = new $MenuItem;
		$menuItem->skipEvents = true;
		$menuItem->menu_id	  = Input::get('menu_id');
		$menuItem->fmodel	  = $fmodel;
		$menuItem->fid 	      = $fid;
		$menuItem->order	  = $MenuItem::where('menu_id', Input::get('menu_id'))->count();
		$menuItem->save();

		return Redirect::to(admin_uri('menus'))->with('success', $fmodel . ' and menu link successfully created.');
	}

}