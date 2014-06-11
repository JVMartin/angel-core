<?php

class ToolBelt {

	static function debug($echo)
	{
		echo '<pre>';
		print_r($echo);
		echo '</pre>';
	}

	static function print_queries()
	{
		ToolBelt::debug(DB::getQueryLog());
	}

	static function print_session()
	{
		ToolBelt::debug(Session::all());
	}

}