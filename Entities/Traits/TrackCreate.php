<?php namespace Laracraft\Core\Entities\Traits;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Laracraft\Core\Entities\Observers\TrackCreateObserver;

/**
 * Trait to automatically populate `created_by` appropriately
 * when the model is saved.
 *
 * @see TrackCreateObserver
 *
 */
trait TrackCreate {

    protected $created_by_column;

    /**
     * Boot ths trait and add observers
     */
    public static function bootTrackCreate()
    {
        static::observe(new TrackCreateObserver);
    }

    /**
     * Get the User that this model was created by
     *
     * @return mixed
     */
    public function createdBy()
    {
        return $this->belongsTo(config('auth.providers.users.model'),$this->getCreatedByColumn());
    }


    /**
     * Get the "created_by" column.
     *
     * @return string
     */
    public function getCreatedByColumn()
    {
        if(!isset($this->created_by_column)){
            $this->created_by_column = 'created_by';
        }
        return $this->created_by_column;
    }

    /**
     * Get the fully qualified "created_by" column.
     *
     * @return string
     */
    public function getQualifiedCreatedByColumn()
    {
        return $this->getTable().'.'.$this->getCreatedByColumn();
    }

    /**
     * Scope query to those models created by a User
     *
     * @param $query
     * @param Authenticatable|Model $user
     * @return mixed
     */
    public function scopeWasCreatedBy($query, $user)
    {
        $authModel = config('auth.providers.users.model');
        if($user instanceof $authModel){
            $user = $user->getAuthIdentifier();
        }elseif($user instanceof Model){
			$user = $user->getKey();
		}
        return $query->where($this->getCreatedByColumn(),$user);
    }

}