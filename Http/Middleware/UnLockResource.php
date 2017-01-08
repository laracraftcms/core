<?php

namespace Laracraft\Core\Http\Middleware;

use Closure;
use Request;
use Laracraft\Core\Entities\EditLock;

class UnLockResource
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
		if(Request::has('and_release')){
			EditLock::release(Request::get('and_release'));
		}
        return $next($request);
    }
}
