<?php
namespace Laracraft\Core\Entities\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ScopeInterface;

class STIScope implements ScopeInterface{

    protected $baseModel;

    /**
     * @param Model $model
     * @internal param string $baseModel
     */
    public function __construct(Model $model){

        if(!isset($model::$sti_base_model)){
            throw new \InvalidArgumentException('static $sti_base_model is undefined or not visible');
        }

        $base = $model::$sti_base_model;

        if(new $base instanceof Model){
            $this->baseModel = $base;
        }else{
            throw new \InvalidArgumentException('$sti_base_model is not a Model or is undefined');
        }

        if(!$model instanceof $base){
            throw new \InvalidArgumentException('Model is not instanceof its $sti_base_model');
        }




    }
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        if(is_subclass_of($model,$this->baseModel)){
            $builder->where('type', '=', get_class($model));
        }
    }
    /**
     * Remove the scope from the given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function remove(Builder $builder, Model $model)
    {
        if(is_subclass_of($model,$this->baseModel)){

            $column = $model->getQualifiedTypeColumn();
            $query = $builder->getQuery();

            $query->wheres = collect($query->wheres)->reject(function ($where) use ($column, $model) {
                return $this->isTypeScopeConstraint($where, $column, $model);
            })->values()->all();

        }
    }


    protected function isTypeScopeConstraint($where, $column, $model)
    {
        return is_subclass_of($model,$this->baseModel) ? (($where['type'] == 'Basic') && ($where['column'] == $column) && ($where['value'] == get_class($model))) : false;
    }
}