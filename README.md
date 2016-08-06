Angel CMS for Laravel 4
=======================
Angel is a CMS built on top of Laravel.  It is available via [Packagist](https://packagist.org/packages/angel/core).

**UPDATE 8/5/2016**:  The Laravel 5 version of Angel is [underway here](https://github.com/JVMartin/angel5).

**UPDATE 3/9/2015**:  Just wanted to give a heads up that this project is still in very active usage and deployment and is well tested by several large applications.  The [eCommerce module](https://github.com/JVMartin/angel-products) that works with Stripe is also well tested and used.

Table of Contents
-----------------
* [Demo](#demo)
* [Installation](#installation)
* [Customize](#customize)
* [Configuration](#configuration)
* [Using Slugs](#using-slugs)
* [Develop Modules](#develop-modules)

Demo
----
[![Demo Screenshot](/assets/demo.jpg?raw=true)](http://youtu.be/et0QXJLHXoQ)

Installation
------------
[![Installation Screenshot](/assets/install.jpg?raw=true)](http://youtu.be/2nAyi-u9jp0)

The Angel CMS was built on top of Laravel 4.1.

Install Laravel 4.1 using the following command:
```bash
composer create-project laravel/laravel --prefer-dist {project-name} 4.1.*
```

Add the `angel/core` package requirement to your `composer.json` file, like this:
```javascript
"require": {
    "laravel/framework": "4.1.*",
    "angel/core": "1.0.*"
},
```

Issue a `composer update` to install the package.

After the package has been installed, open `app/config/app.php` and add the following to your `providers` array:
```php
'Angel\Core\CoreServiceProvider'
```
While you're in there, set `debug` to true.

Delete:
* All the default routes in `app/routes.php`.
* All the default filters except for the `csrf` filter in `app/filters.php`.
* All controllers in `app/controllers` except `BaseController.php`.
* All the models in `app/models`, including `User.php`.  You can replace it with a `.gitkeep` file for now to be sure to keep the `app/models` directory.

Create and configure your database so that we can run the migrations.

Finally, issue the following artisan commands:
```bash
php artisan dump-autoload                 # Dump a load
php artisan asset:publish                 # Publish the assets
php artisan config:publish angel/core     # Publish the config
php artisan migrate --package=angel/core  # Run the migrations
mkdir -p public/uploads/kcfinder          # Create the KCFinder uploads folder
touch public/uploads/kcfinder/.gitkeep    # Keep the folder
```

Customize
---------
[![Customize Screenshot](/assets/customize.jpg?raw=true)](http://youtu.be/6iuZ8p-x5bY)

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

Remove the old binding and bind your new class at the top of your `routes.php` file:
```
App::offsetUnset('PageController');
App::singleton('PageController', function() {
	return new \PageController;
});
```

Do a `composer dump-autoload`.

Now, you should be able to navigate to `http://yoursite.com/home` and see: `You are home!`.

Configuration
-------------
Take a look at the config file you just published in `app/config/packages/angel/core/config.php`.

### Admin URL
By default, the following configuration is set:
```
'admin_prefix' => 'admin'
```

This allows one to access the administration panel via the url `http://yoursite.com/admin`.

To be secure, you may want to change this prefix.  Hackers tend to target sites with URLs like this.

### Admin Menu
The next section is the `'menu'` array.  When you install modules, you add their indexes to this array so that they appear in the administration panel's menu.

### Menu Linkable Models
Some modules come with models that you can create menu links to in the `Menu` module.  This array is used by the `Menu Link Creation Wizard` on the `Menu` module's index.


Using Slugs
---------------------
Often times, you will want to let users access products, blog posts, news articles, etc. by name instead of by ID in the URL.

For instance: `http://yoursite.com/products/big-orange-ball`.

To do this, you want to 'sluggify' one of the columns / properties of the model.

If you are extending the [AngelModel](https://github.com/JVMartin/angel/blob/master/src/models/AngelModel.php), this is as simple as adding a `slug` column to your table with a unique index:

```php
$table->string('slug')->unique();
```

And then setting the `slugSeed` property of your model to the name of the column from which to generate the slug:
```php
protected $slugSeed = 'name';
```

Now, slugs will be automatically generated from the `name` column of the models as they are created or edited.  (You can just as easily use a `title` column or any other appropriate source.)

You can use the generated slugs after adding or editing some items.

For instance:
```php
// app/routes.php
Route::get('products/{slug}', 'ProductController@view');

// app/controllers/ProductController.php
class ProductController extends \Angel\Core\AngelController {

	public function view($slug)
	{
		$Product = App::make('Product');
		$this->data['product'] = $Product::where('slug', $slug)->firstOrFail();
		return View::make('products.view', $this->data);
	}
	
}
```

### Creating Unique Slugs Manually
```php
// Adding a new item:
$article        = new NewsArticle;
$article->title = Input::get('title');
$article->slug  = slug($article, 'title');
$article->save();

// Editing an item:
$article        = Article::find(1);
$article->title = Input::get('title');
$article->slug  = slug($article, 'title');
$article->save();
```

### Sluggifying a String
```php
$slug = sluggify('String to sluggify!'); // Returns 'string-to-sluggify'
```

Develop Modules
---------------
Here is where we'll put code snippets for developing modules.

### Reorderable Indexes

Assume we're developing a `persons` module package.

First, make sure that `Person` extends `\Angel\Core\AngelModel` and has the property `protected $reorderable = true;`.

```php
// workbench/persons/src/views/admin/persons/index.blade.php
@section('js')
    {{ HTML::script('packages/angel/core/js/jquery/jquery-ui.min.js') }}
    <script>
    	$(function() {
            $('tbody').sortable(sortObj);
    	});
    </script>
@stop
@section('content')
    <table class="table table-striped">
        <tbody data-url="persons/order"><!-- This data-url is appended to the admin url and posted. -->
            @foreach ($persons as $person)
                <tr data-id="{{ $person->id }}">
                    {{ Form::hidden(null, $person->order, array('class'=>'orderInput')) }}
                    <button type="button" class="btn btn-xs btn-default handle">
                        <span class="glyphicon glyphicon-resize-vertical"></span>
                    </button>
                </tr>
            @endforeach
        </tbody>
    </table>
@stop

// workbench/persons/src/routes.php
Route::group(array('prefix' => admin_uri('persons'), 'before' => 'admin'), function() {
	Route::post('order', 'AdminPersonsController@order');
});
```
