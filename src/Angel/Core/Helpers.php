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