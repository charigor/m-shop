<?php


namespace App\Services\Crud\Product;


use App\Events\ProductUpdateIndex;
use App\Models\FeatureValueProduct;
use App\Models\FeatureValue;
use App\Models\Product;
use App\Services\BaseCrudService;
use App\Services\Datatables\Products\Products;
use App\Services\TranslationService;
use Barryvdh\Debugbar\Facades\Debugbar;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ProductService extends BaseCrudService
{
    public function __construct()
    {
        parent::__construct(new Product());
    }

    /**
     * @param $request
     * @param null $params
     * @return mixed
     */
    public function getItems($request, $params = null): mixed
    {
        return (new Products)->table($request);
    }

    /**
     * @param $request
     * @return Builder|Model|void
     */
    public function createItem($request)
    {

        $data = $request->validated();
        $model = $this->model::create($data);
        $prepareData = (new TranslationService)->prepareFields($data['lang'],['name','link_rewrite']);
        $model->translation()->createMany($prepareData);
        $this->addMedia($model, $data, ['image']);
        if($data['categories'])  $model->categories()->attach($data['categories']);
        if($data['features']) $this->createUpdateProductFeature($model,$data);
        $model->refresh();
        return  $model;
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

        $prepareData = (new TranslationService)->prepareFields($data['lang'],['name','link_rewrite']);
        foreach ($prepareData as $item) {
            $model->translation()->updateOrCreate(['locale' => $item['locale']],$item);
        };
        $model->categories()->sync($data['categories']);

        $this->createUpdateProductFeature($model,$data);
        $this->addMedia($model, $data, ['image']);
        $this->removeMedia($model, $data, ['image']);
        $model->refresh();

        return $model;


    }

    /**
     * @param $model
     * @param $slug_field
     * @param $slug_from
     */
    public function setSlug($model, $slug_field, $slug_from): string
    {
        return $slug_from ? SlugService::createSlug($model, $slug_field, $slug_from) : "";
    }

    /**
     * @param $model
     * @param $data
     * @param array $collections
     */
    public function removeMedia($model, $data, array $collections = []): void
    {
        foreach($collections as $name){
            foreach($model->getMedia($name) as $media){
                if(!in_array($media->file_name,$data[$name])) {$media->delete();}
            }
        }

    }

    /**
     * @param $model
     * @param $data
     * @param array $collections
     */
    public function addMedia($model, $data, array $collections = []): void
    {
        foreach ($collections as $name) {
            foreach ($data[$name] as $key => $file) {
                if (file_exists(storage_path('app/public/tmp/uploads/' . $file))) {
                    $model->addMedia(storage_path('app/public/tmp/uploads/' . $file))->withCustomProperties(['order' => ($data['main_image'] === $file) ? 0 : $key + 1,'main_image' => ($data['main_image'] === $file) ? 1: 0])->toMediaCollection($name);
                }else{
                    foreach($model->getMedia($name) as $media){
                        if($media->file_name === $file){
                            $media->setCustomProperty('main_image', ($data['main_image'] === $file) ? 1 : 0);
                            $media->setCustomProperty('order', ($data['main_image'] === $file) ? 0 : $key + 1);
                            $media->save();
                        }
                    }
                }
            }
        }
    }

    /**
     * @param $model
     * @param $request
     * @return mixed
     */
    public function deleteProductFeature($model,$request): mixed
    {
        $model->load('features');
        $data = $request->input('feature');
        return $model->features()->detach($data);
    }

    /**
     * @param $model
     * @param $data
     * @return void
     */
    public function createUpdateProductFeature($model,$data): void
    {
        $arr = [];
        foreach ($data['features'] as $item) {
            $arr[$item['feature_value_id']] = ['feature_id' => $item['feature_id']];
        }
            $model->features()->sync($arr);
        }
//    }
}
