<?php


namespace App\Services\Datatables\FeatureValues;

use App\Models\FeatureValue;
use App\Services\Datatables\FeatureValues\Filters\Id;
use App\Services\Datatables\FeatureValues\Filters\Value;
use App\Services\Datatables\Traits\Datatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
class FeatureValues
{
    use Datatable;
    protected int $perPage = 25;
    protected array $filters = [
        'id'               =>   Id::class,
        'value'            =>   Value::class,
    ];

    protected array $search = [
        'value'
    ];
     protected array $sort = [
         'id',
         'value',
     ];
    /**
     * @return Builder
     */
    public function query($id): Builder
    {

        return FeatureValue::selectRaw('feature_values.id,feature_value_lang.value,feature_value_lang.feature_value_id,langs.code')
                            ->leftJoin('feature_value_lang','feature_value_lang.feature_value_id','=','feature_values.id')
                            ->leftJoin('langs','feature_value_lang.locale','=','langs.code')
                            ->where('langs.code',app()->getLocale())
                            ->where('feature_values.feature_id',$id);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function table(Request $request,$id)
    {

        $request->validate([
            'direction' => ['in:asc,desc'],
            'field' => ['in:'.implode(',',$this->sort)]
        ]);
        $query = $this->query($id);
        $query = $this->filter($request,$query);
        $query = $this->search($request,$query);
        $query = $request->has('direction') ? $this->sort($request,$query) : $query->orderBy('id');
        $this->perPage = $this->pagination($request,$query);

        return $query->paginate($this->perPage);

    }
}

