<?php

namespace App\Services\Crud\Brand;

use App\Models\Brand;
use App\Services\BaseCrudService;
use App\Services\Datatables\Brands\Brands;
use App\Services\TranslationService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BrandService extends BaseCrudService
{
    public function __construct()
    {
        parent::__construct(new Brand());
    }

    /**
     * @param  null  $params
     */
    public function getItems($request, $params = null): mixed
    {
        return (new Brands)->table($request);
    }

    /**
     * @return Builder|Model|mixed
     */
    public function createItem($request): mixed
    {
        $data = $request->validated();

        return DB::transaction(function () use ($data) {
            $model = $this->model::create($data);
            $prepareData = (new TranslationService)->prepareFields($data['lang']);
            $model->translation()->createMany($prepareData);
            $this->addMedia($model, $data, ['image']);

            return $model;
        }
        );
    }

    public function updateItem($model, $request): mixed
    {
        $data = $request->validated();
        $model->slug = null;

        return DB::transaction(function () use ($data, $model) {
            $model->update($data);
            $prepareData = (new TranslationService)->prepareFields($data['lang']);
            foreach ($prepareData as $item) {
                $model->translation()->where('locale', $item['locale'])->update($item);
            }
            $this->addMedia($model, $data, ['image']);
            $this->removeMedia($model, $data, ['image']);

            return $model->refresh();
        }
        );

    }

    public function removeMedia($model, $data, array $collections = []): void
    {
        foreach ($collections as $name) {
            foreach ($model->getMedia($name) as $media) {
                if (! in_array($media->file_name, $data[$name])) {
                    $media->delete();
                }
            }
        }

    }

    public function addMedia($model, $data, array $collections = []): void
    {
        foreach ($collections as $name) {
            foreach ($data[$name] as $file) {
                if (file_exists(storage_path('app/public/tmp/uploads/'.$file))) {
                    $model->addMedia(storage_path('app/public/tmp/uploads/'.$file))->toMediaCollection($name);
                }
            }
        }
    }

    public function deleteItems($request): mixed
    {
        $data = $request->only('ids');

        return $this->model->whereIn('id', $data['ids'])->delete();
    }
}
