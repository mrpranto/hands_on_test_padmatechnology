<?php


namespace App\Services;


class BaseServices
{
    protected $model;

    public function __call($method, $arguments)
    {
        return $this->model->{$method}(...$arguments);
    }

    public function setModel($model)
    {
        $this->model = $model;

        return $this;
    }

}
