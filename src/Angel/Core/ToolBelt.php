<?php namespace Angel\Core;

use DB, Session;

class ToolBelt {

	static function debug($echo)
	{
		echo '<pre>';
		print_r($echo);
		echo '</pre>';
	}

	static function print_queries()
	{
		static::debug(DB::getQueryLog());
	}

	static function print_session()
	{
		static::debug(Session::all());
	}

}