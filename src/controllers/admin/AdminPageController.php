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

}