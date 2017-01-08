<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Control Panel Root
	|--------------------------------------------------------------------------
	|
	| The url root for all laracraft control panel routes.
	| Will also effect route names.
	|
	*/

	'cp_root' => 'laracraft',

	/*
	|--------------------------------------------------------------------------
	| Default Section Has Urls
	|--------------------------------------------------------------------------
	|
	| The default Has Urls value for new Sections.
	|
	*/

	'default_has_urls' => true,

	/*
	|--------------------------------------------------------------------------
	| Default Lock Expiry
	|--------------------------------------------------------------------------
	|
	| The default period in seconds after which a record lock will expire.
	| Models must implement the Lockable trait and can override this default
	| by defining a protected $lock_expiry property.
	|
	*/

	'default_lock_expiry' => 120,

	/*
	|--------------------------------------------------------------------------
	| Lock Increment/Heartbeat
	|--------------------------------------------------------------------------
	|
	| The period in seconds by which to increment a locks expiry and
	| the interval at which a request to maintain a lock should be made.
	|
	*/

	'lock_increment' => 30,

	/*
	|--------------------------------------------------------------------------
	| Datatables Defaults
	|--------------------------------------------------------------------------
	|
	| The datatables defaults for us in the laracraft control panel.
	| Values will be converted to json and set as defaults so keep it simple,
	| functions wont work.
	|
	*/

	'datatables' => [

		'pageLength' => 25,

		'lengthMenu' => [25, 50, 100, 200],

	],

	'section_types' => [
		\Laracraft\Core\Entities\Section::SINGLE_TYPE,
		\Laracraft\Core\Entities\Section::CHANNEL_TYPE,
		\Laracraft\Core\Entities\Section::STRUCTURE_TYPE
	],

	'default_field_type' => \Laracraft\Core\Entities\Field::PLAINTEXT_TYPE,


	'field_types' => [
		\Laracraft\Core\Entities\Field::PLAINTEXT_TYPE => \Laracraft\Core\Entities\FieldTypes\PlainText::class,
		\Laracraft\Core\Entities\Field::BOOLEAN_TYPE => \Laracraft\Core\Entities\FieldTypes\Boolean::class
	],

	'routable' => [
		\Laracraft\Core\Entities\Entry::class => 'Laracraft\Core\Http\Controllers\EntryController@show'
	],
	/*
	|--------------------------------------------------------------------------
	| Laracraft Cache Settings
	|--------------------------------------------------------------------------
	|
	| Laracraft performance can be dramatically improved by using a fast cache.
	| The cache can be configured below.
	|
	*/

	'cache' => [

		'repository' => 'cache',
		'default_cache_minutes' => 120

	],

	'reserved_slugs' => [
		'laracraft'
	]

];
