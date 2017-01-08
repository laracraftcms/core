<?php

namespace Laracraft\Core\Policies;

use Laracraft\Core\Entities\EditLock;
use Illuminate\Contracts\Auth\Authenticatable;
use Gate;

class EditLockPolicy
{
    /**
     * Allow super admins to do everything!
     *
     * @param Authenticatable $user
     * @param $ability
     * @return boolean|null
     */
    public function before(Authenticatable $user, $ability)
    {
        if (method_exists($user,'isLaracraftSuperAdmin') && $user->isLaracraftSuperAdmin()) {
            return true;
        }

        return null;
    }

    /**
     * Check if user can maintain the lock
     *
     * @param Authenticatable $user
     * @param EditLock $lock
     * @return bool
     */
    public function maintain(Authenticatable $user, EditLock $lock){

            return $this->ownsLock($user,$lock);
    }

    /**
     * Check if user can release the lock
     *
     * @param Authenticatable $user
     * @param EditLock $lock
     * @return bool
     */
    public function release(Authenticatable $user, EditLock $lock){
        // user owns this lock or has permission to manage locks of this type or can manage locks globally.
        return $this->ownsLock($user,$lock) ||
                Gate::allows('Laracraft.manageLocks', new $lock->lockable_type);
    }

    /**
     * Check if user can takeover the lock
     *
     * @param Authenticatable $user
     * @param EditLock $lock
     * @return bool
     */
    public function takeover(Authenticatable $user, EditLock $lock){
        // user owns this lock or has permission to manage locks of this type or can manage locks globally.
        return $this->ownsLock($user,$lock) ||
                Gate::allows('Laracraft.manageLocks', new $lock->lockable_type);
    }


    /**
     * Check if the user is the owner of the lock
     *
     * @param Authenticatable $user
     * @param EditLock $lock
     * @return bool
     */
    private function ownsLock(Authenticatable $user, EditLock $lock){
        return $lock->created_by == $user->getAuthIdentifier();
    }

}
