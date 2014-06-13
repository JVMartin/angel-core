Angel CMS
=====
Angel is a CMS built on top of Laravel.  It is available via [Packagist](https://packagist.org/packages/angel/core).

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

After the dependency has been loaded, add the following to your Service Providers in `app/config/app.php`:
```php
'Angel\Core\CoreServiceProvider'
```

Delete all the default routes and all the filters except for the `csrf` filter.

Finally, issue the following commands:
```bash
php artisan asset:publish angel/core         # Publish the assets
php artisan config:publish angel/core        # Publish the config
php artisan migrate --package="angel/core"   # Run the migrations
```
