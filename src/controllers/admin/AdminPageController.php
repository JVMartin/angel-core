<?php namespace Angel\Core;

use App, View, Redirect;

class AdminPageController extends AdminCrudController {

	protected $Model	= 'Page';
	protected $uri		= 'pages';
	protected $plural	= 'pages';
	protected $singular	= 'page';
	protected $package	= 'core';

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
	 * @TODO: Fix for new model event paradigm
	 * Copy any number of pages (or all pages) to another language.
	 *
	 * @return Redirect to index with errors and success messages
	 */
	/*public function copy()
	{
		$Page     = App::make('Page');
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
	}*/

}