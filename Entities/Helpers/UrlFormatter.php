<?php
namespace Laracraft\Core\Entities\Helpers;

use Twig;
use Twig_Loader_Array;

class UrlFormatter{

	const HOME_PLACEHOLDER = '__HOME__';

	public function format($key, $format, $params){

		if($format == self::HOME_PLACEHOLDER){
			return '/';
		}

		if(!is_array($params) || !isset($params['entity'])){
			$params = ['entity'=>$params ];
		}

		$originalLoader = Twig::getLoader();

		Twig::setLoader(new Twig_Loader_Array([
			$key => $this->prepareFormat($format)
		]));

		$formatted =  $this->postFormat(Twig::render($key, $params));

		Twig::setLoader($originalLoader);

		return $formatted;
	}

	protected function prepareFormat($format){

		return '/'. preg_replace('/{([^}]*)}/i','{{ entity.$1 }}',$format);

	}

	protected function postFormat($url){
		return trim(rtrim(trim(preg_replace('~[[:cntrl:]]~', '',$url)), '/\\'));
	}
}