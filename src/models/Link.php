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
}