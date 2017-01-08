<?php

namespace Laracraft\Core\Entities\Observers;

use Illuminate\Database\Eloquent\Model;

class PublishableObserver{


    public function saved(Model $model)
    {
        $this->triggerPublishableEvents($model,'saved');
    }

    public function restored(Model $model)
    {
         $this->triggerPublishableEvents($model, 'restored');
    }

    private function triggerPublishableEvents($model, $event){


        if($model->isDirty($model->getPublishedAtColumn())) {
            $published_at = $this->getPublishedAtAttribute();
            $old_published_at = $model->getOriginal($model->getPublishedAtColumn());

            $model->firePublishedAtChangedEvent($published_at, $old_published_at, $event);
        }
        if($model->isDirty($model->getExpiredAtColumn())) {
            $expired_at = $this->getExpiredAtAttribute();
            $old_expired_at = $model->getOriginal($model->getExpiredAtColumn());

            $model->fireExpiredAtChangedEvent($expired_at, $old_expired_at, $event);
        }

    }

}