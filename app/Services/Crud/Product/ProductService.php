<?php


namespace App\Services\Crud\Product;

use App\Models\Category;
use App\Models\Product;
use App\Services\BaseCrudService;
use App\Services\Datatables\Categories\Categories;
use App\Services\TranslationService;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
        return '';
    }

    /**
     * @param $request
     * @return Builder|Model|void
     */
    public function createItem($request)
    {

        $data = $request->all();

        $model = $this->model::create(['name' => Str::random(8),'category_id' => 1, 'active' => $data['active'],'description' => Str::random(80)]);
        $this->addMedia($model, $data, ['image']);

        return  $model;
    }

    /**
     * @param $model
     * @param $request
     * @return mixed
     */
    public function updateItem($model,$request): mixed
    {
        $data = $request->all();

        $model->update(['active' => 1]);


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
}
