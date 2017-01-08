<?php
namespace Laracraft\Core\Entities\Traits;

use Laracraft\Core\Entities\Field;
use Laracraft\Core\Entities\FieldLayout;

trait HasFieldLayout{


    public function fieldLayout(){

        return $this->belongsTo(FieldLayout::class);

    }
}