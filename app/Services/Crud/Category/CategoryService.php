<?php

namespace App\Services\Crud\Category;

use App\Models\Category;
use App\Models\Product;
use App\Services\BaseCrudService;
use App\Services\Datatables\Categories\Categories;
use App\Services\TranslationService;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;

class CategoryService extends BaseCrudService
{
    public function __construct()
    {
        parent::__construct(new Category);
    }

    /**
     * @param  null  $params
     */
    public function getItems($request, $params = null): mixed
    {
        return (new Categories)->table($request, $params);
    }

    /**
     * @return Builder|Model|void
     */
    public function createItem($request)
    {
        $data = $request->validated();
        $category = $this->model::create(['parent_id' => $data['parent_id'], 'active' => $data['active']]);
        $prepareData = (new TranslationService)->prepareFields($data['lang'], ['title', 'link_rewrite']);

        $category->translation()->createMany($prepareData);
        if (! $data['parent_id'] == 0) {
            $node = $this->model::find($data['parent_id']);
            $node->appendNode($category);
        }

        $this->addMedia($category, $data, ['cover_image', 'menu_thumbnail']);

        return $category;
    }

    public function updateItem($model, $request): mixed
    {
        $data = $request->validated();

        $model->update(['parent_id' => $data['parent_id'], 'active' => $data['active']]);
        $prepareData = (new TranslationService)->prepareFields($data['lang'], ['title', 'link_rewrite']);
        foreach ($prepareData as $item) {
            $model->translation()->where('locale', $item['locale'])->update($item);
        }
        if (! $data['parent_id'] == 0) {

            $node = $this->model::find($data['parent_id']);
            $node->appendNode($model);
        } else {
            $model->saveAsRoot();
        }

        $this->addMedia($model, $data, ['cover_image', 'menu_thumbnail']);

        $this->removeMedia($model, $data, ['cover_image', 'menu_thumbnail']);

        return $model->refresh();

    }

    public function sortItem($request): Response
    {
        $data = $request->all();

        foreach ($data['el'] as $key => $item) {
            unset($data['el'][$key]['active']);
        }
        if ($data['id'] === 'categories') {
            $this->model::rebuildTree($data['el']);
        } else {
            $node = $this->model::find($data['id']);
            $this->model::rebuildSubtree($node, $data['el']);
        }

        return response()->noContent();
    }

    public function setSlug($model, $slug_field, $slug_from): string
    {
        return $slug_from ? SlugService::createSlug($model, $slug_field, $slug_from) : '';
    }

    public function removeMedia($model, $data, array $collections = [])
    {
        foreach ($collections as $name) {
            foreach ($model->getMedia($name) as $media) {
                if (! in_array($media->file_name, $data[$name])) {
                    $media->delete();
                }
            }
        }

    }

    public function addMedia($model, $data, array $collections = [])
    {
        foreach ($collections as $name) {
            foreach ($data[$name] as $file) {
                if (file_exists(storage_path('app/public/tmp/uploads/'.$file))) {
                    $model->addMedia(storage_path('app/public/tmp/uploads/'.$file))->toMediaCollection($name);
                }
            }
        }
    }

    public function deleteItems($request)
    {
        $products = Product::whereHas('categories', function ($q) use ($request) {
            $q->whereIn('id', $request->ids);
        })->get();
        $result = $this->model->whereIn('id', $request->ids)->delete();
        $products->searchable();

        return $result;
    }
}
