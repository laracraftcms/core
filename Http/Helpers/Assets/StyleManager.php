<?php namespace Laracraft\Core\Http\Helpers\Assets;

class StyleManager extends AssetManager
{

    protected function printItem($name, $url, $attributes = [])
    {
        unset($attributes['href']);
        $attributes = array_merge([
            'href' => $url,
            'title' => $name,
            'rel' => 'stylesheet',
            'type' => 'text/css'
        ], $attributes);
        return "" . '<link ' . htmlAttr($attributes) . '>' . "\n";
    }
}