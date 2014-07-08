<?php namespace Angel\Core;

class AdminLinkController extends AdminCrudController {

	protected $model	= 'Link';
	protected $uri		= 'links';
	protected $plural	= 'link_models';
	protected $singular	= 'link';
	protected $package	= 'core';

	public function index()
	{
		return $this->index_searchable(array('name', 'url'));
	}

	public function validate_rules($id = null)
	{
		return array(
			'name' => 'required',
			'url'  => 'required'
		);
	}
}