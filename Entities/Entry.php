<?php

namespace Laracraft\Core\Entities;

use Alsofronie\Uuid\UuidModelTrait;
use Illuminate\Database\Query\Builder;
use Laracraft\Core\Entities\Contracts\EnableableContract;
use Laracraft\Core\Entities\Contracts\HasFieldsContract;
use Laracraft\Core\Entities\Contracts\LockableContract;
use Laracraft\Core\Entities\Contracts\RoutableContract;
use Laracraft\Core\Entities\Traits\Enableable;
use Laracraft\Core\Entities\Traits\HasFields;
use Laracraft\Core\Entities\Traits\Lockable;
use Laracraft\Core\Entities\Traits\Publishable;
use Laracraft\Core\Entities\Traits\Routable;
use Laracraft\Core\Entities\Traits\Tracked;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Laracraft\Core\Repositories\Contracts\UrlRepositoryContract;

class Entry extends Model implements LockableContract, EnableableContract, HasFieldsContract, RoutableContract
{

    use Enableable;
    //use Publishable;
    use Lockable;
    use Tracked;
	use HasFields;
	use UuidModelTrait;
	use Routable;

    public $table = 'entries';

    public $dates = [
		'created_at',
		'updated_at',
        'published_at',
        'expired_at'
    ];

    public $casts = [
      'enabled' => 'boolean'
    ];

	public $fillable = [
		'entry_type_id',
		'section_id',
		'updated_at',
		'created_at',
		'created_by'
	];

	protected $observables = ['slugged'];

    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);

        $this->initFieldValues();
    }

	public function section(){
       return $this->belongsTo(Section::class);
    }

    public function entryType(){
        return $this->belongsTo(EntryType::class);
    }

	public function fieldLayout(){
		return $this->entryType->fieldLayout();
	}


	public function needsUrl(){
		return $this->section->has_urls;
	}

	public function generateUrl(){

		switch($this->section->type){
			case Section::STRUCTURE_TYPE :
				return '';
			break;

			default :
				return app('laracraft.urlformatter')->format($this->section->id, $this->section->url_format, $this);
		}
	}

	public function resolveView() {
		return $this->resolvedView = $this->section->resolveView($this);
	}

	public function getExistingSlugs($slug){
		switch($this->section->type){
			case Section::CHANNEL_TYPE :
				return $this->newQuery()->where($this->get)->orWhere();
				break;
			case Section::STRUCTURE_TYPE :
				return [];
				break;
			default :
				return [];
		}
	}
}
