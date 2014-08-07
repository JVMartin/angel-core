<?php namespace Angel\Core;

use App, Input, Config, View, Validator, Redirect;

class AdminPageController extends AdminCrudController {

	protected $Model	= 'Page';
	protected $uri		= 'pages';
	protected $plural	= 'pages';
	protected $singular	= 'page';
	protected $package	= 'core';

	protected $log_changes = true;
	protected $searchable  = array(
		'name',
		'url',
		'title',
		'plaintext'
	);

	// Columns to update on edit/add
	protected static function columns()
	{
		$columns = array(
			'name',
			'url',
			'html',
			'js',
			'css',
			'title',
			'meta_description',
			'meta_keywords',
			'og_type',
			'og_image',
			'twitter_card',
			'twitter_image',
			'published',
			'published_range',
			'published_start',
			'published_end'
		);
		if (Config::get('core::languages')) $columns[] = 'language_id';
		return $columns;
	}

	public function after_save($page, &$changes = array())
	{
		$page->plaintext = strip_tags($page->html);
		$page->save();


		$PageModule    = App::make('PageModule');
		$modules       = $PageModule::where('page_id', $page->id)->get();
		$input_modules = Input::get('modules');

		$input_module_ids = array();
		foreach ($input_modules as $number=>$input_module) {
			// If there's only one module and it's blank, skip it.
			if (!$input_module['name'] && !$input_module['html'] && count($input_modules) == 1) continue;

			// Create a list of module IDs so we can delete the missing modules (which must have been deleted).
			if ($input_module['id']) $input_module_ids[] = $input_module['id'];

			// Grab the existing module if it exists.
			$module_existing = $modules->find($input_module['id']);
			$module          = ($module_existing) ? $module_existing : new $PageModule;

			// If the module exists, log its changes.
			$input_module['number'] = $number;
			if ($module_existing) {
				foreach (array('number', 'html', 'name') as $column) {
					if ($input_module[$column] != $module->$column) {
						$changes['Module ID#' . $module->id . ' ' . $column] = array(
							'old' => $module->$column,
							'new' => $input_module[$column]
						);
					}
				}
			}

			// Save that bad boy.
			$module->page_id = $page->id;
			$module->number  = $number;
			$module->name    = $input_module['name'];
			$module->html    = $input_module['html'];
			$module->save();
		}

		// Delete the deleted modules.
		foreach ($modules as $module) {
			if (!in_array($module->id, $input_module_ids)) {
				$module->delete();
			}
		}
	}

	public function edit($id)
	{
		$Page = App::make('Page');

		$page = $Page::withTrashed()->with('modules')->find($id);

		$this->data['page']    = $page;
		$this->data['changes'] = $page->changes();
		$this->data['action']  = 'edit';

		return View::make($this->view('add-or-edit'), $this->data);
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
		$rules = array(
			'name' => 'required',
			'url'  => 'alpha_dash'
		);
		$validator = Validator::make(Input::all(), $rules);
		$errors = ($validator->fails()) ? $validator->messages()->toArray() : array();
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
			'title'           => Input::get('title') ? Input::get('title') : Input::get('name'),
			'published'       => Input::get('published') ? 1 : 0,
			'published_range' => Input::get('published_range') ? 1 : 0,
			'published_start' => $published_start,
			'published_end'   => $published_end,
			'url'             => strtolower(Input::get('url'))
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
		$Page = App::make('Page');

		$page = $Page::where('url', Input::get('url'));
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
		$Page = App::make('Page');
		$Language = App::make('Language');

		$pages = $Page::where('language_id', $this->data['active_language']->id);
		if (!Input::get('all')) {
			$pages = $pages->whereIn('id', Input::get('ids'));
		}
		$pages = $pages->get();

		$errors = $success = '';
		$target_language = $Language::findOrFail(Input::get('language_id'));
		foreach ($pages as $page) {
			// Make sure a page with that URL doesn't already exist
			if ($Page::withTrashed()->where('language_id', $target_language->id)->where('url', $page->url)->count()) {
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