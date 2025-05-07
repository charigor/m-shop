<?php

namespace App\Services\Datatables\Categories;

use App\Models\Category;
use App\Services\Datatables\Categories\Filters\Active;
use App\Services\Datatables\Categories\Filters\Created_at;
use App\Services\Datatables\Categories\Filters\Id;
use App\Services\Datatables\Categories\Filters\Title;
use App\Services\Datatables\Traits\Datatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class Categories
{
    use Datatable;

    protected mixed $param = null;

    protected int $perPage = 25;

    protected array $filters = [
        'id' => Id::class,
        'title' => Title::class,
        'active' => Active::class,
        'created_at' => Created_at::class,
    ];

    protected array $search = [
        'title',
    ];

    protected array $sort = [
        'id',
        'title',
        'active',
        'created_at',
    ];

    public function query(): Builder
    {
        return Category::with('children')
            ->selectRaw('categories.id,category_lang.title,categories.created_at,categories.active,categories.parent_id,category_lang.meta_title,category_lang.meta_description,category_lang.meta_keywords,langs.code')
            ->leftJoin('category_lang', 'categories.id', '=', 'category_lang.category_id')
            ->leftJoin('langs', 'category_lang.locale', '=', 'langs.code')
            ->where('langs.code', app()->getLocale())
            ->where('categories.parent_id', $this->param);
    }

    /**
     * @return mixed
     */
    public function table(Request $request, $param = null)
    {

        $this->param = $param;
        $request->validate([
            'direction' => ['in:asc,desc'],
            'field' => ['in:'.implode(',', $this->sort)],
        ]);
        $query = $this->query();
        $query = $this->filter($request, $query);
        $query = $this->search($request, $query);
        $query = $request->has('direction') ? $this->sort($request, $query) : $query->defaultOrder();
        $this->perPage = $this->pagination($request, $query);

        return $query->paginate($this->perPage);

    }
}
