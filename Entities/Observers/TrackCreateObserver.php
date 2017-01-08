<?php

namespace Laracraft\Core\Entities\Observers;

use Auth;

class TrackCreateObserver{

    public function creating($model)
    {

        // If there is an authorized user
        if (Auth::check()) {
            $model->created_by = Auth::user()->getAuthIdentifier();
        }
    }

}