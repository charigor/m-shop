<?php

namespace App\Services\Crud\Lang;

use App\Models\Lang;
use App\Services\BaseCrudService;
use App\Services\Datatables\Langs\Langs;

class LangService extends BaseCrudService
{
    public function __construct()
    {
        parent::__construct(new Lang);
    }

    /**
     * @param  null  $params
     * @return mixed
     */
    public function getItems($request, $params = null)
    {
        return (new Langs)->table($request, $params);
    }
}
