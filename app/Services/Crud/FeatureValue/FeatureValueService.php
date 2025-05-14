<?php

namespace App\Services\Crud\FeatureValue;

use App\Models\FeatureValue;
use App\Models\Product;
use App\Services\BaseCrudService;
use App\Services\Datatables\FeatureValues\FeatureValues;
use App\Services\TranslationService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class FeatureValueService extends BaseCrudService
{
    public function __construct()
    {
        parent::__construct(new FeatureValue);
    }

    public function getItems($request, $model = null)
    {
        return (new FeatureValues)->table($request, $model->id);
    }

    /**
     * @return Builder|Model
     */
    public function createItem($request)
    {
        $data = $request->validated();
        $model = $this->model::create($data);
        $prepareData = (new TranslationService)->prepareFields($data['lang'], ['value']);

        $model->translation()->createMany($prepareData);

        return $model;
    }

    public function updateItem($model, $request): mixed
    {
        $data = $request->validated();
        $model->update($data);
        $prepareData = (new TranslationService)->prepareFields($data['lang'], ['value']);
        foreach ($prepareData as $item) {
            $model->translation()->where('locale', $item['locale'])->update($item);
        }

        return $model->refresh();
    }

    public function deleteItems($request): mixed
    {
        $products = Product::whereHas('features', function ($q) use ($request) {
            $q->whereIn('id', $request->ids);
        })->get();
        $result = $this->model->whereIn('id', $request->ids)->delete();
        $products->searchable();

        return $result;
    }
}
