<?php

namespace Laracraft\Core\Policies;

use Laracraft\Core\Entities\Entry;
use Gate;
use Illuminate\Contracts\Auth\Authenticatable;

class EntryPolicy
{

    /**
     * @param $user
     * @param $ability
     * @return boolean
     */
    public function before(Authenticatable $user, $ability)
    {
		if (method_exists($user,'isLaracraftSuperAdmin') && $user->isLaracraftSuperAdmin()) {
			return true;
		}

		return null;
    }

    public function create(Authenticatable $user){

        return Gate::allows('laracraft.entry:create');

    }

    public function edit(Authenticatable $user, Entry $section){

        return Gate::allows('laracraft.entry#'.$section->id . ':edit') || Gate::allows('laracraft.section:update');

    }

    public function manageLocks(Authenticatable $user){

        //user can manage Section locks or can manage locks globally.
        return Gate::allows('laracraft.entry:manageLocks') || Gate::allows('laracraft.manageLocks');

    }
}
