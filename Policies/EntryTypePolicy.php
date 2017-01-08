<?php

namespace Laracraft\Core\Policies;

use Laracraft\Core\Entities\EntryType;
use Illuminate\Contracts\Auth\Authenticatable;
use Gate;

class EntryTypePolicy
{

    /**
     * @param Authenticatable $user
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

    public function store(Authenticatable $user, Entrytype $type){

        return Gate::allows('Laracraft.EntryType:store');

    }

    public function update(Authenticatable $user, Entrytype $type){

        return Gate::allows('Laracraft.EntryType#'.$type->id . ':update') || Gate::allows('EntryType:update');

    }

    public function manageLocks(Authenticatable $user){

        //user can manage EntryType locks or can manage locks globally.

        return Gate::allows('Laracraft.EntryType:manageLocks') || Gate::allows('manageLocks');

    }
    
}
