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
		'url',
		'title',
		'html'
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

		$PageModule = App::make('PageModule');

		// Update changes to PageModules and log the changes.
		if (!$changes) $changes = array();
		$input_modules = Input::get('modules');
		$input_moduleNames = Input::get('moduleNames');
		foreach ($input_modules as $number=>$html) {
			$name = $input_moduleNames[$number];
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
				if (!$html && !$name && $number == 1 && count($input_modules) == 1) continue; // Don't create a module when it's -just- a blank Module 1
				$module = new $PageModule;
				$module->page_id	= $page->id;
				$module->number		= $number;
				$module->html		= $html;
				$module->name		= $name;
				$module->save();
			}
		}
	}

	public function edit($id)
	{
		$Page = App::make('Page');

		$page = $Page::withTrashed()->with('modules')->find($id);
		$this->data['page'] = $page;
		$this->data['changes'] = $page->changes();
		$this->data['action'] = 'edit';

		return View::make($this->package . '::admin.pages.add-or-edit', $this->data);
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
			'url' => 'alpha_dash'
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