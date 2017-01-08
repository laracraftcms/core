<?php

namespace Laracraft\Core\Entities\Observers;

use Laracraft\Core\Entities\Contracts\LockableContract as Lockable;

class LockableObserver{


    public function saving(Lockable $model)
    {
        $model->releaseLock();
    }

    public function restoring(Lockable $model)
    {
        $model->releaseLock();
    }

	public function deleting(Lockable $model)
	{
		$model->releaseLock();
	}
}