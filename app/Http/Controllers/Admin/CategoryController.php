<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Traits\MediaUploadingTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryCreateRequest;
use App\Http\Requests\Admin\CategoryUpdateRequest;
use App\Http\Requests\Admin\Image\CategoryUploadImageRequest;
use App\Http\Resources\Category\CategoryResource;
use App\Http\Resources\Category\CategoryResourceIndex;
use App\Models\Category;
use App\Models\CategoryLang;
use App\Services\Crud\Category\CategoryService;
use Debugbar;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

    /**
     * @param Request $request
     * @param null $parent_id
     * @return \Inertia\Response
     */
    public function index(Request $request,$parent_id = null): \Inertia\Response
    {

        abort_unless(Auth::user()->hasAnyRole(['admin']), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $data = $this->service->getItems($request,$parent_id);
        return Inertia::render('Categories/Index', [
            'categories' => CategoryResourceIndex::collection($data),
            'table_search' => $request->get('search'),
            'table_filter' => $request->get('filter'),
            'active_options' => createOptions(Category::ACTIVE,'All'),
        ]);
    }

    /**
     * @param CategoryCreateRequest $request
     * @return RedirectResponse
     */
    public function store(CategoryCreateRequest $request): RedirectResponse
    {
        try {
         $category =  $this->service->createItem($request);

         return redirect()->route('categories.edit', $category->id)->with('message',trans('messages.success.create'));
        }catch(\Exception $e){
            return redirect()->back()->with(['error' => $e->getMessage()]);
        }


    }

    /**
     * @param CategoryUpdateRequest $request
     * @param Category $category
     * @return RedirectResponse
     */
    public function update(CategoryUpdateRequest $request, Category $category): RedirectResponse

    {
        $this->service->updateItem($category,$request);
        return redirect()->route('categories.index')->with('message',trans('messages.success.update'));
    }
    /**
     * @return \Inertia\Response
     */
    public function create(): \Inertia\Response
    {
        return Inertia::render('Categories/Create', [
            'category' => CategoryResource::make(new Category()),
            'categories' => Category::with(['translation' => fn($q) =>$q->where('locale',app()->getLocale())])->get()->toTree(),
        ]);
    }

    /**
     * @param Category $category
     * @return \Inertia\Response
     */
    public function edit(Category $category): \Inertia\Response
    {
        return Inertia::render('Categories/Edit', [
            'category' => CategoryResource::make($category->load(['media']))->resolve(),
            'categories' => Category::with(['translation' => fn($q) =>$q->where('locale',app()->getLocale())])->where('id', '!=' , $category->id)->get()->toTree(),
        ]);
    }
    /**
     * @param Request $request
     */
    public function destroy(Request $request): RedirectResponse
    {
        Category::whereIn('id',$request->ids)->delete();
        return redirect()->route('categories.index')->with('message',trans('messages.success.delete'));
    }

    /**
     * @param Request $request
     */
    public function sort(Request $request)
    {
         $this->service->sortItem($request);
         return back()->with('message',trans('messages.success.sort'));
    }

    /**
     * @param Request $request
     */
    public function slug(Request $request): JsonResponse
    {
        $slug =  $this->service->setSlug(CategoryLang::class,'link_rewrite',$request->title);
        return response()->json(['slug' => $slug]);
    }

    /**
     * @param CategoryUploadImageRequest $request
     * @return JsonResponse
     */
    public  function storeMedia(CategoryUploadImageRequest $request): JsonResponse
    {

        return $this->saveMedia($request);
    }
}
