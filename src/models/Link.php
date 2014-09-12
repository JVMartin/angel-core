<?php namespace Angel\Core;

use Config;

class Link extends LinkableModel {

	public $timestamps = false;

	public static function columns()
	{
		return array(
			'name',
			'url'
		);
	}

	public function validate_rules()
	{
		return array(
			'name' => 'required',
			'url'  => 'required'
		);
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
	public function search($terms)
	{
		return static::where(function($query) use ($terms) {
			foreach ($terms as $term) {
				$query->orWhere('name', 'like', $term);
				$query->orWhere('url',  'like', $term);
			}
		})->get();
	}
}