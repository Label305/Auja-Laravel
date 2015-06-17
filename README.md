# Auja for Laravel

[![Build Status](https://travis-ci.org/Label305/Auja-Laravel.svg?branch=dev)](https://travis-ci.org/Label305/Auja-Laravel)
[![Coverage Status](https://coveralls.io/repos/Label305/Auja-Laravel/badge.png?branch=dev)](https://coveralls.io/r/Label305/Auja-Laravel?branch=dev)
[![Dependency Status](https://www.versioneye.com/user/projects/548991d6746eb53fa700032e/badge.svg?style=flat)](https://www.versioneye.com/user/projects/548991d6746eb53fa700032e)
[![Latest Stable Version](https://poser.pugx.org/label305/auja-laravel/v/stable.svg)](https://packagist.org/packages/label305/auja-laravel)
[![Total Downloads](https://poser.pugx.org/label305/auja-laravel/downloads.svg)](https://packagist.org/packages/label305/auja-laravel)
[![Latest Unstable Version](https://poser.pugx.org/label305/auja-laravel/v/unstable.svg)](https://packagist.org/packages/label305/auja-laravel)

Auja is an easy-to-use, easy-to-implement admin interface. It provides an easy and intuitive way for you to view and manipulate your data, so you can focus on more important matters. Auja is designed to be both user-friendly _and_ developer-friendly by providing you with tools to setup your admin interface in a couple of minutes.

The [Auja javascript frontend](https://github.com/Label305/Auja) provides the graphical user interface. To determine its content, it relies on a JSON web-service you implement. [Auja-PHP](https://github.com/Label305/Auja-PHP) in turn, provides an Object Oriented approach to provide these JSON messages from a PHP application. In this repository you will find a Laravel implementation which you can use to setup Auja for your Laravel project.

![Auja Screenshot](https://label305.github.io/Auja/images/auja-animated.gif)

Related repositories
-----------

  - [**Auja**](https://github.com/Label305/Auja) - The frontend JavaScript GUI
  - [**Auja PHP Development Kit**](https://github.com/Label305/Auja-PHP) - A web service development kit for communicating with the Auja javascript frontend

Installation and Setup
-------

Because Auja uses both a library for the PHP backand and a library for [the JavaScript frontend](https://github.com/Label305/Auja) there a few more steps required to install Auja that you may be used to with other libraries.

1.  [Setup Laravel to work with Bower and Gulp](#setup-laravel-to-work-with-bower-and-gulp), if you haven't already. _This is done by default in Laravel 5, but you still need to install [bower](http://bower.io/) and [gulp](http://gulpjs.com/) globaly with [npm](http://nodejs.org/)._

2.  Modify the `gulpfile.js` so all Auja's assets and scripts are placed in the correct folders when running `gulp`.

    ```js
    var elixir = require('laravel-elixir');

    elixir(function(mix) {
        mix
        // .sass('app.scss') // included in Laravel 5 but not manditory
        .publish('auja/auja.min.js', 'public/js/vendor/auja.js')
        .publish('auja/auja.css', 'public/css/vendor/auja.css')
        .publish('trumbowyg/dist/ui/trumbowyg.min.css', 'public/css/vendor/trumbowyg.css')
        .publish('Ionicons/css/ionicons.min.css', 'public/css/vendor/ionicons.css')
        .publish('Ionicons/fonts/ionicons.ttf', 'public/css/fonts/ionicons.ttf')
        .publish('Ionicons/fonts/ionicons.woff', 'public/css/fonts/ionicons.woff')
        .publish('animate.css/animate.min.css', 'public/css/vendor/animate.css');
    });
    ```

3.  Install the Auja javascript files through bower. When installing through bower the `gulp` command is automaticly called by bower.

    ```shell
    $ bower install auja
    ```

4.  Install the Auja Laravel library through composer. The library depends on [Auja-PHP](https://github.com/Label305/Auja-PHP) which is a API wrapper for communicating with the javascript frontend.

    ```shell
    $ composer require label305/auja-laravel:dev-dev
    ```

6.  Publish the Auja config file and main view

    ```shell
    $ php artisan config:publish label305/auja-laravel
    $ php artisan view:publish label305/auja-laravel
    ```

7.  Add the `AdminServiceProvider`, `AujaFacade` and `AujaRouteFacade` to your `app.php` config file.

    ```php
    'providers' => [
        ...
        'YourApp\Providers\AdminServiceProvider',
    ],
    'aliases' => [
        ...
        'Auja' => 'Label305\AujaLaravel\Facade\AujaFacade',
        'AujaRoute' => 'Label305\AujaLaravel\Facade\AujaRouteFacade',
    ],
    ```

8.  Customize the `auja-laravel/config.php` file and specify the models you wish to be included in the Admin interface.

9.  Create a `app/controllers/Admin/ClubsController.php` or `app/Http/Controllers/Admin/ClubsController.php`, to manage the `Clubs` in your admin interface. You can do this for all your models. Here is an example:

    ```php
    <?php namespace YourApp\Http\Controllers\Admin;

    use Illuminate\Routing\Controller;

    class ClubsController extends Controller {

        public function index()
        {
            if (Input::has('q')) {
                $items = Club::where('title', 'LIKE', sprintf('%%%s%%', Input::get('q')))->simplePaginate(10);
            } else {
                $items = Club::simplePaginate(10);
            }

            $linkTarget = urldecode(URL::route(AujaRoute::getEditName('Club'), '%d'));

            return Response::json(
                Auja::itemsFor($this, $items, $linkTarget)
            );
        }

        public function store()
        {
            Club::create(Input::all());
            return Response::json(
                new Message()
            );
        }

        public function update($id)
        {
            $page = Club::find($id);
            $page->fill(Input::all());
            $page->save();

            return Response::json(
                new Message()
            );
        }

        public function delete($id)
        {
            $page = Club::find($id);
            $page->delete($id);

            return Response::json(
                new Message()
            );
        }
    }
    ```

10.  Now setup the routes for the administration panel.

    ```php
    AujaRoute::group(['before'=> 'auth'], function() {

        AujaRoute::resource('Club', 'YourApp\Http\Controllers\Admin\ClubsController');
        AujaRoute::resource('Team', 'YourApp\Http\Controllers\Admin\TeamsController');
    });
    ```

11.  _Tip:_ add the following lines to your `.gitignore`:

    ```
    node_modules
    /public/css/vendor
    /public/js/vendor
    ```


Setup Laravel to work with Bower and Gulp
--------

1.  Make sure [node.js and npm](http://nodejs.org/) are installed.

2.  Intall [bower](http://bower.io/) and [gulp](http://gulpjs.com/).

    ```shell
    $ npm install -g bower gulp
    ```

3.  Make sure you have setup the `.bowerrc` file for Laravel and gulp. _By the way these are the Laravel 5 defaults but will work on other versions as well._

    ```json
    {
        "directory": "vendor/bower_components",
        "scripts": {
            "postinstall": "gulp publish"
        }
    }
    ```

4.  Install the javascript dependencies that are included in Laravel 5 by default. If you work with another version add the `package.json` file to your projects root.

    ```json
    {
        "devDependencies": {
            "gulp": "^3.8.8",
            "laravel-elixir": "*"
        }
    }
    ```

    And run:

    ```shell
    $ npm install
    ```

5.  Setup the `gulpfile.js` in your projects root.

    ```js
    var elixir = require('laravel-elixir');

    elixir(function(mix) {
        // mix.sass('app.scss'); // included in Laravel 5 but not manditory
    });
    ```

6.  Now you're all setup. Laravel 5 also includes a `bower.json` file but placing this file is not mandatory for other versions.

    ```json
    {
        "name": "Laravel Application",
        "dependencies": {
            "bootstrap-sass-official": "~3.3.1",
            "font-awesome": "~4.2.0",
            "auja": "~3.0.0"
        }
    }
    ```

Custom Model Configurations
------

You can provide your own subclasses of `ModelConfig` to the Auja config file in the `models` key. This way you can customize `icons` for example.

1. Create a new class that extends `ModelConfig`, for example `UserConfig`.

    ```php
    class UserConfig extends ModelConfig {

        public function getModelClass()
        {
            return 'User';
        }

        public function getIcon()
        {
            return Icons::ion_ios7_person;
        }

        public function isSearchable()
        {
            return false;
        }
    }
    ```

2. Modify the `auja-laravel/config.php` file to reference this new class.

    ```php
    'models' => [
        ...
        'YourApp\Admin\Configurations\UserConfig'
    ],
    ```

Custom Auja Support Controller
--------

By default Auja provides a default support controller to handle authentication and providing the main interface manifest. If you want your own AujaSupportController to handle authentication you can implement your own. Create a `YourSupportController.php`. This file contains information on what kind of information the admin area should contain and how administrators authenticate.

1. Set `route` to `null` in the configucation and create your own route to the controller.

    ```php
    AujaRoute::group(['prefix' => 'admin'], function() {
        AujaRoute::support('YourApp\Http\Controllers\Admin\YourSupportController');
    });

    AujaRoute::group(['before'=> 'auth', 'prefix' => 'admin'], function() {
        AujaRoute::resource('Club', 'YourApp\Http\Controllers\Admin\ClubsController');
        AujaRoute::resource('Team', 'YourApp\Http\Controllers\Admin\TeamsController');
    });
    ```

2. Create the `YourSupportController.php` file and make sure it implements `AujaSupportControllerInterface`.
    ```php
    <?php namespace YourApp\Http\Controllers\Admin;

    use Illuminate\Routing\Controller;
    use Label305\Auja\Shared\Message;

    class YourSupportController extends Controller implements AujaSupportControllerInterface {

        public function index()
        {
            return View::make('auja-laravel::admin/index');
        }

        public function manifest()
        {
            $username = Auth::user() == null ? null : Auth::user()->name;
            $authenticationForm = Auja::authenticationForm('Welcome Administrator!', 'admin/login');

            $main = Auja::main(
                'Your Awesome App',
                Auth::check(),
                $username,
                'admin/logout',
                $authenticationForm
            );

            $main->setColor(Main::COLOR_MAIN, '#00FF00');
            $main->setColor(Main::COLOR_ALERT, '#00FF00');
            $main->setColor(Main::COLOR_SECONDARY, '#666666');

            return Response::json($main);
        }

        public function login()
        {
            Auth::attempt(['email' => Input::get('email'), 'password' => Input::get('password')]);

            $message = new Message();
            $message->setAuthenticated(Auth::check());
            return Response::json($message);
        }

        public function logout()
        {
            Auth::logout();
            return Redirect::to('admin');
        }
    }
    ```

License
---------

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
