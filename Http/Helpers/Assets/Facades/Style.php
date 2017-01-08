<?php
namespace Laracraft\Core\Http\Helpers\Assets\Facades;

use Illuminate\Support\Facades\Facade;

class Style extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'StyleManager';
    }


    /**
     * Handle dynamic, static calls to the object.
     *
     * @param  string $method
     * @param  array $args
     * @return mixed
     */
    public static function __callStatic($method, $args)
    {
        $instance = static::getFacadeRoot();

        switch (count($args)) {
            case 0:
                if (method_exists($instance, $method)) {
                    return $instance->$method();
                } else {
                    return $instance->$method;
                }
            case 1:
                return $instance->$method($args[0]);

            case 2:
                return $instance->$method($args[0], $args[1]);

            case 3:
                return $instance->$method($args[0], $args[1], $args[2]);

            case 4:
                return $instance->$method($args[0], $args[1], $args[2], $args[3]);

            default:
                return call_user_func_array([$instance, $method], $args);
        }
    }

    public function __toString()
    {
        return (string)static::getFacadeRoot()->getAssets();
    }
}