<?php

/*
|--------------------------------------------------------------------------
| Register Namespaces And Routes
|--------------------------------------------------------------------------
|
| When a module starting, this file will executed automatically. This helps
| to register some namespaces like translator or view. Also this file
| will load the routes file for each module. You may also modify
| this file as you want.
|
*/

$config = app('config')->get('laracraft-core', []);

app('config')->set('laracraft-core', array_merge(require __DIR__ . '/Config/laracraft-core.php', $config));

define('CP_ROOT', config('laracraft-core.cp_root','laracraft'));
define('CP_COFIGURE', CP_ROOT  . '.configure');
require __DIR__ . '/Http/routes.php';
