<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Traits\MediaUploadingTrait;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Services\Crud\Category\CategoryService;
use App\Services\Datatables\Categories\Categories;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends Controller
{
    use MediaUploadingTrait;
    private CategoryService $service;

    /**
     * @param CategoryService $categoryService
     */
    public function __construct(private CategoryService   $categoryService)
    {
        $this->service = $categoryService;
    }
    public function index(Request $request,$parent_id = null) {

        abort_unless(Auth::user()->hasAnyRole(['admin']), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $data = $this->service->getItems($request,$parent_id);

        return Inertia::render('Categories/Index', [
            'categories' => CategoryResource::collection($data),
            'search' => $request->get('search'),
            'filter' => $request->get('filter')
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $category = $this->service->createItem($request);

        return redirect()->route('category.edit', $category->id)->with('message','Item(s) was created successfully');;

    }

    public function update(Request $request, Category $category)

    {
        $this->service->updateItem($category,$request);
        return redirect()->route('category.index')->with('message','Item(s) was updated successfully');
    }
    /**
     * @return \Inertia\Response
     */
    public function create()
    {
        return Inertia::render('Categories/Create', [
            'category' => CategoryResource::make(new Category()),
            'categories' => CategoryResource::collection(Category::orderBy('name')->get())->resolve(),
        ]);
    }

    /**
     * @param Category $category
     * @return \Inertia\Response
     */
    public function edit(Category $category)
    {
        return Inertia::render('Categories/Edit', [
            'category' => CategoryResource::make($category->load('media'))->resolve(),
            'categories' => CategoryResource::collection(Category::where('id', '!=' , $category->id)->get())->resolve(),
        ]);
    }
    /**
     * @param Request $request
     */
    public function destroy(Request $request)
    {
        Category::whereIn('id',$request->ids)->delete();
        return redirect()->route('category.index')->with('message','Item(s) was deleted successfully');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function sort(Request $request){
        return $this->service->sortItem($request);
    }
}
