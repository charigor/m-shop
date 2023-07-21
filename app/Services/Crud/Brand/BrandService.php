<?php


namespace App\Services\Crud\Brand;

use App\Models\Brand;
use App\Services\BaseCrudService;
use App\Services\Datatables\Brands\Brands;
use App\Services\TranslationService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class BrandService extends BaseCrudService
{
    public function __construct()
    {
        parent::__construct(new Brand());
    }

    /**
     * @param $request
     * @param null $params
     * @return mixed
     */
    public function getItems($request, $params = null)
    {
        return (new Brands)->table($request);
    }
    /**
     * @param $request
     * @return Builder|Model|void
     */
    public function createItem($request)
    {
        $data = $request->validated();
        $model = $this->model::create($data);
        $prepareData = (new TranslationService)->prepareFields($data['lang']);

        $model->translation()->createMany($prepareData);

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
        $data = $request->validated();

        $model->update($data);
        $prepareData = (new TranslationService)->prepareFields($data['lang']);
        foreach ($prepareData as $item) {
            $model->translation()->where('locale', $item['locale'])->update($item);
        };

        $this->addMedia($model, $data, ['image']);

        $this->removeMedia($model, $data, ['image']);
        return $model->refresh();


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
            foreach ($data[$name] as $file) {
                if (file_exists(storage_path('app/public/tmp/uploads/' . $file))) $model->addMedia(storage_path('app/public/tmp/uploads/' . $file))->toMediaCollection($name);
            }
        }
    }
}
