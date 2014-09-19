<?php namespace Angel\Core;

use App;

abstract class LinkableModel extends AngelModel {

	///////////////////////////////////////////////
	//                  Events                   //
	///////////////////////////////////////////////
	public static function boot()
	{
		parent::boot();

		// Delete all links when deleting any LinkableModel.
		static::deleting(function($model) {
			with(App::make('MenuItem'))->where('fmodel', short_name($model))
				                       ->where('fid', $model->id)
				                       ->delete();
		});
	}

	///////////////////////////////////////////////
	//               Menu Linkable               //
	///////////////////////////////////////////////
	/**
	 * URL of where to view this model in the front-end.
	 *
	 * @return string - The URL.
	 */
	abstract public function link();

	/**
	 * URL of where to edit this model in the admin panel.
	 *
	 * @return string - The URL.
	 */
	abstract public function link_edit();

	/**
	 * Fetch all models that match any of a set of search terms.
	 *
	 * @param array $terms - An array of search terms, already formatted
	 *                       like this:  array('%apple%', '%orange%')
	 * @return \Illuminate\Database\Eloquent\Collection - A collection of models; the search results.
	 */
	abstract public function search($terms);
}