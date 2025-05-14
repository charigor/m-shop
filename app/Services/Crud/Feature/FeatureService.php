<?php

namespace App\Services\Crud\Feature;

use App\Models\Feature;
use App\Models\Product;
use App\Services\BaseCrudService;
use App\Services\Datatables\Attributes\Attributes;
use App\Services\Datatables\Features\Features;
use App\Services\TranslationService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;

class FeatureService extends BaseCrudService
{
    public function __construct()
    {
        parent::__construct(new Feature);
    }

    /**
     * @param  null  $params
     * @return mixed
     */
    public function getItems($request, $params = null)
    {
        return (new Features)->table($request);
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

    public function getAttributeItems($request, $model)
    {
        return (new Attributes)->table($request, $model->id);
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

    public function deleteItems($request): mixed
    {
        $products = Product::whereHas('featureValues', function ($q) use ($request) {
            $q->whereIn('id', $request->ids);
        })->get();
        $result = $this->model->whereIn('id', $request->ids)->delete();
        $products->searchable();

        return $result;
    }
}
