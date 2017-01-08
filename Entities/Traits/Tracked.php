<?php namespace Laracraft\Core\Entities\Traits;

/**
 * Trait to automatically populate `updated_by` and `created_by` appropriately
 * when the model is saved.
 *
 * @see TrackCreate
 * @see TrackUpdate
 *
 */
trait Tracked {

    use TrackCreate;
    use TrackUpdate;

    /**
     * Boot ths trait and add observers
     */
    public static function bootTracked(){

        static::bootTrackCreate();
        static::bootTrackUpdate();

    }

}