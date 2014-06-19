<?php namespace Angel\Core;

use App, Input, Config, View;

class AdminPageController extends AdminCrudController {

	protected $model	= 'Page';
	protected $uri		= 'pages';
	protected $plural	= 'pages';
	protected $singular	= 'page';
	protected $package	= 'core';

	public function index()
	{
		$pageModel = App::make('Page');

		$search = Input::get('search') ? urldecode(Input::get('search')) : null;
		$paginator = $pageModel::withTrashed();
		if (Config::get($this->package . '::languages')) {
			$paginator = $paginator->with('language')
				                   ->where('language_id', $this->data['active_language']->id);
		}
		if ($search) {
			$terms = explode(' ', $search);
			$paginator = $paginator->where(function($query) use ($terms) {
				foreach ($terms as $term) {
					$term = '%'.$term.'%';
					$query->orWhere('url', 'like', $term)
						  ->orWhere('title', 'like', $term)
						  ->orWhere('html', 'like', $term);
				}
			});
		}
		$paginator = $paginator->paginate();

		$this->data['pages'] = $paginator->getCollection();
		$appends = $_GET;
		unset($appends['page']);
		$this->data['links'] = $paginator->appends($appends)->links();
		$this->data['search'] = $search;
		return View::make($this->package . '::admin.pages.index', $this->data);
	}

	public function attempt_add()
	{
		$pageModel = App::make('Page');

		$errors = $this->validate($custom);
		if (count($errors)) {
			return Redirect::to(admin_uri('pages/add'))->withInput()->withErrors($errors);
		}

		$page = new $pageModel;
		foreach($pageModel::columns() as $column) {
			$page->{$column} = isset($custom[$column]) ? $custom[$column] : Input::get($column);
		}
		$page->save();
		$this->handle_modules($page);

		// Are we creating a page from the menu wizard?
		if (Input::get('menu_id')) {
			return $this->also_add_menu_item('Page', $page->id);
		}

		return Redirect::to(admin_uri('pages'))->with('success', '
			<p>Page successfully created.</p>
			<p><a href="'.url($page->link()).'" target="_blank">View Page</a></p>
		');
	}

	public function edit($id)
	{
		$pageModel = App::make('Page');

		$page = $pageModel::withTrashed()->with('modules')->find($id);
		$this->data['page'] = $page;
		$this->data['changes'] = $page->changes();
		$this->data['action'] = 'edit';

		return View::make($this->package . '::admin.pages.add-or-edit', $this->data);
	}

	public function attempt_edit($id)
	{
		$pageModel = App::make('Page');
		$changeModel = App::make('Change');

		$errors = $this->validate($custom, $id);
		if (count($errors)) {
			return Redirect::to(admin_uri('pages/edit/'.$id))->withInput()->withErrors($errors);
		}

		$page = $pageModel::withTrashed()->with('modules')->findOrFail($id);
		$changes = array();
		foreach($pageModel::columns() as $column) {
			$value = isset($custom[$column]) ? $custom[$column] : Input::get($column);

			// If the value has changed...
			if ($page->{$column} != $value) {
				// Log the change
				$changes[$column] = array(
					'old' => $page->{$column},
					'new' => $value
				);
				// Update the value
				$page->{$column} = $value;
			}
		}
		$page->save();
		$this->handle_modules($page, $changes);

		if (count($changes)) {
			$change = new $changeModel;
			$change->user_id 	= Auth::user()->id;
			$change->fmodel		= 'Page';
			$change->fid 		= $page->id;
			$change->changes 	= json_encode($changes);
			$change->save();
		}

		return Redirect::to(admin_uri('pages/edit/'.$id))->with('success', '
			<p>Page successfully updated.</p>
			<p><a href="'.url($page->link()).'" target="_blank">View Page</a></p>
			<p><a href="'.admin_url('pages').'">Return to Pages</a></p>
		');
	}

	private function handle_modules($page, &$changes = null)
	{
		$pageModuleModel = App::make('PageModule');

		if (!$changes) $changes = array();
		$input_modules = Input::get('modules');
		$input_moduleNames = Input::get('moduleNames');
		foreach ($input_modules as $number=>$html) {
			$found_module = false;
			foreach ($page->modules as $module) {
				if ($number == $module->number) {
					$found_module = true;
					if ($html != $module->html) {
						$changes['Module ' . $module->number . ' HTML'] = array(
							'old' => $module->html,
							'new' => $html
						);
						$module->html = $html;
					}
					if ($input_moduleNames[$number] != $module->name) {
						$changes['Module ' . $module->number . ' Name'] = array(
							'old' => $module->name,
							'new' => $input_moduleNames[$number]
						);
						$module->name = $input_moduleNames[$number];
					}
					$module->save();
					break;
				}
			}
			if (!$found_module) {
				if (!$html && $number == 1 && count($input_modules) == 1) continue; // Don't create a module when it's -just- a blank Module 1
				$module = new $pageModuleModel;
				$module->page_id	= $page->id;
				$module->number		= $number;
				$module->html		= $html;
				$module->name		= $input_moduleNames[$number];
				$module->save();
			}
		}
	}

	/**
	 * Validate all input when adding or editing a page.
	 *
	 * @param array &$custom - This array is initialized by this function.  Its purpose is to
	 * 							exclude certain columns that require intervention of some kind (such as
	 * 							checkboxes because they aren't included in input on submission)
	 * @param int $id - (Optional) ID of page beind edited
	 * @return array - An array of error messages to show why validation failed
	 */
	public function validate(&$custom, $id = null)
	{
		$errors = array();
		$rules = array(
			'name' => 'required',
			'url' => 'required|alpha_dash'
		);
		$validator = Validator::make(Input::all(), $rules);
		if ($validator->fails()) {
			foreach($validator->messages()->all() as $error) {
				$errors[] = $error;
			}
		}
		if ($this->url_taken($id)) {
			$errors[] = 'A page with that URL in that language already exists.';
		}

		$published_start = Input::get('published_start');
		$published_end = Input::get('published_end');
		if (Input::get('published_range') && $published_end && strtotime($published_start) >= strtotime($published_end)) {
			$errors[] = 'The publication end time must come after the start time.';
		} else if (!Input::get('published_range')) {
			// Reset these so that we won't ever get snagged by an impossible range
			// if the user has collapsed the publication range expander.
			$published_start = $published_end = 0;
		}

		$custom = array(
			'published'			=> Input::get('published') ? 1 : 0,
			'published_range'	=> Input::get('published_range') ? 1 : 0,
			'published_start'	=> $published_start,
			'published_end'		=> $published_end,
			'url'				=> strtolower(Input::get('url'))
		);

		return $errors;
	}

	/**
	 * Determine whether an URL is already taken in the specified language.
	 *
	 * @param int $id - (Optional) ID of page to exclude
	 * @return bool
	 */
	public function url_taken($id = null)
	{
		$pageModel = App::make('Page');

		$page = $pageModel::where('url', Input::get('url'));
		if (Config::get('core::languages')) {
			$page = $page->where('language_id', Input::get('language_id'));
		}
		if ($id) {
			$page = $page->where('id', '<>', $id);
		}
		return $page->get()->count();
	}

	/**
	 * Copy any number of pages (or all pages) to another language.
	 *
	 * @return Redirect to index with errors and success messages
	 */
	public function copy()
	{
		$pageModel = App::make('Page');
		$languageModel = App::make('Language');

		$pages = $pageModel::where('language_id', $this->data['active_language']->id);
		if (!Input::get('all')) {
			$pages = $pages->whereIn('id', Input::get('ids'));
		}
		$pages = $pages->get();

		$errors = $success = '';
		$target_language = $languageModel::findOrFail(Input::get('language_id'));
		foreach ($pages as $page) {
			// Make sure a page with that URL doesn't already exist
			if ($pageModel::withTrashed()->where('language_id', $target_language->id)->where('url', $page->url)->count()) {
				$errors .= '
					<p>
						Could not copy page with url "' . $page->url . '"
						- a page with that url already exists in "' . $target_language->name . '".
					</p>
				';
				continue;
			}

			// Copy the page
			$new_page = $page->replicate();
			$new_page->language_id = $target_language->id;
			$new_page->save();

			// Copy all the page's modules to the new page
			foreach ($page->modules as $module) {
				$new_module = $module->replicate();
				$new_module->page_id = $new_page->id;
				$new_module->save();
			}

			$success .= '
				<p>Copied page with url "' . $page->url . '" to "' . $target_language->name . '".</p>
			';
		}
		$redirect = Redirect::to(admin_uri('pages'));
		if ($success) $redirect = $redirect->with('success', $success);
		if ($errors) $redirect = $redirect->withErrors($errors);
		return $redirect;
	}

}