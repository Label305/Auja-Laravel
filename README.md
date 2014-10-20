# Auja-Laravel

[![Build Status](https://travis-ci.org/Label305/Auja-Laravel.svg?branch=dev)](https://travis-ci.org/Label305/Auja-Laravel)
[![Coverage Status](https://coveralls.io/repos/Label305/Auja-Laravel/badge.png?branch=dev)](https://coveralls.io/r/Label305/Auja-Laravel?branch=dev)
[![Latest Stable Version](https://poser.pugx.org/label305/auja-laravel/v/stable.svg)](https://packagist.org/packages/label305/auja-laravel)
[![Total Downloads](https://poser.pugx.org/label305/auja-laravel/downloads.svg)](https://packagist.org/packages/label305/auja-laravel)
[![Latest Unstable Version](https://poser.pugx.org/label305/auja-laravel/v/unstable.svg)](https://packagist.org/packages/label305/auja-laravel)

[Auja](http://label305.github.io/Auja/) is an easy-to-use and easy-to-implement back-end interface. It provides an easy and intuitive way for you to view and manipulate your data, so you can focus on more important matters. Auja is designed to be both user-friendly _and_ developer-friendly by providing you with tools to setup your back-end in no more than five minutes.

Auja's basis is the [Auja JavaScript repository](https://github.com/Label305/Auja), which provides the graphical user interface. To determine its content, it relies on specific JSON messages. [Auja-PHP](https://github.com/Label305/Auja-PHP) in turn, provides an Object Oriented approach to provide these JSON messages in PHP. In this repository you will find a Laravel implementation which you can use to setup Auja in a matter of minutes.

## Related repositories
  
  - [**Auja**](https://github.com/Label305/Auja) - The JavaScript GUI implementation.
  - [**Auja-PHP**](https://github.com/Label305/Auja-PHP) - Auja's protocol implemented in an Object Oriented manner, in PHP.
  - [**Auja-Laravel-Example**](https://github.com/Label305/Auja-Laravel-Example) - An example Laravel project using Auja.

## Setup

Auja-Laravel is available on [Packagist](https://packagist.org/packages/label305/auja-laravel).

 - Run `composer require label305/auja:v3.0.0-alpha1 label305/auja-laravel:dev-dev`;
 - Add `'Auja' => 'Label305\AujaLaravel\AujaFacade'` to your list of aliases in `app\config\app.php`;
 - Copy [`assets/index.php`](https://raw.githubusercontent.com/Label305/Auja-Laravel/dev/assets/index.php) to your `views` folder;
 - Copy [`assets/AujaController.php`](https://raw.githubusercontent.com/Label305/Auja-Laravel/dev/assets/AujaController.php) to your `controllers` folder;
 - Copy the folders [`assets/assets`](https://github.com/Label305/Auja-Laravel/tree/dev/assets/assets), [`assets/bower_components`](https://github.com/Label305/Auja-Laravel/tree/dev/assets/bower_components) and [`assets/build`](https://github.com/Label305/Auja-Laravel/tree/dev/assets/build) to your `public` folder;
 - Add the following to `routes.php`:
 
```php
Route::get('/', 'AujaController@index');
Route::get('main', 'AujaController@main');
``` 

## Getting Started

 - Setup Resources (Tip: [Way Generators](https://github.com/JeffreyWay/Laravel-4-Generators));
 - For each of your resources, add `AujaRoute::resource('{model name}', '{controller name}')` to `routes.php`:

```php
AujaRoute::resource('Club', 'ClubsController');
AujaRoute::resource('Team', 'TeamsController');
```

 - Create a new ServiceProvider class, which extends `'Label305\AujaLaravel\AujaServiceProvider'`, add it to your providers in `app\config\app.php` and implement the `getModelNames()` function:
 
```php
use Label305\AujaLaravel\AujaServiceProvider;

class AujaPresenterServiceProvider extends AujaServiceProvider {

    /**
     * Returns a String array of model names, e.g. ['Club', 'Team'].
     *
     * @return String[] The model names.
     */
    function getModelNames() {
        return ['Club', 'Team'];
    }
}
```

 - In each of your resource controllers, implement at least the following functions:
   - `index()`, `menu($id = 0)`, `create()`, `store()`, `show($id)`, `edit($id)`, `update($id)`, `delete($id)`. 

```php
class ClubsController extends \BaseController {

	const NAME = 'Club';

	public function index() {
		return Auja::buildResourceItems(self::NAME, Club::simplePaginate(10));
	}

	public function menu($id = 0) {
		return Auja::buildIndexMenu(self::NAME, $id);
	}

	public function create() {
		return Auja::buildPage(self::NAME);
	}

	public function store() {
		Club::create(Input::all());
	}
	
	public function show() {
	  
	}

	public function edit($id) {
		return Auja::buildPage(self::NAME, $id);
	}

	public function update($id) {
		$club = Club::find($id);
		$club->fill(Input::all());
		$club->save();
	}

	public function delete($id) {
		$club = Club::find($id);
		$club->delete($id);
	}
}
```

That's it, you're done!

## License

Copyright 2014 Label305 B.V.

Licensed under the Apache License, Version 2.0 (the "License");  
you may not use this file except in compliance with the License.  
You may obtain a copy of the License at

[http://www.apache.org/licenses/LICENSE-2.0](http://www.apache.org/licenses/LICENSE-2.0)

Unless required by applicable law or agreed to in writing, software  
distributed under the License is distributed on an "AS IS" BASIS,  
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.  
See the License for the specific language governing permissions and  
limitations under the License.
