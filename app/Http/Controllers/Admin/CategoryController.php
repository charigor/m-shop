<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Traits\MediaUploadingTrait;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Models\CategoryLang;
use App\Services\Crud\Category\CategoryService;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends Controller
{
    use MediaUploadingTrait;
    private CategoryService $service;

    /**
     * @param CategoryService $categoryService
     */
    public function __construct(CategoryService   $categoryService)
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

        return redirect()->route('category.edit', $category->id)->with('message',trans('messages.success.create'));;

    }

    public function update(Request $request, Category $category)

    {
        $this->service->updateItem($category,$request);
        return redirect()->route('category.index')->with('message',trans('messages.success.update'));
    }
    /**
     * @return \Inertia\Response
     */
    public function create()
    {
        return Inertia::render('Categories/Create', [
            'category' => CategoryResource::make(new Category()),
            'categories' => CategoryResource::collection(Category::orderBy('id')->get())->resolve(),
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
        return redirect()->route('category.index')->with('message',trans('messages.success.delete'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function sort(Request $request){
        return $this->service->sortItem($request);
    }

    /**
     * @param Request $request
     */
    public function slug(Request $request): \Illuminate\Http\JsonResponse
    {
        $slug =  $this->service->setSlug(CategoryLang::class,'link_rewrite',$request->title);
        info($slug);
        return response()->json(['slug' => $slug ?? ""]);
    }
}
