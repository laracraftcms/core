<?php

namespace Laracraft\Core\Entities;

use Alsofronie\Uuid\UuidModelTrait;
use Baum\Node;
use Illuminate\Database\Eloquent\Model;

class Structure extends Node
{

	use UuidModelTrait;

    public function structured(){
        return $this->morphTo();
    }

    public function entity(){
        return $this->morphTo();
    }
}
