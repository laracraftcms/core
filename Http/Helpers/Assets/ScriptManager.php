<?php namespace Laracraft\Core\Http\Helpers\Assets;

use JsonSerializable;
use stdClass;
use Exception;

class ScriptManager extends AssetManager
{

    protected $ready = [];

    protected $vars = [];

    protected $default_namespace = 'footer';

    protected $default_var_namespace = 'laracraft';

    /**
     * All transformable types.
     *
     * @var array
     */
    protected $var_types = [
        'String',
        'Array',
        'Object',
        'Numeric',
        'Boolean',
        'Null'
    ];

    protected function printItem($name, $url, $attributes = [])
    {
        unset($attributes['src']);
        $attributes = array_merge([
            'src' => $url,
            'title' => $name,
            'type' => 'text/javascript'
        ], $attributes);
        return "" . '<script ' . htmlAttr($attributes) . '></script>' . "\n";

    }

    public function ready($name,$js){
        $this->ready[$name] = $js;
    }

    public function unready($name){
        unset($this->ready[$name]);
    }

    public function isReadied($name){
        return array_key_exists($name,$this->ready);
    }

    public function getReadied(){

        return $this->ready;
    }

    public function printReadied($name=null){

        ob_start();
        ?>
        <script type="text/javascript" title="onReady">
            document.addEventListener('DOMContentLoaded',function(){
            <?php
            foreach($this->ready as $js){
                echo $js . "\r\n";
            }
            ?>
            });
        </script>
        <?php
        return ob_get_clean();

    }

    public function setVar($name,$value,$namespace=false){

        if (empty($namespace)) {
            $namespace = $this->default_var_namespace;
        }

        if (!isset($this->vars[$namespace])) {
            $this->vars[$namespace] = [];
        }

        $this->vars[$namespace][$name] = $this->optimizeValueForJavaScript($value);
    }

    public function unsetVar($name,$namespace=false)
    {

        if (empty($namespace)) {
            $namespace = $this->default_var_namespace;
        }
        if (isset($this->vars[$namespace])) {
            unset($this->vars[$namespace][$name]);
        }

    }

    public function getVars($namespace = false)
    {

        if (!empty($namespace)) {
            return $this->vars[$namespace];
        }

        return $this->vars;

    }

    public function printVars()
    {

        ob_start();
        ?>
        <script type="text/javascript" title="laracraftVars">
            <?php
            foreach($this->vars as $namespace => $values){
            ?>
            window.<?php echo $namespace; ?> = window.<?php echo $namespace; ?> || {};
            <?php
            foreach($values as $name => $value){
                echo 'window.'.$namespace . '.' . $name . ' = ' . $value . ";\n";
            }
            }
            ?>
        </script>
        <?php
        return ob_get_clean();

    }

    /**
     * Format a value for JavaScript.
     *
     * @param  string $value
     * @throws Exception
     * @return string
     */
    public function optimizeValueForJavaScript($value)
    {
        // For every transformable type, let's see if
        // it needs to be transformed for JS-use.
        foreach ($this->var_types as $transformer) {
            $js = $this->{"transform{$transformer}"}($value);
            if ( ! is_null($js)) {
                return $js;
            }
        }
    }

    /**
     * Transform a string.
     *
     * @param  string $value
     * @return string
     */
    protected function transformString($value)
    {
        if (is_string($value)) {
            return "'{$this->escape($value)}'";
        }
    }

    /**
     * Transform an array.
     *
     * @param  array $value
     * @return string
     */
    protected function transformArray($value)
    {
        if (is_array($value)) {
            return json_encode($value);
        }
    }

    /**
     * Transform a numeric value.
     *
     * @param  mixed $value
     * @return mixed
     */
    protected function transformNumeric($value)
    {
        if (is_numeric($value)) {
            return $value;
        }
    }

    /**
     * Transform a boolean.
     *
     * @param  boolean $value
     * @return string
     */
    protected function transformBoolean($value)
    {
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }
    }

    /**
     * @param  object $value
     * @return string
     * @throws Exception
     */
    protected function transformObject($value)
    {
        if ( ! is_object($value)) {
            return;
        }
        if ($value instanceof JsonSerializable || $value instanceof StdClass) {
            return json_encode($value);
        }
        // If a toJson() method exists, we'll assume that
        // the object can cast itself automatically.
        if (method_exists($value, 'toJson')) {
            return $value;
        }
        // Otherwise, if the object doesn't even have a
        // __toString() method, we can't proceed.
        if ( ! method_exists($value, '__toString')) {
            throw new Exception('Cannot transform this object to JavaScript.');
        }
        return "'{$value}'";
    }

    /**
     * Transform "null."
     *
     * @param  mixed $value
     * @return string
     */
    protected function transformNull($value)
    {
        if (is_null($value)) {
            return 'null';
        }
    }

    /**
     * Escape any single quotes.
     *
     * @param  string $value
     * @return string
     */
    protected function escape($value)
    {
        return str_replace(["\\", "'"], ["\\\\", "\'"], $value);
    }


}