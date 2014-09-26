<?php namespace Angel\Core;

use App, Validator, Input;

abstract class AngelModel extends \Eloquent {

	/**
	 * The column from which to seed the slug.  (i.e.: 'name')
	 *
	 * @var string
	 */
	protected $slugSeed = null;

	/**
	 * Whether or not the *entire* table is to have the `order` column
	 * updated automatically on add/delete/etc.  (Don't use this if
	 * you cluster orders into subsets or categories, etc.)
	 *
	 * @var bool
	 */
	protected $reorderable = false;

	/**
	 * Whether to skip the model events, namely the assign() method
	 * that is called from the static::saving() in the boot() method
	 * of AngelModel to assign all user input to the columns.
	 *
	 * @var bool
	 */
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
		$errors = array();

		if (count($this->validate_rules())) {
			$validator = Validator::make(Input::all(), $this->validate_rules());
			if ($validator->fails()) {
				$errors = array_merge($errors, $validator->messages()->toArray());
			}
		}

		$errors = array_merge($errors, $this->validate_custom());

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

		// Assign input to the columns()
		static::saving(function($model) {
			if ($model->skipEvents) return;
			$model->assign();
		});

		static::creating(function($model) {
			if ($model->reorderable) {
				$model->order = $model->count();
			}
		});

		// Fill in the `order` gap after deleting a model.
		static::deleted(function($model) {
			if (!$model->reorderable) return;
			$order = 0;
			foreach ($model->orderBy('order')->get() as $object) {
				$object->order = $order++;
				$object->save();
			}
		});
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
		if ($this->slugSeed) $this->slug = slug($this, $this->slugSeed);
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