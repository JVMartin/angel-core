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
		static::debug(DB::getQueryLog());
	}

	static function print_session()
	{
		static::debug(Session::all());
	}

	static function pennies($dollars)
	{
		return (int)str_replace('.', '', number_format((float)$dollars, 2, '.', ''));
	}

	/**
	 * Get the MySQL version of the server.
	 *
	 * @return string - i.e.: '5.5.12'
	 */
	static function mysql_version()
	{
		$version = DB::select('SELECT VERSION() AS `version`');
		$version = explode('-', $version[0]->version);
		$version = $version[0];
		return $version;
	}

	/**
	 * Test if the MySQL version is greater than a given number.
	 *
	 * i.e.:  \ToolBelt::mysql_greater(5, 5, 10);  // Greater than 5.5.10?
	 */
	static function mysql_greater($a, $b, $c)
	{
		$version = explode('.', static::mysql_version());
		if ($a <= $version[0] && $b <= $version[1] && $c <= $version[2]) return true;
		return false;
	}

}