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

	/**
	 * Convert dollars and cents to a number of pennies for Stripe usage.
	 * i.e.:  $1044.22 -> 104422
	 *
	 * @param float $dollars
	 * @return int - The number of pennies.
	 */
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
	 *
	 * @return true/false
	 */
	static function mysql_greater($a, $b, $c)
	{
		$version = explode('.', static::mysql_version());
		if ($a <= $version[0] && $b <= $version[1] && $c <= $version[2]) return true;
		return false;
	}

	/**
	 * Handle the creation of the slug and verifying that it is unique.
	 * Slugs are used for URLs generally, like: http://yoursite.com/products/large-green-ball
	 *
	 * @param string $model - The model object.
	 * @param string $slugSeed - The column with which to seed the slug.
	 * @return string $unique_slug - The unique slug.
	 */
	static function slug($model, $slugSeed)
	{
		$slug        = static::sluggify($model->$slugSeed);
		$unique_slug = $slug;
		$i           = 1;

		do {
			$not_unique = $model->where('slug', $unique_slug);
			if ($model->id) $not_unique = $not_unique->where('id', '<>', $model->id);
			$not_unique = $not_unique->count();
			if ($not_unique) $unique_slug = $slug . '-' . $i++;
		} while ($not_unique);

		return $unique_slug;
	}

	/**
	 * Turn a string into a slug.
	 * i.e.: 'Large Green Ball' -> 'large-green-ball'
	 *
	 * @param string $name - The string to sluggify.
	 * @return string $slug - The sluggified string.
	 */
	static function sluggify($name)
	{
		$slug = strtolower($name);
		$slug = strip_tags($slug);
		$slug = stripslashes($slug);

		$slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
		$slug = trim($slug, '-');

		return $slug;
	}

}