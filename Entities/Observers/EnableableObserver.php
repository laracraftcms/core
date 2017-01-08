<?php

namespace Laracraft\Core\Entities\Observers;

use Laracraft\Core\Entities\Contracts\EnableableContract as Enableable;
use Illuminate\Contracts\Events\Dispatcher;

class EnableableObserver{

    protected $events;

    public function __construct(Dispatcher $events)
    {
        $this->events = $events;
    }

    public function saving(Enableable $model)
    {
        $this->triggerEnableablePreEvents($model,'saving');
    }

    public function restoring(Enableable $model)
    {
        $this->triggerEnableablePreEvents($model, 'restoring');
    }

    public function saved(Enableable $model)
    {
        $this->triggerEnableablePostEvents($model,'saved');
    }

    public function restored(Enableable $model)
    {
         $this->triggerEnableablePostEvents( $model, 'restored');
    }

    private function triggerEnableablePreEvents(Enableable $model, $event){

        if($event=='saving' && !$model->isDirty($model->getEnabledColumn())) {
            //nothing to do
            return;
        }

            if ($model->getEnabledAttribute()) {
                if($model->fireEnablingEvent() !==null){
                    $model->setEnabledAttribute(false);
                    $model->setEnabling(false);
                    $model->setDisabling(false);
                }else{
					$model->setEnabling(true);
					$model->setDisabling(false);
                }
            } elseif ($event=='saving') {
                if($model->fireDisablingEvent() !==null){
                    $model->setEnabledAttribute(true);
					$model->setEnabling(false);
					$model->setDisabling(false);
                }else{
					$model->setEnabling(false);
					$model->setDisabling(true);
                }
            }
    }

    private function triggerEnableablePostEvents(Enableable $model, $event){

        if($model->isEnabling()){
            $model->fireEnabledEvent();
        }elseif($model->isDisabling()){
            $model->fireDisabledEvent();
        }
    }
}