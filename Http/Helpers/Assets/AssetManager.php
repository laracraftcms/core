<?php namespace Laracraft\Core\Http\Helpers\Assets;

abstract class AssetManager
{

    // Store queued assets
    protected $assets = [];

    // tag attributes for assets
    protected $attributes = [];

    //default namespace for assets
    protected $default_namespace = 'default';

    // Add asset to queue
    public function enqueue($name, $path, $namespace = false, $attributes = [])
    {

        if (empty($namespace)) {
            $namespace = $this->default_namespace;
        }

        if (!isset($this->assets[$namespace])) {
            $this->assets[$namespace] = [];
        }

        try {
            //look for a versioned asset
            $this->assets[$namespace][$name] = asset(elixir($path, config('app.assetBuildPath','build')));
        } catch (\InvalidArgumentException $e) {
            $this->assets[$namespace][$name] = asset($path);
        }

        $this->setAttributes($name, $attributes, $namespace);

        return $this->assets[$namespace][$name];
    }

    // Remove asset from queue by name
    public function dequeue($name, $namespace = false)
    {

        if (empty($namespace)) {
            $namespace = $this->default_namespace;
        }
        if (isset($this->assets[$namespace])) {
            unset($this->assets[$namespace][$name]);
        }
        if (isset($this->attributes[$namespace])) {
            unset($this->attributes[$namespace][$name]);
        }

    }

    // Get queued assets
    public function getQueued($namespace = false)
    {

        if (!empty($namespace)) {
            return $this->assets[$namespace];
        }

        return $this->assets;
    }

    // Get queued assets
    public function isQueued($name, $namespace = false)
    {

        if (!empty($namespace)) {
            return (isset($this->assets[$namespace]) && isset($this->assets[$namespace][$name]));
        } else {
            foreach ($this->assets as $namespace => $assets) {
                if (isset($this->assets[$namespace][$name])) {
                    return $namespace;
                }
            }
        }

        return false;
    }

    public function setAttributes($name, $attrs, $namespace = false)
    {

        if (empty($namespace)) {
            $namespace = $this->default_namespace;
        }

        if (!isset($this->attributes[$namespace])) {
            $this->attributes[$namespace] = [];
        }

        $this->attributes[$namespace][$name] = $attrs;
    }

    public function getAttributes($name, $namespace = false)
    {
        if (empty($namespace)) {
            $namespace = $this->default_namespace;
        }

        if (isset($this->attributes[$namespace]) && isset($this->attributes[$namespace][$name])) {
            return $this->attributes[$namespace][$name];
        }

        return [];
    }

    // Format single asset for html output
    abstract protected function printItem($name, $url, $attributes = []);


    public function __tostring()
    {
        return $this->getAssets($this->default_namespace);

    }

    public function __get($name)
    {

        if (array_key_exists($name, $this->assets)) {
            return $this->getAssets($name);
        } else {
            return $this->getAsset($name);
        }
    }

    public function getAssets($namespace = false)
    {

        if (empty($namespace)) {
            $namespace = $this->default_namespace;
        }

        if (isset($this->assets[$namespace])) {

            $output = '';
            foreach ($this->assets[$namespace] as $name => $url) {
                $output .= $this->getAsset($name, $namespace);
            }

            return $output;
        }

        return false;
    }

    public function getAsset($name, $namespace = false)
    {

        if (empty($namespace)) {

            foreach ($this->assets as $namespace => $assets) {
                if (isset($assets[$name])) {
                    return $this->getAsset($name, $namespace);
                }
            }
        } else {
            if (isset($this->assets[$namespace]) && isset($this->assets[$namespace][$name])) {
                $attrs = $this->getAttributes($name, $namespace);
                return $this->printItem($name, $this->assets[$namespace][$name], $attrs);
            }
        }

        return false;
    }

}

