<?php

class ToolBelt {

	/**
	 * Echo out a variable in a readable manner.
	 *
	 * @param mixed $var - The variable to echo.
	 */
	static function debug($var)
	{
		echo '<pre>';
		print_r($var);
		echo '</pre>';
	}

	/**
	 * Echo out all queries that have been executed thus far.
	 */
	static function print_queries()
	{
		static::debug(DB::getQueryLog());
	}

	/**
	 * Echo out the session.
	 */
	static function print_session()
	{
		static::debug(Session::all());
	}

	/**
	 * Convert dollars and cents to a number of pennies for Stripe usage.
	 * i.e.:  $1044.22 -> 104422
	 *
	 * @param float $dollars
	 * @return int - The number of pennies.
	 */
	static function pennies($dollars)
	{
		return (int) str_replace('.', '', number_format((float) $dollars, 2, '.', ''));
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
	 * Test if the MySQL version is greater than or equal to a given version number.
	 *
	 * @param string $than - The version number that we're comparing to, i.e. '5.5.12'
	 * @return boolean
	 */
	static function mysql_greater($than)
	{
		return version_compare(static::mysql_version(), $than, '>=');
	}

}