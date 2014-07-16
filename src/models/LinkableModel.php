<?php namespace Angel\Core;

use Eloquent, App, Config, ReflectionClass;

// NOTE: If languages are enabled, always eager-load the language relationship when grabbing linkable models.

abstract class LinkableModel extends Eloquent {

	///////////////////////////////////////////////
	//               Relationships               //
	///////////////////////////////////////////////
	public function language()
	{
		return $this->belongsTo('Language');
	}

	// Handling relationships in controller CRUD methods
	public function pre_delete()
	{
		$MenuItem = App::make('MenuItem');

		$MenuItem::where('fmodel', short_name($this))
				 ->where('fid', $this->id)
				 ->delete();
	}
	public function pre_restore()
	{
		$MenuItem = App::make('MenuItem');

		$MenuItem::withTrashed()
				 ->where('fmodel', short_name($this))
				 ->where('fid', $this->id)
				 ->restore();
	}
	public function pre_hard_delete()
	{
		$MenuItem = App::make('MenuItem');

		$MenuItem::withTrashed()
				 ->where('fmodel', short_name($this))
				 ->where('fid', $this->id)
				 ->forceDelete();
	}

	///////////////////////////////////////////////
	//               Menu Linkable               //
	///////////////////////////////////////////////
	abstract public function link();
	abstract public function link_edit();
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
}