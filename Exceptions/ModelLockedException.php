<?php

namespace Laracraft\Core\Exceptions;

use RuntimeException;

class ModelLockedException extends RuntimeException
{

    /**
     * The affected Eloquent model.
     *
     * @var string
     */
    protected $model;

    /**
     * Set the affected Eloquent model.
     *
     * @param  string   $model
     * @return $this
     */
    public function setModel($model)
    {
        $this->model = $model;

        $this->message = "The requested model is locked for editing by " . $model->editLock->createdBy;

        return $this;
    }

    /**
     * Get the affected Eloquent model.
     *
     * @return string
     */
    public function getModel()
    {
        return $this->model;
    }
}