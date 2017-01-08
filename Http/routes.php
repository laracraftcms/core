<?php
use Laracraft\Core\Repositories\Contracts\SectionRepositoryContract;
use Laracraft\Core\Entities\EntryType;
use Laracraft\Core\Repositories\Contracts\EntryTypeRepositoryContract;
use Laracraft\Core\Repositories\Contracts\FieldLayoutRepositoryContract;
use Laracraft\Core\Entities\Field;
use Laracraft\Core\Repositories\Contracts\FieldRepositoryContract;
use Laracraft\Core\Repositories\Contracts\FieldGroupRepositoryContract;

Route::middleware('cp_assets' , \Laracraft\Core\Http\Middleware\EnqueueControlPanelAssets::class);
Route::middleware('lock_resource' , \Laracraft\Core\Http\Middleware\LockResource::class);
Route::middleware('unlock_resource', \Laracraft\Core\Http\Middleware\UnLockResource::class);

Route::middlewareGroup('laracraft-cp',[
	'web',
	'auth:web',
	'cp_assets',
	'unlock_resource'
]);

Route::bind('section', function($key, $route) {
	$repository = app(SectionRepositoryContract::class);
	$routeKey = $repository->getRouteKeyName();
	return $repository->tags(EntryType::class)->with('entryTypes')->findOrFailByField($routeKey, $key);
});

Route::bind('entry_type', function($key, $route) {
	$repository = app(EntryTypeRepositoryContract::class);
	$routeKey = $repository->getRouteKeyName();
	return $repository->findOrFailByField($routeKey, $key);
});

Route::bind('field_layout', function($key, $route) {
	$repository = app(FieldLayoutRepositoryContract::class);
	$routeKey = $repository->getRouteKeyName();
	return $repository->tags(Field::class)->with(['tabs', 'fields'])->findOrFailByField($routeKey, $key);
});

Route::bind('field_group', function($key, $route) {
	$repository = app(FieldGroupRepositoryContract::class);
	$routeKey = $repository->getRouteKeyName();
	return $repository->tags(Field::class)->with(['fields'])->findOrFailByField($routeKey, $key);
});

Route::bind('field', function($key, $route) {
	$repository = app(FieldRepositoryContract::class);
	$routeKey = $repository->getRouteKeyName();
	return $repository->findOrFailByField($routeKey, $key);
});

Route::group(['middleware' => ['laracraft-cp'], 'prefix' => CP_ROOT, 'namespace' => 'Laracraft\Core\Http\Controllers', 'priority' => 51 ], function () {
	Route::get('/', [
		'as' => CP_ROOT .'.index',
		'uses' => 'CoreController@index'
	]);

	Route::group(['prefix' => 'entries'],function(){
		Route::get('/',
				   [
					   'as' => CP_ROOT . '.entry.index',
					   'uses' => 'EntryController@index'
				   ]);
		Route::get('/{section}',
				   [
					   'as' => CP_ROOT . '.section.entry.index',
					   'uses' => 'EntryController@forSection'
				   ]);

		Route::get('/{section}/create',
				   [
					   'as' => CP_ROOT . '.section.entry.create',
					   'uses' => 'EntryController@create'
				   ]);

		Route::get('/{section}/{entry_type}',
				   [
					   'as' => CP_ROOT . '.section.entry_type.entry.index',
					   'uses' => 'EntryController@forSectionEntryType'
				   ]);

		Route::post('/{section}/{entry_type}',
					[
						'as' => CP_ROOT . '.section.entry_type.entry.store',
						'uses' => 'EntryController@store'
					]);

		Route::get('/{section}/{entry_type}/{entry}',
				   [
					   'as' => CP_ROOT . '.section.entry_type.entry.edit',
					   'uses' => 'EntryController@edit'
				   ]);

		Route::get('/{section}/{entry_type}/create',
				   [
					   'as' => CP_ROOT . '.section.entry_type.entry.create',
					   'uses' => 'EntryController@createType'
				   ]);


		Route::match(['put','patch'],'/{section}/{entry_type}/{entry}',
					[
						'as' => CP_ROOT . '.section.entry_type.entry.update',
						'uses' => 'EntryController@update'
					]);

	});

	Route::group(['prefix' => 'configure'], function () {
		Route::get('/',
				   [
					   'as' => CP_COFIGURE . '.index',
					   'uses' => 'CoreController@configure'
				   ]);

		Route::resource('section', 'SectionController', ['except' => ['show'],'names' => [
			'store' => CP_COFIGURE .'.section.store',
			'index' => CP_COFIGURE .'.section.index',
			'create' => CP_COFIGURE .'.section.create',
			'update' => CP_COFIGURE .'.section.update',
			'destroy' => CP_COFIGURE .'.section.destroy',
			'edit' => CP_COFIGURE .'.section.edit'
		]]);

		Route::get('section/{section}/entry_type',[
			'as' => CP_ROOT . '.configure.section.entry_type.index',
			'uses' => 'SectionController@entryTypes'
		]);

		Route::patch('section/{section}/entry_type',[
			'as' => CP_ROOT . '.configure.section.entry_type.update',
			'uses' => 'SectionController@updateEntryTypes'
		]);

		Route::resource('entry_type','EntryTypeController',['except' => ['show'],'names' => [
			'store' => CP_COFIGURE .'.entry_type.store',
			'index' => CP_COFIGURE .'.entry_type.index',
			'create' => CP_COFIGURE .'.entry_type.create',
			'update' => CP_COFIGURE .'.entry_type.update',
			'destroy' => CP_COFIGURE .'.entry_type.destroy',
			'edit' => CP_COFIGURE .'.entry_type.edit'
		]]);

		Route::resource('field_group','FieldGroupController',['except' => ['show'],'names' => [
			'store' => CP_COFIGURE .'.field_group.store',
			'index' => CP_COFIGURE .'.field_group.index',
			'create' => CP_COFIGURE .'.field_group.create',
			'update' => CP_COFIGURE .'.field_group.update',
			'destroy' => CP_COFIGURE .'.field_group.destroy',
			'edit' => CP_COFIGURE .'.field_group.edit'
		]]);

		Route::resource('field_layout','FieldLayoutController', ['except' => ['show'],'names' => [
			'store' => CP_COFIGURE .'.field_layout.store',
			'index' => CP_COFIGURE .'.field_layout.index',
			'create' => CP_COFIGURE .'.field_layout.create',
			'update' => CP_COFIGURE .'.field_layout.update',
			'destroy' => CP_COFIGURE .'.field_layout.destroy',
			'edit' => CP_COFIGURE .'.field_layout.edit'
		]]);

		Route::resource('field','FieldController', ['except' => ['show'],'names' => [
			'store' => CP_COFIGURE .'.field.store',
			'index' => CP_COFIGURE .'.field.index',
			'create' => CP_COFIGURE .'.field.create',
			'update' => CP_COFIGURE .'.field.update',
			'destroy' => CP_COFIGURE .'.field.destroy',
			'edit' => CP_COFIGURE .'.field.edit'
		]]);

		Route::get('field/settings/{type}/{field}',[
			'as' => CP_COFIGURE . '.field.settings.for',
			'uses' =>'FieldController@settingsHtml'
		]);
		Route::get('field/settings/{type}',[
			'as' => CP_COFIGURE . '.field.settings',
			'uses' =>'FieldController@settingsHtml'
		]);
	});

	Route::group(['prefix' => 'lock'],function(){
		Route::get('{lock}',[
			'as' => CP_ROOT . '.lock.maintain',
			'uses' => 'EditLockController@maintain'
		]);
		Route::delete('{lock}',[
			'as' => CP_ROOT . '.lock.release',
			'uses' => 'EditLockController@release'
		]);
		Route::get('{lock}/takeover',[
			'as' => CP_ROOT . '.lock.takeover',
			'uses' => 'EditLockController@takeover'
		]);
		Route::match(['get', 'post'],'release',[
			'as' => CP_ROOT . '.lock.release_all',
			'uses' => 'EditLockController@releaseAll'
		]);
	});

});

Route::any( '{laracraft_route}', [
	'uses' => 'Laracraft\Core\Http\Controllers\CoreController@route',
	'as' => CP_ROOT . '.router'
] )->where('laracraft_route', '(.*)')->setPriority(0);


