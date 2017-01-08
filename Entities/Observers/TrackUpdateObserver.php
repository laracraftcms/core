<?php

namespace Laracraft\Core\Entities\Observers;

use Auth;

class TrackUpdateObserver
{

    public function saving($model)
    {
        // If there is an authorized user
        if (Auth::check()) {
            $model->updated_by = Auth::user()->getAuthIdentifier();
        }
    }

}