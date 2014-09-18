<?php namespace Angel\Core;

use App, Input;

class MenuItem extends AngelModel {

	protected $table = 'menus_items';

	public static function columns()
	{
		return array(
			'child_menu_id'
		);
	}

	/**
	 * @param array &$errors - The array of failed validation errors.
	 * @return array - A key/value associative array of custom values.
	 */
	public function validate_custom()
	{
		$errors = array();

		if (Input::has('child_menu_id')) {
			if (Input::get('child_menu_id') == $this->menu_id) {
				$errors[] = 'The child menu cannot be the same as the parent menu.  A recursive loop would occur.';
			}
			if (static::where('child_menu_id', $this->menu_id)->count()) {
				$errors[] = 'A child menu cannot have a child menu nested within it.';
			}
		}

		return $errors;
	}

	///////////////////////////////////////////////
	//                  Events                   //
	///////////////////////////////////////////////
	public static function boot()
	{
		parent::boot();

		static::saving(function($menuItem) {
			if ($menuItem->skipEvents) return;

			if (Input::exists('child_menu_id') && !Input::get('child_menu_id')) {
				$menuItem->child_menu_id = null;
			}
		});
		static::creating(function($menuItem) {
			if ($menuItem->skipEvents) return;

			$menuItem->order   = static::where('menu_id', Input::get('menu_id'))->count();
			$menuItem->menu_id = Input::get('menu_id');
			$menuItem->fmodel  = Input::get('fmodel');
			$menuItem->fid     = Input::get('fid');
		});
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