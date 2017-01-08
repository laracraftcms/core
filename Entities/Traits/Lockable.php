<?php namespace Laracraft\Core\Entities\Traits;

use Laracraft\Core\Entities\EditLock;
use Carbon\Carbon;
use Auth;
use Laracraft\Core\Entities\Observers\LockableObserver;
use Laracraft\Core\Exceptions\ModelLockedException;
use Illuminate\Validation\UnauthorizedException;

/**
 * Trait Lockable
 *
 * Provide ability to allow a use to lock a record whilst editing
 *
 * Opening a record to edit will lock that record from being edited by other users, this lock will expire after a
 * configured period.
 * This prevents simultaneous editing and the potential problems associated with race conditions and overwrites etc.
 *
 * @package Laracraft\Core\Entities\Traits
 */
trait Lockable
{

    protected $lock_expiry;

    public static function bootLockable()
    {

        static::observe(new LockableObserver());

    }

    public function getLockExpiry(){
        if (!isset($this->lock_expiry)) {
            $this->lock_expiry = config('laracraft-core.default_lock_expiry');
        }
        return $this->lock_expiry;
    }

    public function editLock()
    {
        return $this->morphOne(EditLock::class, 'lockable');
    }

    /**
     * Check if resource is locked
     *
     * @return bool
     */
    public function isLocked(){
        if(Auth::check()) {
            $lock = $this->editLock;

            if($lock) { // if there is a lock

                if (
                    $lock->created_by!= Auth::user()->getAuthIdentifier() && // and it is not owned by this user
                    $lock->expires_at->gt(Carbon::now()) // and its hasn't expired yet
                ) {
                    return true; //its locked!
                }
            }
            //otherwise its not!
            return false;
        }
        return true; //just in case the user is not logged in
    }

    /**
     * Obtain a lock for this resource or throw appropriate exception
     *
     * @return bool true on successful locking
     * @throws ModelLockedException|UnauthorizedException
     */
    public function getLock(){

        if(Auth::check()) {
            if (!$this->isLocked()) {
                $lock = $this->editLock ? $this->editLock : new EditLock();
                $lock->setExpiresAtAttribute(Carbon::now()->addSeconds($this->getLockExpiry()));
                $lock->created_by = Auth::user()->getAuthIdentifier();
                $this->editLock()->save($lock);
                return $lock;
            }else{
                $ex = new ModelLockedException();
                $ex->setModel($this);
                throw $ex;
            }
        }else{
            throw new UnauthorizedException();
        }

    }

    /**
     * Release the lock on this resource
     */
    public function releaseLock(){
        $this->editLock()->delete();
    }

    public static function releaseLocks(){
        EditLock::wasCreatedBy(Auth::user())->where('lockable_type',static::class)->delete();
    }


}