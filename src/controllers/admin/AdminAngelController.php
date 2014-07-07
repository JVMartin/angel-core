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
	 * Handle the creation of the slug and verifying that it is unique.
	 * Slugs are used for URLs generally, like: http://yoursite.com/products/large-green-ball
	 *
	 * @param string $model - The model name.
	 * @param string $column - The column name where the slug is stored.
	 * @param string $value - The value to sluggify (usually the 'name' field from input).
	 * @param null $id - The ID of the current object if editing.
	 * @return string $unique_slug - The unique slug.
	 */
	public function slug($model, $column, $value, $id = null)
	{
		$slug        = $this->sluggify($value);
		$unique_slug = $slug;
		$i           = 1;

		do {
			$counter = $model::where($column, $slug);
			if ($id) $counter = $counter->where($id, '<>', $id);
			$counter = $counter->count();
			if ($counter) {
				$unique_slug = $slug . '-' . $i++;
			}
		} while ($counter);

		return $unique_slug;
	}

	/**
	 * Turn a string into a slug.
	 * i.e.: 'Large Green Ball' -> 'large-green-ball'
	 *
	 * @param string $name - The string to sluggify.
	 * @return string $slug - The sluggified string.
	 */
	private function sluggify($name)
	{
		$slug = strtolower($name);
		$slug = strip_tags($slug);
		$slug = stripslashes($slug);

		$slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
		$slug = trim($slug, '-');

		return $slug;
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
		$menuItemModel = App::make('MenuItem');

		$order				= $menuItemModel::where('menu_id', Input::get('menu_id'))->count();
		$menu_item			= new $menuItemModel;
		$menu_item->menu_id	= Input::get('menu_id');
		$menu_item->fmodel	= $fmodel;
		$menu_item->fid 	= $fid;
		$menu_item->order	= $order;
		$menu_item->save();
		return Redirect::to(admin_uri('menus'))->with('success', $fmodel . ' and menu link successfully created.');
	}

}