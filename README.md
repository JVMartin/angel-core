Angel CMS
=====
Angel is a CMS built on top of Laravel.  It is available via [Packagist](https://packagist.org/packages/angel/core).

Try It
------
Check out a [live deployment of the CMS here](http://angel-test.angelvision.tv/).

Sign into the [admin section](http://angel-test.angelvision.tv/admin) with the credentials:
```
User: avadmin
Pass: password
```

Installation
------------
We are currently using Laravel 4.1 for this CMS until 4.2 is more stable.

Install Laravel 4.1 using the following command:
```bash
composer create-project laravel/laravel --prefer-dist {project-name} 4.1.*
```

Add the `angel/core` package requirement to your `composer.json` file, like this:
```javascript
"require": {
    "laravel/framework": "4.1.*",
    "angel/core": "dev-master"
},
```

Issue a `composer update` to install the package.

After the package has been installed, open `app/config/app.php` and add the following to your `providers` array:
```php
'Angel\Core\CoreServiceProvider'
```

Delete all the default routes in `app/routes.php` and all the filters except for the `csrf` filter in `app/filters.php`.

You should also delete the file `app/models/User.php`.  You can replace it with a `.gitkeep` file for now to keep the `app/models` directory.

Create and configure your database so that we can run the migrations.

Finally, issue the following artisan commands:
```bash
php artisan asset:publish angel/core         # Publish the assets
php artisan config:publish angel/core        # Publish the config
php artisan migrate --package="angel/core"   # Run the migrations
```

Configuration
-------------
Take a look at the config file you just published in `app/packages/angel/core/config.php`.

### Languages
The first configurations are related to languages.  By default, only one language is used and your URLs will look like this for created pages:
```
http://www.website.com/about-us
http://www.website.com/contact-us
```

If you enable multiple languages, your URLs will look like this, for instance, with English and Spanish pages:
```
http://www.website.com/en/about-us
http://www.website.com/sp/about-us
http://www.website.com/en/contact-us
http://www.website.com/sp/contact-us
```

You can then, if you choose to, easily `mod_rewrite`-out the default language base URI so that your URLs look like this:
```
http://www.website.com/about-us
http://www.website.com/sp/about-us
http://www.website.com/contact-us
http://www.website.com/sp/contact-us
```

If you would like to enable this feature, you must choose to do so before you begin development.  This is because the `languages` table is only built, and the other language-related tables (including `pages`) only have their relationships built, when this configuration is set.  This ensures that the site is optimized either way you go.

To enable this feature, first roll back all your migrations (if you've already ran them):
```bash
php artisan migrate:rollback
```

Then, set the configuration in `app/packages/angel/core/config.php`:
```php
'languages' => true
```

And finally, run the migrations so the languages table and relationships will be built:
```bash
php artisan migrate --package="angel/core"
```

### Admin URL
By default, the following configuration is set:
```
'admin_prefix' => 'admin'
```

This allows one to access the administration panel via the url `http://yoursite.com/admin`.

Feel free to change this prefix, or to even remove it completely, which will cause `http://yoursite.com` to show the administration panel.

### Admin Menu
The next section is the `'menu'` array.  When you install modules, you add their indexes to this array so that they appear in the administration panel's menu.

### Menu Linkable Models
Some modules come with models that you can create menu links to in the `Menu` module.  This array is used by the `Menu Link Creation Wizard` on the `Menu` module's index.

Extending the Core
------------------
Every class in the core is easily extendable.

Let's start by extending the [PageController](https://github.com/JVMartin/angel/blob/master/src/controllers/PageController.php).

When extending this controller, you can create a method for each page URI that you've created in the administration panel.

Create the following file as `app/controllers/PageController.php`:

```php
<?php

class PageController extends \Angel\Core\PageController {
	
	public function home()
	{
		return 'You are home!';
	}

}
```

Register your new PageController in `app/routes.php` (or anywhere you prefer):
```php
App::singleton('PageController', function() {
	return new \PageController;
});
```

Now, you should be able to navigate to `http://yoursite.com/home` and see: `You are home!`.
