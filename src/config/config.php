<?php

$config = array(
	// Migrations use the 'languages' setting in their logic.
	// As such, the database schema changes depending upon whether multiple languages need support.
	'languages'			=> false,
	'language_primary'	=> 'en',
	// All models that have a language relationship must be defined here.
	'language_models'	=> array(
		'Page'
	),

	// This is the route URI prefix for the admin pages.
	// You may set this to blank for no prefix, in which case www.website.com will land on the
	// admin sign-in page.
	'admin_prefix' => 'admin',

	// This is the menu in the admin console.
	// Add modules here after installation.
	// 'Name' => 'admin uri'
	'menu' => array(
		'Pages'		=> 'pages',
		'Menus'		=> 'menus',
		'Links'		=> 'links',
		'Users'		=> 'users',
		'Settings'	=> 'settings'
	),

	// All linkable models must be declared here.
	// 'Model Name' => 'admin uri'
	'linkable_models'	=> array(
		'Page' => 'pages',
		'Link' => 'links',
	)

);

if ($config['languages'] && Auth::check() && Auth::user()->is_superadmin()) {
	$config['menu']['Languages'] = 'languages';
}

return $config;