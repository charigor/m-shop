<?php


namespace App\Services\Datatables\Attributes;


use App\Models\Attribute;
use App\Models\AttributeGroup;
use App\Services\Datatables\AttributeGroups\Filters\Id;
use App\Services\Datatables\AttributeGroups\Filters\Color;
use App\Services\Datatables\AttributeGroups\Filters\Position;
use App\Services\Datatables\AttributeGroups\Filters\Name;
use App\Services\Datatables\Traits\Datatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
class Attributes
{
    use Datatable;
    protected int $perPage = 25;
    protected array $filters = [
        'id'               =>   Id::class,
        'color'            =>   Color::class,
        'name'             =>   Name::class,
        'position'         =>   Position::class,
    ];

    protected array $search = [
        'name'
    ];
     protected array $sort = [
         'id',
         'color',
         'name',
         'position'
     ];
    /**
     * @return Builder
     */
    public function query($id): Builder
    {

        return Attribute::selectRaw('attributes.id,attributes.color,attributes.position,attribute_lang.name,attributes.attribute_group_id,langs.code')
                            ->leftJoin('attribute_lang','attribute_lang.attribute_id','=','attributes.id')
                            ->leftJoin('langs','attribute_lang.locale','=','langs.code')
                            ->where('langs.code',app()->getLocale())
                            ->where('attributes.attribute_group_id',$id);
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
        $query = $request->has('direction') ? $this->sort($request,$query) : $query->orderBy('position');
        $this->perPage = $this->pagination($request,$query);

        return $query->paginate($this->perPage);

    }
}

