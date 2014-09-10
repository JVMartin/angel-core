<?php namespace Angel\Core;

class AdminLinkController extends AdminCrudController {

	protected $Model	= 'Link';
	protected $uri		= 'links';
	protected $plural	= 'link_models';
	protected $singular	= 'link';
	protected $package	= 'core';

}