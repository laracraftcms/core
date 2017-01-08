<?php

namespace Laracraft\Core\Policies;

use Laracraft\Core\Entities\Section;
use Gate;
use Illuminate\Contracts\Auth\Authenticatable;

class SectionPolicy
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

    public function store(Authenticatable $user, Section $section){

        return Gate::allows('Laracraft.Section:store');

    }

    public function update(Authenticatable $user, Section $section){

        return Gate::allows('Laracraft.Section#'.$section->id . ':update') || Gate::allows('Section:update');

    }

    public function manageLocks(Authenticatable $user){

        //user can manage Section locks or can manage locks globally.

        return Gate::allows('Laracraft.Section:manageLocks') || Gate::allows('manageLocks');

    }
}
