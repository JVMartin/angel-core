<?php namespace Angel\Core;

use App, View, Input, Redirect, Config, Session, Request;

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
	 * A searchable index of all the model objects.  If the models are reorderable, they are
	 * displayed all at once.  Otherwise, they are paginated.
	 *
	 * @return \Illuminate\View\View
	 */
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
	 * Both add()
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
	 * @return $this->add_redirect()
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
			return $this->also_add_menu_item($this->Model, $object->id);
		}

		return $this->add_redirect($object);
	}

	/**
	 * Often times, we want to just change where we redirect to after adding the model.
	 * That is why this function exists.
	 *
	 * @param $object - The model we just added.
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function add_redirect($object)
	{
		return Redirect::to($this->uri())->with('success', '
			<p>' . $this->Model . ' successfully created.</p>
		');
	}

	/**
	 * Show the model for editing.
	 *
	 * @param $id
	 * @return \Illuminate\View\View
	 */
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
		$object = $Model::withTrashed()->findOrFail($id);

		$errors = $object->validate();
		if (count($errors)) {
			return Redirect::to($this->uri('edit/' . $id))->withInput()->withErrors($errors);
		}

		$object->save();

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

	public function delete($id, $ajax = false)
	{
		$Model = App::make($this->Model);

		$object = $Model::findOrFail($id);
		$object->delete();

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

	/**
	 * Handle adding new menu items when creating content (such as pages) from within the menu system.
	 *
	 * @param string $fmodel - Name of the model.
	 * @param int $fid - ID of the model.
	 * @return Redirect to the menu index with success message.
	 */
	protected function also_add_menu_item($fmodel, $fid)
	{
		$MenuItem = App::make('MenuItem');

		$menuItem = new $MenuItem;
		$menuItem->skipEvents = true;
		$menuItem->menu_id    = Input::get('menu_id');
		$menuItem->fmodel     = $fmodel;
		$menuItem->fid        = $fid;
		$menuItem->order      = $MenuItem::where('menu_id', Input::get('menu_id'))->count();
		$menuItem->save();

		return Redirect::to(admin_uri('menus'))->with('success', $fmodel . ' and menu link successfully created.');
	}

}