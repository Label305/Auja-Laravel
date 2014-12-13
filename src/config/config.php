<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Administration Interface Title
	|--------------------------------------------------------------------------
	|
	| In the upper left corner the admin interface will display a title.
	|
	*/

	'title' => 'Your App Admin',

	/*
	|--------------------------------------------------------------------------
	| Color
	|--------------------------------------------------------------------------
	|
	| The interface has a color property. You could use this color to
	| make environments easy to differentiate for administrators. Use a CSS
	| color like: 'red' or '#232323' or 'rgba(0,0,255,0)'.
	|
	*/

	'color' => [
        'main' => '#1EBAB8',
        'secondary' => '#666666',
        'alert' => '#1EBAB8',
    ],

	/*
	|--------------------------------------------------------------------------
	| Default Database Provider
	|--------------------------------------------------------------------------
	|
	| Specify the database type Auja should use.
	| Currently supported: 'mysql'
	|
	*/

	'database' => 'mysql',

	/*
	|--------------------------------------------------------------------------
	| Admin routes prefix
	|--------------------------------------------------------------------------
	|
	| The default route at which the admin interface is located.
	| For example: 'admin' will make the interface available at: '/admin'.
	| If set to 'null' the routes will not be set and you can define your own
	| routes.
	|
	*/

	'route' => 'admin',

	/*
	|--------------------------------------------------------------------------
	| Available Models
	|--------------------------------------------------------------------------
	|
	| Specify the models that you wish to be available in the admin
	| interface. Each model can have a separate config class.
	|
	| For example: 'YourApp\Models\User'
	|
	*/

	'models' => [

	],

	/*
	|--------------------------------------------------------------------------
	| Custom Model Configurations
	|--------------------------------------------------------------------------
	|
	| Auja will autopopulate the interface for all models, however you can
	| customize a lot. To do so create ModelConfiguration classes that define
	| how each model should be presented by the interface.
	|
	| For example 'YourApp\Admin\Config\UsersConfig'
	|
	*/

	'configurations' => [

	],

	/*
	|--------------------------------------------------------------------------
	| Uses Cache For Database Structure
	|--------------------------------------------------------------------------
	|
	| Auja will cache the database structure to speed up the loading of the
	| admin interface.
	|
	*/

	'cache' => true,
];