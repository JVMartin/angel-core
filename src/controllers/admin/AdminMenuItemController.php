<?php namespace Angel\Core;

use App, Input, Redirect;

class AdminMenuItemController extends AdminCrudController {

	protected $Model	= 'MenuItem';
	protected $uri		= 'menus/items';
	protected $plural	= 'items';
	protected $singular	= 'item';
	protected $package	= 'core';

	// Columns to update/insert on edit/add
	protected static function columns()
	{
		return array(
			'child_menu_id'
		);
	}

	public function attempt_add()
	{
		$MenuItem = App::make('MenuItem');

		$order = $MenuItem::where('menu_id', Input::get('menu_id'))->count();

		$menu_item = new $MenuItem;
		$menu_item->order	= $order;
		$menu_item->menu_id = Input::get('menu_id');
		$menu_item->fmodel	= Input::get('fmodel');
		$menu_item->fid		= Input::get('fid');
		$menu_item->save();

		return Redirect::to(admin_uri('menus'))->with('success', '
			<p>Link created.</p>
		');
	}

	public function edit($id)
	{
		$Menu  = App::make('Menu');
		$menus = $Menu::all();

		$menu_list = array(''=>'None');
		foreach ($menus as $menu) {
			$menu_list[$menu->id] = $menu->name;
		}
		$this->data['menu_list'] = $menu_list;

		return parent::edit($id);
	}

	/**
	 * @param array &$errors - The array of failed validation errors.
	 * @return array - A key/value associative array of custom values.
	 */
	public function validate_custom($id = null, &$errors)
	{
		$MenuItem  = App::make('MenuItem');
		$menu_item = $MenuItem::findOrFail($id);

		if (Input::get('child_menu_id') == $menu_item->menu_id) {
			$errors[] = 'The child menu cannot be the same as the parent menu.  A recursive loop would occur.';
		}
		if ($MenuItem::where('child_menu_id', $menu_item->menu_id)->count()) {
			$errors[] = 'A child menu cannot have a child menu nested within it.';
		}
		if (!Input::get('child_menu_id')) {
			return array(
				'child_menu_id' => null
			);
		}

		return array();
	}
}