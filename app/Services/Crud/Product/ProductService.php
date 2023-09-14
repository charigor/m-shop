<?php


namespace App\Services\Crud\Product;


use App\Models\FeatureProduct;
use App\Models\FeatureValue;
use App\Models\Product;
use App\Services\BaseCrudService;
use App\Services\Datatables\Products\Products;
use App\Services\TranslationService;
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

//         $data['features'] = json_encode(collect($data['features'])->map(function($item){
//            $item = ['feature_id' =>  $item['feature_id'] ,'feature_value' => $item['feature_value_id']];
//            return $item;
//        }));
        $model = $this->model::create($data);
        $prepareData = (new TranslationService)->prepareFields($data['lang'],['name','link_rewrite']);
        $model->translation()->createMany($prepareData);
        $this->addMedia($model, $data, ['image']);
        if($data['categories'])  $model->categories()->attach($data['categories']);
//
        if($data['features']) $this->createUpdateProductFeature($model,$data);
        return  $model->refresh();
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
            $model->translation()->where('locale', $item['locale'])->update($item);
        };
        $model->categories()->sync($data['categories']);

        $this->createUpdateProductFeature($model,$data);
        $this->addMedia($model, $data, ['image']);
        $this->removeMedia($model, $data, ['image']);
        return $model->refresh();


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
    public function removeMedia($model, $data, array $collections = []){
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
    public function addMedia($model, $data, array $collections = [])
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
    public function deleteProductFeature($model,$request)
    {
        $data = $request->input('feature');
        return FeatureProduct::where('product_id',$model->id)->where('feature_id',$data['feature_id'])->where('feature_value_id',$data['feature_value_id'])->delete();


    }
    public function createUpdateProductFeature($model,$data){
        foreach($data['features'] as $item) {
            FeatureProduct::updateOrCreate(
                ['product_id' => $model->id, 'feature_id' => $item['feature_id'], 'feature_value_id' => $item['feature_value_id']],
                ['product_id' => $model->id, 'feature_id' => $item['feature_id'], 'feature_value_id' => $item['feature_value_id']]);
        }
    }
}
