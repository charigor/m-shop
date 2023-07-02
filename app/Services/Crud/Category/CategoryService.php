<?php


namespace App\Services\Crud\Category;


use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Services\BaseCrudService;
use App\Services\Datatables\Categories\Categories;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CategoryService extends BaseCrudService
{
    public function __construct()
    {
        parent::__construct(new Category());
    }

    /**
     * @param $request
     * @param null $params
     * @return mixed
     */
    public function getItems($request, $params = null)
    {
        return (new Categories)->table($request,$params);
    }

    /**
     * @param $request
     * @return \Illuminate\Database\Eloquent\Builder|Model
     */
    public function createItem($request)
    {
        $data = $request->all();
        $category = $this->model::create($data);
        if(!empty($data['parent']))
        {
            $node =  $this->model::find($data['parent']);
            $node->appendNode($category);
        }
        foreach($data['image'] as  $file){
            if(file_exists(storage_path('tmp/uploads/'.$file))) $category->addMedia(storage_path('tmp/uploads/' . $file))->toMediaCollection('uploadfiles');
        }
        return $category;
    }

    /**
     * @param $model
     * @param $request
     * @return mixed
     */
    public function updateItem($model,$request){
        $data = $request->all();

        $model->update($data);

        if(!empty($data['parent']))
        {
            $node =  $this->model::find($data['parent']);
            $node->appendNode($model);
        }else{
            $model->saveAsRoot();
        }
        foreach($data['image'] as  $file){
            if(file_exists(storage_path('tmp/uploads/'.$file))) $model->addMedia(storage_path('tmp/uploads/' . $file))->toMediaCollection('uploadfiles') ;
        }
        foreach($model->media as $media){
            if(!in_array($media->file_name,$data['image'])) {$media->delete();}
        }
        return $model->refresh();
    }

    /**
     * @param $request
     * @return \Illuminate\Http\Response
     */
    public function sortItem($request): \Illuminate\Http\Response
    {

        $data = $request->all();

        if($data['id'] === 'categories')
        {
            $this->model::rebuildTree($data['el']);
        }else{
            $node =  $this->model::find($data['id']);
            $this->model::rebuildSubtree($node,$data['el']);
        }
        return response()->noContent();
    }
}
