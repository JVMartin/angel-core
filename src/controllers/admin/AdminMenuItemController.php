<?php namespace Angel\Core;

use App, Redirect;

class AdminMenuItemController extends AdminCrudController {

	protected $Model	= 'MenuItem';
	protected $uri		= 'menus/items';
	protected $plural	= 'items';
	protected $singular	= 'item';
	protected $package	= 'core';

	public function add_redirect($menuItem)
	{
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
}