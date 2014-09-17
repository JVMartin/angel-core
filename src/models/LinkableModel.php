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
	abstract public function link();
	abstract public function link_edit();
	abstract public function search($terms);
}