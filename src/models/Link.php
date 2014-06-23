<?php namespace Angel\Core;

use Config;

class Link extends LinkableModel {

	public $timestamps = false;

	// Columns to update/insert on edit/add
	public static function columns()
	{
		$columns = array(
			'name',
			'url'
		);
		if (Config::get('core::languages')) $columns[] = 'language_id';
		return $columns;
	}

	///////////////////////////////////////////////
	//               Menu Linkable               //
	///////////////////////////////////////////////
	public function link()
	{
		return $this->url;
	}
	public function link_edit()
	{
		return admin_url('links/edit/' . $this->id);
	}
}