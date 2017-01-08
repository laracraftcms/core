<?php namespace Laracraft\Core\Entities\Traits;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Laracraft\Core\Entities\Observers\TrackUpdateObserver;

/**
 * Trait to automatically populate `updated_by` appropriately
 * when the model is saved.
 *
 * @see TrackCreateObserver
 *
 */
trait TrackUpdate {

    protected $updated_by_column;

    /**
     * Boot ths trait and add observers
     */
    public static function bootTrackUpdate()
    {
        static::observe(new TrackUpdateObserver);
    }

    /**
     * Get the User that this model was last updated by
     *
     * @return mixed
     */
    public function updatedBy()
    {
        return $this->belongsTo(config('auth.providers.users.model'),$this->getUpdatedByColumn());
    }


    /**
     * Get the "updated_by" column.
     *
     * @return string
     */
    public function getUpdatedByColumn()
    {
        if(!isset($this->updated_by_column)){
            $this->updated_by_column = 'updated_by';
        }
        return $this->updated_by_column;
    }

    /**
     * Get the fully qualified "updated_by" column.
     *
     * @return string
     */
    public function getQualifiedUpdatedByColumn()
    {
        return $this->getTable().'.'.$this->getUpdatedByColumn();
    }

    /**
     * Scope query to those models updated last by a User
     *
     * @param $query
	 * @param Authenticatable|Model $user
     * @return mixed
     */
    public function scopeUpdatedBy($query,$user)
    {
		$authModel = config('auth.providers.users.model');
		if($user instanceof $authModel){
			$user = $user->getAuthIdentifier();
		}elseif($user instanceof Model){
			$user = $user->getKey();
		}
        return $query->where($this->getUpdatedByColumn(),$user);
    }

}