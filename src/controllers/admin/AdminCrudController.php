<?php namespace Angel\Core;

use App, View, Input, Redirect, Session, Request;

abstract class AdminCrudController extends AngelController {

	/*
	// Required:
	protected $Model	= 'ProductCategory';
	protected $uri		= 'products/categories';
	protected $plural	= 'categories';
	protected $singular	= 'category';
	protected $package	= 'products';
	*/

	/**
	 * A (sometimes searchable) index of all the model objects.  If the models are reorderable, they are
	 * displayed all at once.  Otherwise, they are paginated.
	 *
	 * @return \Illuminate\View\View
	 */
	public function index()
	{
		$Model   = App::make($this->Model);
		$objects = $Model::withTrashed();

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
		if ($Model::$reorderable) {
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

	/**
	 * Show the form for adding.
	 *
	 * Both add() and edit() use the same view:  add-or-edit
	 *
	 * There are two ways of adding a new item.  One is from its index,
	 * and the other is from the menu index's 'Add Link' wizard.
	 *
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
	 */
	public function add()
	{
		// Are we creating this object from the menu add wizard?
		// Toss the menu_id URL variable into the session instead
		if (Input::get('menu_id')) {
			return Redirect::to($this->uri('add'))->with('menu_id', Input::get('menu_id'));
		}
		// And grab the menu_id from the session or the old input, depending on whether we're new here
		if (Session::has('menu_id')) {
			$this->data['menu_id'] = Session::get('menu_id');
		} else if (Input::old('menu_id')) {
			$this->data['menu_id'] = Input::old('menu_id');
		}

		$this->data['action'] = 'add';
		return View::make($this->view('add-or-edit'), $this->data);
	}

	/**
	 * When attempting to add a new model, we simply call validate() on it
	 * and then save it.  All models that extend AngelModel have a saving()
	 * model event that calls the assign() method and fills the model's columns/properties
	 * from the posted inputs.
	 *
	 * @return $this->add_redirect() - Where to go after a successful add?
	 */
	public function attempt_add()
	{
		$Model   = App::make($this->Model);
		$object  = new $Model;

		$errors = $object->validate();
		if (count($errors)) {
			return Redirect::to($this->uri('add'))->withInput()->withErrors($errors);
		}

		$object->save();

		// Are we creating this object from the menu wizard?  (And it isn't a MenuItem?)
		// NOTE:  You only need this for menu-linkable models
		if (Input::get('menu_id') && !Request::is(admin_uri('menus/items/add'))) {
			return $this->also_add_menu_item($object);
		}

		return $this->add_redirect($object);
	}

	/**
	 * Where to go after a successful add?
	 *
	 * Often times, we want to just change where we redirect to after adding the model.
	 * That is why this function exists.
	 *
	 * @param AngelModel $object - The model we just added.
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function add_redirect($object)
	{
		return Redirect::to($this->uri())->with('success', '
			<p>' . $this->Model . ' successfully created.</p>
		');
	}

	/**
	 * Show the model form for editing.
	 *
	 * Both add() and edit() use the same view:  add-or-edit
	 *
	 * @param int $id - The ID of the model we're editing.
	 * @return \Illuminate\View\View
	 */
	public function edit($id)
	{
		$Model = App::make($this->Model);

		$this->data[$this->singular] = $Model::withTrashed()->findOrFail($id);
		$this->data['action']        = 'edit';

		return View::make($this->view('add-or-edit'), $this->data);
	}

	/**
	 * Edit a model.
	 *
	 * When attempting to edit a model, we simply call validate() on it
	 * and then save it.  All models that extend AngelModel have a saving()
	 * model event that calls the assign() method and fills the model's columns/properties
	 * from the posted inputs.
	 *
	 * @param int $id - The ID of the model we're editing.
	 * @return $this->edit_redirect() - Where to go after a successful edit?
	 */
	public function attempt_edit($id)
	{
		$Model  = App::make($this->Model);
		$object = $Model::withTrashed()->findOrFail($id);

		$errors = $object->validate();
		if (count($errors)) {
			return Redirect::to($this->uri('edit/' . $id))->withInput()->withErrors($errors);
		}

		$object->save();

		return $this->edit_redirect($object);
	}

	/**
	 * Where to go after a successful edit?
	 *
	 * Often times, we want to just change where we redirect to after editing the model.
	 * That is why this function exists.
	 *
	 * @param AngelModel $object - The model we just edited.
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function edit_redirect($object)
	{
		return Redirect::to($this->uri('edit/' . $object->id))->with('success', '
			<p>' . $this->Model . ' successfully updated.</p>
			<p><a href="' . $this->uri('', true) . '">Return to index</a></p>
		');
	}

	/**
	 * AJAX for reordering objects.
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
	 * Delete a model by ID.
	 *
	 * @param int $id - The ID of the model we're deleting.
	 * @param bool $ajax - Is this an AJAX deletion?
	 * @return $this->delete_redirect() - Where to go after a deletion?
	 */
	public function delete($id, $ajax = false)
	{
		$Model = App::make($this->Model);

		$object = $Model::findOrFail($id);
		$object->delete();

		if ($ajax) return 1;

		return $this->delete_redirect();
	}

	/**
	 * Where to go after a deletion?
	 *
	 * Often times, we want to just change where we redirect to after deleting the model.
	 * That is why this function exists.
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 */
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

	/**
	 * Handle adding new menu items when creating content (such as pages) from within the menu system.
	 *
	 * @param AngelModel $object - The model we just added.
	 * @return Redirect to the menu index with success message.
	 */
	protected function also_add_menu_item($object)
	{
		$MenuItem = App::make('MenuItem');

		$menuItem = new $MenuItem;
		$menuItem->skipEvents = true;
		$menuItem->menu_id    = Input::get('menu_id');
		$menuItem->fmodel     = $this->Model;
		$menuItem->fid        = $object->id;
		$menuItem->order      = $MenuItem::where('menu_id', Input::get('menu_id'))->count();
		$menuItem->save();

		return Redirect::to(admin_uri('menus'))->with('success', $this->Model . ' and menu link successfully created.');
	}

}