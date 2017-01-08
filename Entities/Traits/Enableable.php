<?php namespace Laracraft\Core\Entities\Traits;

use Laracraft\Core\Entities\Scopes\EnabledScope;
use Laracraft\Core\Entities\Observers\EnableableObserver;

trait Enableable {

    protected $enabled_column;
    protected $isEnabling = false;
    protected $isDisabling = false;

	/**
	 * @return boolean
	 */
	public function isEnabling() {
		return $this->isEnabling;
	}

	/**
	 * @param boolean $isEnabling
	 */
	public function setEnabling($isEnabling) {
		$this->isEnabling = $isEnabling;
	}

	/**
	 * @return boolean
	 */
	public function isDisabling() {
		return $this->isDisabling;
	}

	/**
	 * @param boolean $isDisabling
	 */
	public function setDisabling($isDisabling) {
		$this->isDisabling = $isDisabling;
	}

    public static function bootEnableable(){

        static::observe(app(EnableableObserver::class));
        static::addGlobalScope(new EnabledScope());

    }

    /**
     * Get the "enabled" column.
     *
     * @return string
     */
    public function getEnabledColumn()
    {
        if(!isset($this->enabled_column)){
            $this->enabled_column = 'enabled';
        }
        return $this->enabled_column;
    }

    /**
     * Get the fully qualified "Enabled" column.
     *
     * @return string
     */
    public function getQualifiedEnabledColumn()
    {
        return $this->getTable().'.'.$this->getEnabledColumn();
    }

    function setEnabledAttribute($bool)
    {

        $this->attributes[$this->getEnabledColumn()] = (bool) $bool;

    }

    function getEnabledAttribute()
    {
        return (bool) array_key_exists($this->getEnabledColumn(),$this->attributes)
			? $this->attributes[$this->getEnabledColumn()]
			: true;
    }

    /**
     * Fire the namespaced enabling event.
     *
     */
    public function fireEnablingEvent()
    {
        return $this->fireModelEvent('enabling', true);
    }

    /**
     * Fire the namespaced enabled event.
     *
     */
    public function fireEnabledEvent()
    {
        return $this->fireModelEvent('enabled');
    }

    /**
     * Fire the namespaced disabling event.
     *
     */
    public function fireDisablingEvent()
    {
        return $this->fireModelEvent('disabling', true);
    }

    /**
     * Fire the namespaced enabled event.
     *
     */
    public function fireDisabledEvent()
    {
        return $this->fireModelEvent('disabled');
    }
}