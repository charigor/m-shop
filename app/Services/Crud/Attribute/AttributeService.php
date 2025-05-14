<?php

namespace App\Services\Crud\Attribute;

use App\Models\Attribute;
use App\Services\BaseCrudService;
use App\Services\Datatables\Attributes\Attributes;
use App\Services\TranslationService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;

class AttributeService extends BaseCrudService
{
    public function __construct()
    {
        parent::__construct(new Attribute);
    }

    public function getItems($request, $params = null)
    {
        return (new Attributes)->table($request, $params->id);
    }

    /**
     * @return Builder|Model
     */
    public function createItem($request)
    {
        $data = $request->validated();
        $max = $this->model->max('position');
        $data['position'] = $max + 1;
        $model = $this->model::create($data);
        $prepareData = (new TranslationService)->prepareFields($data['lang'], ['name']);

        $model->translation()->createMany($prepareData);

        return $model;
    }

    public function updateItem($model, $request): mixed
    {
        $data = $request->validated();
        $model->update($data);
        $prepareData = (new TranslationService)->prepareFields($data['lang'], ['name']);
        foreach ($prepareData as $item) {
            $model->translation()->where('locale', $item['locale'])->update($item);
        }

        return $model->refresh();
    }

    public function sortItem($request): Response
    {
        $data = $request->all();

        foreach ($data['el'] as $key => $item) {
            $el = $this->model->find($item['id']);
            $el->position = $key;
            $el->save();
        }

        return response()->noContent();
    }
}
