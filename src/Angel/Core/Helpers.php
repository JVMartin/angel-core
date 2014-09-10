<?php

function admin_uri($uri = '') {
	if (!$prefix = Config::get('core::admin_prefix')) {
		return $uri;
	}
	$uri = ltrim($uri, '/');
	return $prefix . '/' . $uri;
}

function admin_url($uri = '') {
	return url(admin_uri($uri));
}

function short_name($object) {
	$reflection = new \ReflectionClass($object);
	return $reflection->getShortName();
}

/**
 * Handle the creation of the slug and verifying that it is unique.
 * Slugs are used for URLs generally, like: http://yoursite.com/products/large-green-ball
 *
 * @param string $model - The model object.
 * @param string $slugSeed - The column with which to seed the slug.
 * @return string $unique_slug - The unique slug.
 */
function slug($model, $slugSeed) {
	$slug        = sluggify($model->$slugSeed);
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
function sluggify($name) {
	$slug = strtolower($name);
	$slug = strip_tags($slug);
	$slug = stripslashes($slug);

	$slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
	$slug = trim($slug, '-');

	return $slug;
}