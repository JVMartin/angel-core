<?php namespace Angel\Core;

use App, Config, ReflectionClass;

// NOTE: If languages are enabled, always eager-load the language relationship when grabbing linkable models.

abstract class LinkableModel extends AngelModel {

	///////////////////////////////////////////////
	//                  Events                   //
	///////////////////////////////////////////////
	public static function boot()
	{
		parent::boot();

		static::deleting(function($model) {
			with(App::make('MenuItem'))->where('fmodel', short_name($model))
				                       ->where('fid', $model->id)
				                       ->delete();
		});
	}

	///////////////////////////////////////////////
	//               Relationships               //
	///////////////////////////////////////////////
	public function language()
	{
		return $this->belongsTo('Language');
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
	public function name_full()
	{
		if (Config::get('core::languages')) return $this->language->name . ' | ' . $this->name;
		return $this->name;
	}
	public static function drop_down($model)
	{
		$model = App::make($model);

		if (Config::get('core::languages')) $objects = $model::orderBy('language_id')->get();
		else $objects = $model::get();
		$arr = array();
		foreach ($objects as $object) {
			$arr[$object->id] = $object->name_full();
		}
		return $arr;
	}
	public static function drop_down_with($objects)
	{
		$arr = array();
		foreach ($objects as $object) {
			$arr[$object->id] = $object->name_full();
		}
		return $arr;
	}
}