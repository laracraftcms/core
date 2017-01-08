<?php

namespace Laracraft\Core\Entities;

use Alsofronie\Uuid\UuidModelTrait;
use Laracraft\Core\Entities\Contracts\LockableContract;
use Laracraft\Core\Entities\Traits\Lockable;
use Illuminate\Database\Eloquent\Model;
use Artisan;

class FieldGroup extends Model implements LockableContract
{
	use UuidModelTrait;
    use Lockable;

	const DEFAULT_GROUP = 'd51b1e44-fd23-4f39-ad43-61d3b826f931';
	const DEFAULT_GROUP_TABLE = 'default';

    public $table = 'field_groups';

    protected $fillable = ['name', 'table_name'];

	public $renamed = null;

	public function getRouteKeyName() {
		return 'table_name';
	}

    /**
     * Boot model
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function (FieldGroup $model){
            app()->make('laracraft.generator.migration.field_group')->create($model);
			Artisan::call('migrate', array('--force' => true));
        });
		static::updating(function(FieldGroup $model){
			if($model->isDirty('table_name')){
				$model->renamed = $model->getOriginal('table_name') . '_content';
			}
		});
		static::updated(function (FieldGroup $model){
			if(!is_null($model->renamed)) {
				app()->make('laracraft.generator.migration.field_group')->rename($model, $model->renamed);
				Artisan::call('migrate', ['--force' => true]);
			}
		});
		static::deleting(function(FieldGroup $model){
			if($model->id === static::DEFAULT_GROUP){
				return false;
			}
		});
    }

    public function fields(){
        return $this->hasMany(Field::class);
    }

    public function fieldCount(){

        return $this->fields()
            ->selectRaw('count(*) as aggregate, field_group_id')
            ->groupBy('field_group_id');
    }

    public function getFieldCountAttribute()
    {
        // if relation is not loaded already, let's do it first
        if ( ! $this->relationLoaded('fieldCount'))
            $this->load('fieldCount');

        $related = $this->getRelation('fieldCount')->first();

        // then return the count directly
        return ($related) ? (int) $related->aggregate : 0;
    }


}
