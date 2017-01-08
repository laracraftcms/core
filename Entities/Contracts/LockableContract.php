<?php
namespace Laracraft\Core\Entities\Contracts;

use Laracraft\Core\Exceptions\ModelLockedException;
use Illuminate\Contracts\Validation\UnauthorizedException;

interface LockableContract
{

    /**
     * Obtain a lock for this resource or throw appropriate exception
     *
     * @return bool true on successful locking
     * @throws ModelLockedException|UnauthorizedException
     */
    public function getLock();

    /**
     * Check if resource is locked
     *
     * @return bool
     */
    public function isLocked();


    public function releaseLock();

    public static function releaseLocks();
}