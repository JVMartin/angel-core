<?php

$config = array(
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

return $config;