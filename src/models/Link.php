<?php namespace Angel\Core;

class Link extends LinkableModel {

	public $timestamps = false;

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