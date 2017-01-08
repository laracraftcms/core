<?php

namespace Laracraft\Core\Entities;

use Alsofronie\Uuid\UuidModelTrait;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Laracraft\Core\Entities\Traits\TrackCreate;
use Gate;

class EditLock extends Model
{
    use TrackCreate;
	use UuidModelTrait;

    protected $table = 'edit_locks';

    public $timestamps = false;

    protected $dates = [
        'expires_at',
    ];

    public function lockable()
    {
        return $this->morphTo();
    }

    function setExpiresAtAttribute($expires)
    {
        $expires = $expires instanceof Carbon ? $expires : new Carbon($expires);
        $now = Carbon::now();

        if($expires < $now){
            $expires = $now->addSeconds(config('laracraft-core.default_lock_expiry'));
        }

        $this->attributes['expires_at'] = $expires;

    }

    public function incrementExpiry(){
        $increment = intval(config('laracraft-core.lock_increment'));
        $maxLock = Carbon::now();
        $maxLock->addSeconds((2 * $increment));

        if ($this->expires_at <= $maxLock) {
            $this->expires_at = $this->expires_at->addSeconds($increment);
            return true;
        }
        return false;
    }

    public static function release($lock){
        $class = static::class;
        $lock = ($lock instanceof $class) ? $lock : static::find($lock);
        if($lock && Gate::allows('release',$lock)){
            $lock->delete();
        }
    }

}
