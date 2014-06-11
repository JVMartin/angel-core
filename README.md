Angel
=====
Angel is a CMS built on top of Laravel.  It is available via [Packagist](https://packagist.org/packages/angel/core).

Installation
------------
Add the following requirements to your `composer.json` file:
```javascript
"require": {
    "laravel/framework": "4.1.*",
    "angel/core": "dev-master"
},
```

After installing the dependencies, add the following to your Service Providers in `app/config/app.php`:
```
'Angel\Core\CoreServiceProvider'
```

Delete all the default routes and all the filters except for the `csrf` filter.

Finally, issue the following commands:
```
php artisan asset:publish angel/core         # Publish the assets
php artisan config:publish angel/core        # Publish the config
php artisan migrate --package="angel/core"   # Run the migrations
```
