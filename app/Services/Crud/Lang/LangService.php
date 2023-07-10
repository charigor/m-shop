<?php


namespace App\Services\Crud\Lang;


use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Models\Lang;
use App\Services\BaseCrudService;
use App\Services\Datatables\Categories\Categories;
use App\Services\Datatables\Langs\Langs;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class LangService extends BaseCrudService
{
    public function __construct()
    {
        parent::__construct(new Lang());
    }

    /**
     * @param $request
     * @param null $params
     * @return mixed
     */
    public function getItems($request, $params = null)
    {
        return (new Langs)->table($request,$params);
    }

}
