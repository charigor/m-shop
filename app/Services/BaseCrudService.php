<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;

abstract class BaseCrudService
{
    /**
     * @var array
     */
    public $fields;

    /**
     * @var Model
     */
    public $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
        $this->fields = $model->getFillable();
    }

    abstract public function getItems($request, $params = null);

    public function getItem($id)
    {
        return $this->model::query()
            ->relations()
            ->find($id);
    }

    public function createItem($request)
    {
        $fields = $request->only($this->fields);
        if (! is_array($fields)) {
            $fields = $fields->all();
        }
        $item = $this->model->query()
            ->create($fields);

        return $item->refresh();
    }

    public function updateItem($model, $request)
    {

        // Model is soft delete
        if (method_exists($model, 'trashed')) {
            $model->withTrashed();
        }

        $fields = $request->only($this->fields);
        if (! is_array($fields)) {
            $fields = $fields->all();
        }

        $model->update($fields);

        return $model;

    }

    public function deleteItems($request)
    {
        return $this->model->whereIn('id', $request->ids)->delete();
    }
}
