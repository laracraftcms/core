<?php

namespace Laracraft\Core\Entities;

use Alsofronie\Uuid\UuidModelTrait;
use Illuminate\Database\Eloquent\Model;

class Url extends Model
{

	public $incrementing = false;
	public $timestamps = false;
	public $primaryKey = 'routable_id';

	public $table = 'urls';

	public $fillable = [
		'routable_id',
		'routable_type',
		'url',
		'hash'
	];

	public function routable(){
		return $this->morphTo('routable');
	}
}