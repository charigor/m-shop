<?php


namespace App\Services\Crud\FeatureValue;

use App\Models\FeatureValue;
use App\Services\BaseCrudService;
use App\Services\Datatables\FeatureValues\FeatureValues;
use App\Services\TranslationService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class FeatureValueService extends BaseCrudService
{
    public function __construct()
    {
        parent::__construct(new FeatureValue());
    }


    public function getItems($request, $model = null)
    {
        return (new FeatureValues)->table($request,$model->id);
    }

    /**
     * @param $request
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
    /**
     * @param $model
     * @param $request
     * @return mixed
     */
    public function updateItem($model,$request): mixed
    {
        $data = $request->validated();
        $model->update($data);
        $prepareData = (new TranslationService)->prepareFields($data['lang'], ['value']);
        foreach ($prepareData as $item) {
            $model->translation()->where('locale', $item['locale'])->update($item);
        };
        return $model->refresh();
    }
}
