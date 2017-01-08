<?php

namespace Laracraft\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laracraft\Core\Entities\Helpers\UrlFormatter;
use Laracraft\Core\Entities\Url;
use Laracraft\Core\Repositories\Contracts\UrlRepositoryContract;

class CoreController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('core::cp.dashboard');
    }

	/**
	 * Display a listing of the resource.
	 * @return Response
	 */
	public function configure()
	{
		return view('core::cp.configure.index');
	}

	public function route($url, UrlRepositoryContract $url_repository){

		\Debugbar::startMeasure('core:route');
		if($url !== '/'){
			$url  = '/'.$url;
		}
		$hash = md5($url);
		$url = $url_repository->tags(['url:'.$hash])->findOrFailByField('hash',$hash);
		$routables = config('laracraft-core.routable',[]);
		if(array_key_exists($url->routable_type,$routables)){
			list($class, $method) = explode('@', $routables[$url->routable_type]);
			\Debugbar::stopMeasure('core:route');
			return call_user_func(array( app($class) , $method ), $url->routable);
		}else{
			abort(404);
		}
		return false;
	}

}
