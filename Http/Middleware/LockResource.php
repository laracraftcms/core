<?php

namespace Laracraft\Core\Http\Middleware;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Script;

class LockResource
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $binding)
    {
        $model = $request->route()->parameters()[$binding];

        if($model instanceof Model && method_exists($model,'getLock')){
            $lock = $model->getLock();
            Script::ready("laracraft-lock-hearbeat-timer","window.laracraft.settings.lock_heartbeat_timer = setInterval(function(){window.laracraft.lock.heartbeat('"
                            . $lock->id . "')}, window.laracraft.settings.lock_increment);");
        }

        return $next($request);
    }
}
