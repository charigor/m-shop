<?php

namespace App\Services\Crud\AttributeGroup;

use App\Models\AttributeGroup;
use App\Services\BaseCrudService;
use App\Services\Datatables\AttributeGroups\AttributeGroups;
use App\Services\Datatables\Attributes\Attributes;
use App\Services\TranslationService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;

class AttributeGroupService extends BaseCrudService
{
    public function __construct()
    {
        parent::__construct(new AttributeGroup());
    }

    /**
     * @param  null  $params
     * @return mixed
     */
    public function getItems($request, $params = null)
    {
        return (new AttributeGroups)->table($request);
    }

    /**
     * @return Builder|Model
     */
    public function createItem($request)
    {
        $data = $request->validated();
        $max = $this->model->max('position');
        $data['position'] = $max + 1;
        $data['is_color_group'] = ($data['group_type'] === array_search('color', AttributeGroup::GROUP_TYPE));
        $model = $this->model::create($data);
        $prepareData = (new TranslationService)->prepareFields($data['lang'], ['name', 'public_name']);
        $model->translation()->createMany($prepareData);

        return $model;
    }

    public function updateItem($model, $request): mixed
    {
        $data = $request->validated();
        $data['is_color_group'] = ($data['group_type'] === array_search('color', AttributeGroup::GROUP_TYPE));
        $model->update($data);
        $prepareData = (new TranslationService)->prepareFields($data['lang'], ['name', 'public_name']);
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
}
