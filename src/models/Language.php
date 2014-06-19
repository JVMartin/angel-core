<?php namespace Angel\Core;

use Eloquent;

class Language extends Eloquent {

	// Columns to update/insert on edit/add
	public static function columns()
	{
		return array(
			'name',
			'uri'
		);
	}

	///////////////////////////////////////////////
	//                  Other                    //
	///////////////////////////////////////////////
	/**
	 * Models with foreign keys (such as Pages and Menus) will be deleted by cascade.
	 * However, we still need to run any pre-deletion functions on these before that happens to
	 * delete any relations that don't cascade (such as MenuItems).
	 */
	public function pre_hard_delete() {
		foreach (Config::get('core::language_models') as $fmodel) {
			if (!method_exists($fmodel, 'pre_hard_delete')) continue;

			foreach ($fmodel::where('language_id', $this->id)->get() as $model) {
				$model->pre_hard_delete();
			}
		}
	}

	/**
	 * We need to have a primary language for usage in error handlers (404), etc.
	 */
	public static function primary() {
		return static::where('uri', Config::get('core::language_primary'))->first();
	}
}