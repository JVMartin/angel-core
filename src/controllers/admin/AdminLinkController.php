<?php namespace Angel\Core;

use Config;

class AdminLinkController extends AdminCrudController {

	protected $Model	= 'Link';
	protected $uri		= 'links';
	protected $plural	= 'link_models';
	protected $singular	= 'link';
	protected $package	= 'core';

	protected $searchable = array(
		'name',
		'url'
	);

	// Columns to update on edit/add
	protected static function columns()
	{
		$columns = array(
			'name',
			'url'
		);
		if (Config::get('core::languages')) $columns[] = 'language_id';
		return $columns;
	}

	public function validate_rules($id = null)
	{
		return array(
			'name' => 'required',
			'url'  => 'required'
		);
	}
}