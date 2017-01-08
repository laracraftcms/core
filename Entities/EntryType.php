<?php

namespace Laracraft\Core\Entities;

use Alsofronie\Uuid\UuidModelTrait;
use Laracraft\Core\Entities\Contracts\LockableContract;
use Laracraft\Core\Entities\Traits\HasFieldLayout;
use Laracraft\Core\Entities\Traits\Lockable;
use Illuminate\Database\Eloquent\Model;

class EntryType extends Model implements LockableContract
{
	use UuidModelTrait;
    use HasFieldLayout;
    use Lockable;

    public $table = 'entry_types';

    protected $fillable = [
        'field_layout_id',
        'name',
        'handle',
        'has_title_field',
        'title_config'
    ];


	public function getRouteKeyName() {
		return 'handle';
	}

    public function sections(){

        return $this->belongsToMany(Section::class,'section_entry_types','entry_type_id','section_id');
    }
}
