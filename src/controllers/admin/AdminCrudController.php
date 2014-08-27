<?php namespace Angel\Core;

use App, Auth, View, Input, Redirect, Validator, Config;

abstract class AdminCrudController extends AdminAngelController {

	/*
	// Required:
	protected $Model	= 'ProductCategory';
	protected $uri		= 'products/categories';
	protected $plural	= 'categories';
	protected $singular	= 'category';
	protected $package	= 'products';

	// Optional:
	protected $log_changes = true;
	protected $slug        = 'name'; // Populate the 'slug' column with a sluggified version of the given column.  ('name', 'title', etc.)
	protected $reorderable = true;   // Only to be used when all objects are ordered together (not in clusters/categories of any kind)
	*/

	// Columns to update on edit/add
	protected static function columns()
	{
		return array();
	}

	public function index()
	{
		$Model   = App::make($this->Model);
		$objects = $Model::withTrashed();

		// If languages are enabled, only get the current active language's objects.
		if (Config::get('core::languages') && in_array(Config::get('language_models'), $this->Model)) {
			$objects = $objects->where('language_id', $this->data['active_language']->id);
		}

		// If a search term has been entered...
		$this->data['search'] = $search = (Input::get('search')) ? urldecode(Input::get('search')) : null;
		if ($search) {
			$terms = explode(' ', $search);
			foreach ($terms as &$term) {
				$term = '%' . $term . '%';
			}

			// Call the search method on the Model
			$resultIDs = array();
			$Model->search($terms)->each(function($object) use (&$resultIDs) {
				$resultIDs[] = $object->id;
			});
			// Limit the $objects query based on the results, make sure that no objects
			// are returned if there are no results. (where id = 0, it's cheap but it works!)
			$objects = (count($resultIDs)) ? $objects->whereIn('id', $resultIDs) : $objects->where('id', 0);
		}

		// Return all objects in order if this is a reorderable index
		if (isset($this->reorderable) && $this->reorderable) {
			$this->data[$this->plural] = $objects->orderBy('order')->get();
		} else {
			// Otherwise, paginate the objects
			$paginator = $objects->paginate();
			$this->data[$this->plural] = $paginator->getCollection();
			$appends = $_GET;
			unset($appends['page']);
			$this->data['links'] = $paginator->appends($appends)->links();
		}

		return View::make($this->view('index'), $this->data);
	}

	public function add()
	{
		// Toss the menu_id URL variable into the session instead
		// NOTE:  You only need this for menu-linkable models
		if (Input::get('menu_id')) {
			return Redirect::to($this->uri('add'))->with('menu_id', Input::get('menu_id'));
		}

		$this->data['action'] = 'add';
		return View::make($this->view('add-or-edit'), $this->data);
	}

	public function attempt_add()
	{
		$Model = App::make($this->Model);

		$errors = $this->validate($custom);
		if (count($errors)) {
			return Redirect::to($this->uri('add'))->withInput()->withErrors($errors);
		}

		$object = new $Model;

		foreach(static::columns() as $column) {
			// Prefer the $custom array before input
			$object->{$column} = isset($custom[$column]) ? $custom[$column] : Input::get($column);
		}
		if (isset($this->slug) && $this->slug) {
			$object->slug = $this->slug($Model, 'slug', $object->{$this->slug});
		}
		if (isset($this->reorderable) && $this->reorderable) {
			$object->order = $Model::count();
		}

		if (method_exists($this, 'before_save')) $this->before_save($object);
		$object->save();
		if (method_exists($this, 'after_save')) $this->after_save($object);

		// Are we creating this object from the menu wizard?
		// NOTE:  You only need this for menu-linkable models
		if (Input::get('menu_id')) {
			return $this->also_add_menu_item($this->Model, $object->id);
		}

		return $this->add_redirect($object);
	}
	public function add_redirect($object)
	{
		return Redirect::to($this->uri())->with('success', '
			<p>' . $this->Model . ' successfully created.</p>
		');
	}

	public function edit($id)
	{
		$Model = App::make($this->Model);

		$this->data[$this->singular] = $Model::withTrashed()->findOrFail($id);
		$this->data['action']        = 'edit';

		return View::make($this->view('add-or-edit'), $this->data);
	}

	public function attempt_edit($id)
	{
		$Model  = App::make($this->Model);
		$Change = App::make('Change');

		$errors = $this->validate($custom, $id);
		if (count($errors)) {
			return Redirect::to($this->uri('edit/' . $id))->withInput()->withErrors($errors);
		}

		$object  = $Model::withTrashed()->findOrFail($id);
		$changes = array();

		foreach (static::columns() as $column) {
			// Prefer the $custom array before input
			$new_value = array_key_exists($column, $custom) ? $custom[$column] : Input::get($column);

			if (isset($this->log_changes) && $this->log_changes && $object->{$column} != $new_value) {
				$changes[$column] = array(
					'old' => $object->{$column},
					'new' => $new_value
				);
			}

			$object->{$column} = $new_value;
		}
		if (isset($this->slug) && $this->slug) {
			$object->slug = $this->slug($Model, 'slug', $object->{$this->slug}, $id);
		}

		if (method_exists($this, 'before_save')) $this->before_save($object, $changes);
		$object->save();
		if (method_exists($this, 'after_save')) $this->after_save($object, $changes);

		if (count($changes)) {
			$change = new $Change;
			$change->user_id = Auth::user()->id;
			$change->fmodel  = $this->Model;
			$change->fid     = $object->id;
			$change->changes = json_encode($changes);
			$change->save();
		}

		return $this->edit_redirect($object);
	}
	public function edit_redirect($object)
	{
		return Redirect::to($this->uri('edit/' . $object->id))->with('success', '
			<p>' . $this->Model . ' successfully updated.</p>
			<p><a href="' . $this->uri('', true) . '">Return to index</a></p>
		');
	}

	/**
	 * Validate all input when adding or editing.
	 *
	 * @param array &$custom - This array is initialized by this function.  Its purpose is to
	 * 							exclude certain columns that require intervention of some kind (such as
	 * 							checkboxes because they aren't included in input on submission)
	 * @param int $id - (Optional) ID of member beind edited
	 * @return array - An array of error messages to show why validation failed
	 */
	public function validate(&$custom, $id = null)
	{
		$validator = Validator::make(Input::all(), $this->validate_rules($id));
		$errors = ($validator->fails()) ? $validator->messages()->toArray() : array();
		$custom = $this->validate_custom($id, $errors);
		return $errors;
	}

	/**
	 * @param int $id - The ID of the model when editing, null when adding.
	 * @return array - Rules for the validator.
	 */
	public function validate_rules($id = null)
	{
		return array();
	}

	/**
	 * @param array &$errors - The array of failed validation errors.
	 * @return array - A key/value associative array of custom values.
	 */
	public function validate_custom($id = null, &$errors)
	{
		return array();
	}

	/**
	 * This method is called before the model is saved in add() and edit()
	 *
	 * @param $object - The instance of the model about to be saved.
	 * @param array $changes - The array of changes.
	 */
	public function before_save(&$object, &$changes = array())
	{
		//
	}

	/**
	 * This method is called after the model is saved in add() and edit()
	 *
	 * @param $object - The instance of the model that was just saved.
	 * @param array $changes - The array of changes.
	 */
	public function after_save($object, &$changes = array())
	{
		//
	}

	/**
	 * AJAX for reordering objects
	 */
	public function order()
	{
		$Model   = App::make($this->Model);
		$orders  = Input::get('orders');
		$objects = $Model::whereIn('id', array_keys($orders))->get();

		foreach ($objects as $object) {
			$object->order = $orders[$object->id];
			$object->save();
		}

		return 1;
	}

	/**
	 * Called after delete/restore/etc. to ensure that the 'gap' in orders is filled in.
	 */
	public function reorder()
	{
		if (!isset($this->reorderable) || !$this->reorderable) return;
		$Model = App::make($this->Model);

		$objects = $Model::orderBy('order')->get();

		$order = 0;
		foreach ($objects as $object) {
			$object->order = $order++;
			$object->save();
		}
	}

	public function delete($id, $ajax = false)
	{
		$Model = App::make($this->Model);

		$object = $Model::find($id);
		if (method_exists($object, 'pre_delete')) {
			$object->pre_delete();
		}
		$object->delete();

		$this->reorder();

		if ($ajax) return 1;

		return $this->delete_redirect();
	}
	public function delete_redirect()
	{
		return Redirect::to($this->uri())->with('success', '
			<p>' . $this->Model . ' successfully deleted forever.</p>
		');
	}

	public function view($name)
	{
		$view = '';
		if ($this->package) $view .= $this->package . '::';
		return $view . 'admin/' . $this->uri . '/' . $name;
	}

	public function uri($append = '', $url = false)
	{
		$uri = $this->uri;
		if ($append) $uri .= '/' . $append;
		if ($url) return admin_url($uri);
		return admin_uri($uri);
	}

	//------------------------------
	// Change Log Related Functions
	//------------------------------
	protected function log_relation_name($object)
	{
		$name = short_name($object) . ' ID#' . $object->id;
		if (isset($object->name) && $object->name) $name .= ' Name: ' . $object->name;
		return $name;
	}

	protected function log_relation_change($object, $old_array, $columns, &$changes)
	{
		$name = $this->log_relation_name($object);
		if (!count($old_array)) {
			$changes['Created new ' . $name] = array();
			return;
		}
		foreach ($columns as $column) {
			if ($object->$column == $old_array[$column]) continue;
			$changes['Changed ' . $name . ' Column: ' . $column] = array(
				'old' => $old_array[$column],
				'new' => $object->$column
			);
		}
	}

	protected function log_relation_deletion($object, &$changes)
	{
		$name = $this->log_relation_name($object);
		$changes['Deleted ' . $name] = array();
	}

}