<?php

namespace Laracraft\Core\Entities;

use Alsofronie\Uuid\UuidModelTrait;
use Illuminate\Database\Eloquent\Model;

class FieldLayoutTab extends Model
{
	use UuidModelTrait;

    public $table = 'field_layout_tabs';

    public function fields(){
        return $this->belongsToMany(Field::class,'field_layout_fields')->withPivot(['required','name_override','help_override'])->orderBy('sequence');
    }
}
