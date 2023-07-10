<?php


namespace App\Services\Datatables\Langs;


use App\Models\Lang;
use App\Services\Datatables\Langs\Filters\Code;
use App\Services\Datatables\Langs\Filters\DateFormat;
use App\Services\Datatables\Langs\Filters\DateFormatFull;
use App\Services\Datatables\Traits\Datatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Services\Datatables\Langs\Filters\Id;
use App\Services\Datatables\Langs\Filters\Name;
use App\Services\Datatables\Langs\Filters\Active;
class Langs
{
    use Datatable;
    protected int $perPage = 25;
    protected array $filters = [
        'id'               => Id::class,
        'name'             => Name::class,
        'code'             => Code::class,
        'active'           => Active::class,
        'date_format'      => DateFormat::class,
        'date_format_full' => DateFormatFull::class,
    ];

    protected array $search = [
        'name',
        'code',
        'active',
        'date_format',
        'date_format_full'
    ];
     protected array $sort = [
         'id',
         'name',
         'code',
         'active',
         'date_format',
         'date_format_full'
     ];
    /**
     * @return Builder
     */
    public function query(): Builder
    {

        return Lang::selectRaw('langs.id,langs.name,langs.code,langs.active,langs.date_format,langs.date_format_full');
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function table(Request $request)
    {

        $request->validate([
            'direction' => ['in:asc,desc'],
            'field' => ['in:'.implode(',',$this->sort)]
        ]);
        $query = $this->query();
        $query = $this->filter($request,$query);
        $query = $this->search($request,$query);
        $query = $request->has('direction') ? $this->sort($request,$query) : $query->orderByDesc('id');
        $this->perPage = $this->pagination($request,$query);

        return $query->paginate($this->perPage);

    }
}

