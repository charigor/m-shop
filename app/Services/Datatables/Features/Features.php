<?php

namespace App\Services\Datatables\Features;

use App\Models\Feature;
use App\Services\Datatables\Features\Filters\Amount;
use App\Services\Datatables\Features\Filters\Id;
use App\Services\Datatables\Features\Filters\Name;
use App\Services\Datatables\Features\Filters\Position;
use App\Services\Datatables\Traits\Datatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class Features
{
    use Datatable;

    protected int $perPage = 25;

    protected array $filters = [
        'id' => Id::class,
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
        'name',
        'position',
        'amount',
    ];

    public function query(): Builder
    {

        return Feature::query()->selectRaw(' features.id,features.position,feature_lang.name,langs.code, count(feature_values.id) as amount')
            ->leftJoin('feature_lang', 'feature_lang.feature_id', '=', 'features.id')
            ->leftJoin('feature_values', 'feature_values.feature_id', '=', 'features.id')
            ->leftJoin('langs', 'feature_lang.locale', '=', 'langs.code')
            ->where('langs.code', app()->getLocale())
            ->groupBy('features.id', 'feature_lang.name', 'langs.code');

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
