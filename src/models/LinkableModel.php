<?php namespace Angel\Core;

use Eloquent, App, Config;

abstract class LinkableModel extends Eloquent {

	///////////////////////////////////////////////
	//               Relationships               //
	///////////////////////////////////////////////
	public function menuItem()
	{
		return $this->morphOne(App::make('MenuItem'), 'linkable');
	}

	// Handling relationships in controller CRUD methods
	public function pre_delete()
	{
		$menuItemModel = App::make('MenuItem');

		$menuItemModel::where('fmodel', get_class($this))
				      ->where('fid', $this->id)
				      ->delete();
	}
	public function pre_restore()
	{
		$menuItemModel = App::make('MenuItem');

		$menuItemModel::withTrashed()
				      ->where('fmodel', get_class($this))
				      ->where('fid', $this->id)
				      ->restore();
	}
	public function pre_hard_delete()
	{
		$menuItemModel = App::make('MenuItem');

		$menuItemModel::withTrashed()
				      ->where('fmodel', get_class($this))
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