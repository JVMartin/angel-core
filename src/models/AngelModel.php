<?php namespace Angel\Core;

use Eloquent, App, Validator, Input, ReflectionClass, ToolBelt;

abstract class AngelModel extends Eloquent {

	// The column from which to seed the slug.  (i.e.: 'name')
	protected $slugSeed = null;
	// Whether or not the *entire* table is a single set of orders,
	// to be updated on add/delete/etc.
	public static $reorderable = false;
	// Whether to skip the events
	public $skipEvents = false;

	/**
	 * An array of columns to update from user input on each save.
	 *
	 * @return array
	 */
	public static function columns()
	{
		return array();
	}

	public function validate()
	{
		$validator = Validator::make(Input::all(), $this->validate_rules());
		$errors = ($validator->fails()) ? $validator->messages()->toArray() : array();
		foreach ($this->validate_custom() as $error) {
			$errors[] = $error;
		}
		return $errors;
	}
	public function validate_rules()
	{
		return array();
	}
	public function validate_custom()
	{
		return array();
	}

	///////////////////////////////////////////////
	//                  Events                   //
	///////////////////////////////////////////////
	public static function boot()
	{
		parent::boot();

		static::saving(function($model) {
			if ($model->skipEvents) return;
			$model->assign();
		});
		if (static::$reorderable) {
			static::deleted(function() {
				$order = 0;
				foreach (static::orderBy('order')->get() as $object) {
					$object->order = $order++;
					$object->save();
				}
			});
		}
	}

	/**
	 * Assign input to the columns() and log changes (if updating).
	 */
	public function assign()
	{
		$changes = array();
		foreach (static::columns() as $column) {
			if (!Input::exists($column)) continue;
			if ($this->id && ($this->$column != Input::get($column))) {
				$changes[$column] = array(
					'old' => $this->$column,
					'new' => Input::get($column)
				);
			}
			$this->$column = Input::get($column);
		}
		if ($this->id) with(App::make('Change'))->log($this, $changes);
		if ($this->slugSeed) $this->slug = ToolBelt::slug($this, $this->slugSeed);
	}

	public function changes()
	{
		return with(App::make('Change'))->where('fmodel', short_name($this))
			                            ->where('fid', $this->id)
			                            ->with('user')
			                            ->orderBy('created_at', 'DESC')
			                            ->get();
	}


}