<?php namespace Angel\Core;

use App, View, Input, Redirect, Validator;

abstract class AdminCrudController extends AdminAngelController {

	/*
	protected $model	= 'ProductCategory';
	protected $uri		= 'products/categories';
	protected $plural	= 'categories';
	protected $singular	= 'category';
	protected $package	= 'products';
	*/

	public function view($name)
	{
		return $this->package . '::admin/' . $this->uri . '/' . $name;
	}

	public function uri($append = '', $url = false)
	{
		$uri = $this->uri;
		if ($append) $uri .= '/' . $append;
		if ($url) return admin_url($uri);
		return admin_uri($uri);
	}

	public function index()
	{
		$model = App::make($this->model);

		$paginator = $model::withTrashed()->paginate();
		$this->data[$this->plural] = $paginator->getCollection();
		$appends = $_GET;
		unset($appends['page']);
		$this->data['links'] = $paginator->appends($appends)->links();

		return View::make($this->view('index'), $this->data);
	}

	public function index_searchable($searchable = array())
	{
		$model = App::make($this->model);

		$search = Input::get('search') ? urldecode(Input::get('search')) : null;
		$paginator = $model::withTrashed();

		if ($search) {
			$terms = explode(' ', $search);
			$paginator = $paginator->where(function($query) use ($terms, $searchable) {
				foreach ($terms as $term) {
					$term = '%'.$term.'%';
					foreach ($searchable as $column) {
						$query->orWhere($column, 'like', $term);
					}
				}
			});
		}
		$paginator = $paginator->paginate();

		$this->data[$this->plural] = $paginator->getCollection();
		$appends = $_GET;
		unset($appends['page']);
		$this->data['links'] = $paginator->appends($appends)->links();
		$this->data['search'] = $search;
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
		$model = App::make($this->model);

		$errors = $this->validate($custom);
		if (count($errors)) {
			return Redirect::to($this->uri('add'))->withInput()->withErrors($errors);
		}

		$object = new $model;
		foreach($model::columns() as $column) {
			$object->{$column} = isset($custom[$column]) ? $custom[$column] : Input::get($column);
		}
		if ($object->reorderable) {
			$object->order = $model::count();
		}
		$object->save();

		if (method_exists($this, 'after_save')) $this->after_save($object);

		// Are we creating this object from the menu wizard?
		// NOTE:  You only need this for menu-linkable models
		if (Input::get('menu_id')) {
			return $this->also_add_menu_item($this->model, $object->id);
		}

		return Redirect::to($this->uri())->with('success', '
			<p>' . $this->model . ' successfully created.</p>
		');
	}

	public function edit($id)
	{
		$model = App::make($this->model);

		$object = $model::withTrashed()->findOrFail($id);
		$this->data[$this->singular] = $object;
		$this->data['action'] = 'edit';

		return View::make($this->view('add-or-edit'), $this->data);
	}

	public function attempt_edit($id)
	{
		$model = App::make($this->model);

		$errors = $this->validate($custom, $id);
		if (count($errors)) {
			return Redirect::to($this->uri('edit/' . $id))->withInput()->withErrors($errors);
		}

		$object = $model::withTrashed()->findOrFail($id);
		foreach ($model::columns() as $column) {
			$object->{$column} = isset($custom[$column]) ? $custom[$column] : Input::get($column);
		}
		$object->save();

		if (method_exists($this, 'after_save')) $this->after_save($object);

		return Redirect::to($this->uri('edit/' . $id))->with('success', '
			<p>' . $this->model . ' successfully updated.</p>
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
		$errors = array();

		$validator = Validator::make(Input::all(), $this->validate_rules($id));
		if ($validator->fails()) {
			foreach($validator->messages()->all() as $error) {
				$errors[] = $error;
			}
		}

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

	public function reorder()
	{
		$model = App::make($this->model);
		$object = new $model;
		if (!$object->reorderable) return;

		$objects = $model::orderBy('order')->get();

		$order = 0;
		foreach ($objects as $object) {
			$object->order = $order++;
			$object->save();
		}
	}

	public function delete($id)
	{
		$model = App::make($this->model);

		$object = $model::find($id);
		if (method_exists($object, 'pre_delete')) {
			$object->pre_delete();
		}
		$object->delete();

		$this->reorder();

		return Redirect::to($this->uri())->with('success', '
			<p>' . $this->model . ' successfully deleted.</p>
			<p><a href="'.$this->uri('restore/' . $object->id, true).'">Undo</a></p>
		');
	}

	public function restore($id)
	{
		$model = App::make($this->model);

		$object = $model::withTrashed()->find($id);
		if (method_exists($object, 'pre_restore')) {
			$object->pre_restore();
		}
		$object->restore();

		$this->reorder();

		return Redirect::to($this->uri())->with('success', '
			<p>' . $this->model . ' successfully restored.</p>
		');
	}

	public function hard_delete($id, $ajax = false)
	{
		$model = App::make($this->model);

		$object = $model::withTrashed()->findOrFail($id);
		if (method_exists($object, 'pre_hard_delete')) {
			$object->pre_hard_delete();
		}
		$object->forceDelete();

		$this->reorder();

		if ($ajax) return 1;

		return Redirect::to($this->uri())->with('success', '
			<p>' . $this->model . ' successfully deleted forever.</p>
		');
	}

}