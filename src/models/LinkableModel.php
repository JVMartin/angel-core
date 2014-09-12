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
	public function name()
	{
		return $this->name;
	}
	public static function drop_down($Model)
	{
		$Model = App::make($Model);

		$arr = array();
		foreach ($Model::get() as $object) {
			$arr[$object->id] = $object->name();
		}

		return $arr;
	}
	public static function drop_down_with($objects)
	{
		$arr = array();
		foreach ($objects as $object) {
			$arr[$object->id] = $object->name();
		}
		return $arr;
	}
}