<?php

namespace App\Services\Datatables\Products;

use App\Models\Product;
use App\Services\Datatables\Products\Filters\Active;
use App\Services\Datatables\Products\Filters\Created_at;
use App\Services\Datatables\Products\Filters\Id;
use App\Services\Datatables\Products\Filters\Name;
use App\Services\Datatables\Products\Filters\Price;
use App\Services\Datatables\Products\Filters\Quantity;
use App\Services\Datatables\Products\Filters\Reference;
use App\Services\Datatables\Products\Filters\Updated_at;
use App\Services\Datatables\Traits\Datatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class Products
{
    use Datatable;

    protected int $perPage = 25;

    protected array $filters = [
        'id' => Id::class,
        'name' => Name::class,
        'active' => Active::class,
        //         'price'       => Price::class,
        'quantity' => Quantity::class,
        'reference' => Reference::class,
        'created_at' => Created_at::class,
        'updated_at' => Updated_at::class,
    ];

    protected array $search = [
        'name',
        'reference',
    ];

    protected array $sort = [
        'id',
        'name',
        //        'price',
        'active',
        'quantity',
        'reference',
        'created_at',
        'updated_at',
    ];

    public function query(): Builder
    {

        return Product::with('media')->selectRaw('products.id,product_lang.name,products.reference,products.quantity,products.active,products.created_at,products.updated_at,langs.code')
            ->leftJoin('product_lang', 'products.id', '=', 'product_lang.product_id')
            ->leftJoin('langs', 'product_lang.locale', '=', 'langs.code')
            ->where('langs.code', app()->getLocale());
    }

    /**
     * @return mixed
     */
    public function table(Request $request, $param = null)
    {

        $request->validate([
            'direction' => ['in:asc,desc'],
            'field' => ['in:'.implode(',', $this->sort)],
        ]);
        $query = $this->query();
        $query = $this->filter($request, $query);
        $query = $this->search($request, $query);
        $query = $request->has('direction') ? $this->sort($request, $query) : $query->orderBy('created_at', 'desc');
        $this->perPage = $this->pagination($request, $query);

        return $query->paginate($this->perPage);

    }
}
