<?php namespace Angel\Core;

use Config;

class Language extends \Eloquent {

	///////////////////////////////////////////////
	//                  Other                    //
	///////////////////////////////////////////////
	/**
	 * Models with foreign keys (such as Pages and Menus) will be deleted by cascade.
	 * However, we still need to run any pre-deletion functions on these before that happens to
	 * delete any relations that don't cascade (such as MenuItems).
	 */
	public function pre_delete()
	{
		foreach (Config::get('core::language_models') as $fmodel) {
			if (!method_exists($fmodel, 'pre_delete')) continue;

			foreach ($fmodel::where('language_id', $this->id)->get() as $model) {
				$model->pre_delete();
			}
		}
	}

	/**
	 * We need to have a primary language for usage in error handlers (404), etc.
	 */
	public static function primary()
	{
		return static::where('uri', Config::get('core::language_primary'))->first();
	}
}