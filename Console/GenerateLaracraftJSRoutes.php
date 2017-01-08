<?php

namespace Laracraft\Core\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Lord\Laroute\Routes\Collection;

class GenerateLaracraftJSRoutes extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'laracraft:js_routes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates laracraft routes for in javascript';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
		$routes  = new Collection(app('router')->getRoutes(),'laracraft','laracraft.routes');
		$generator  = app('Lord\Laroute\Generators\GeneratorInterface');


		$namespace  = 'laracraft.routes';
		$routes     = json_encode(Arr::where(json_decode($routes->toJSON(),true),function($route){ return substr($route['name'],0,9) == config('laracraft-core.cp_root'); }));
		$absolute   = true;
		$rootUrl    = config('app.url', '');
		$prefix		= '';

		$conf =  compact('namespace', 'routes', 'absolute', 'rootUrl', 'prefix');
		$generator->compile('vendor/lord/laroute/src/templates/laroute.js', $conf, public_path('modules/core/js/routes.js'));
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
        ];
    }
}
