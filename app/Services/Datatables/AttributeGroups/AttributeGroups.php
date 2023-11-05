<?php

namespace App\Services\Datatables\AttributeGroups;

use App\Models\AttributeGroup;
use App\Services\Datatables\AttributeGroups\Filters\Amount;
use App\Services\Datatables\AttributeGroups\Filters\GroupType;
use App\Services\Datatables\AttributeGroups\Filters\Id;
use App\Services\Datatables\AttributeGroups\Filters\IsColorGroup;
use App\Services\Datatables\AttributeGroups\Filters\Name;
use App\Services\Datatables\AttributeGroups\Filters\Position;
use App\Services\Datatables\Traits\Datatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class AttributeGroups
{
    use Datatable;

    protected int $perPage = 25;

    protected array $filters = [
        'id' => Id::class,
        'is_color_group' => IsColorGroup::class,
        'group_type' => GroupType::class,
        'name' => Name::class,
        'position' => Position::class,
        'amount' => Amount::class,
    ];

    protected array $search = [
        'name',
        'amount',
    ];

    protected array $sort = [
        'id',
        'is_color_group',
        'group_type',
        'name',
        'position',
        'amount',
    ];

    public function query(): Builder
    {

        return AttributeGroup::query()->selectRaw(' attribute_groups.id,attribute_groups.is_color_group,attribute_groups.group_type,attribute_groups.position,attribute_group_lang.name,langs.code, count(attributes.id) as amount')
            ->leftJoin('attribute_group_lang', 'attribute_group_lang.attribute_group_id', '=', 'attribute_groups.id')
            ->leftJoin('attributes', 'attributes.attribute_group_id', '=', 'attribute_groups.id')
            ->leftJoin('langs', 'attribute_group_lang.locale', '=', 'langs.code')
            ->where('langs.code', app()->getLocale())
            ->groupBy('attribute_groups.id', 'attribute_group_lang.name', 'langs.code');

    }

    /**
     * @return mixed
     */
    public function table(Request $request)
    {

        $request->validate([
            'direction' => ['in:asc,desc'],
            'field' => ['in:'.implode(',', $this->sort)],
        ]);
        $query = $this->query();
        $query = $this->filter($request, $query);
        $query = $this->search($request, $query);
        $query = $request->has('direction') ? $this->sort($request, $query) : $query->orderBy('position');
        $this->perPage = $this->pagination($request, $query);

        return $query->paginate($this->perPage);

    }
}
