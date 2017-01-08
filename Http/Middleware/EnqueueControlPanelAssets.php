<?php

namespace Laracraft\Core\Http\Middleware;

use Closure;
use Style;
use Script;

class EnqueueControlPanelAssets
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        Style::enqueue('laracraft-core', 'modules/core/css/laracraft.css');
        Script::enqueue('laracraft-core', 'modules/core/js/laracraft.js');
        Script::enqueue('laracraft-routes', 'modules/core/js/routes.js');

		Script::setVar('settings',null);
		Script::setVar('cp_root',config('laracraft-core.cp_root'),'laracraft.settings');
		Script::setVar('lock_increment',intval(config('laracraft-core.lock_increment'))*1000,'laracraft.settings');

		Script::setVar('forms',trans('core::forms'),'laracraft.lang');
		
		$datatables = Script::optimizeValueForJavaScript(config('laracraft-core.datatables'));

		Script::ready('datatables-defaults',"$.extend( $.fn.dataTable.defaults, $datatables );");

        return $next($request);
    }
}
