<?php namespace Angel\Core;

use App;

class PageModule extends \Eloquent {

	protected $table = 'pages_modules';

	public function page()
	{
		return $this->belongsTo(App::make('Page'));
	}

}