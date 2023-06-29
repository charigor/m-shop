<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Services\Datatables\Categories\Categories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends Controller
{

    /**
     * @param Request $request
     * @return \Inertia\Response
     */
    public function index(Request $request,$parent_id = null) {

        abort_unless(Auth::user()->hasAnyRole(['admin']), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return Inertia::render('Categories/Index', [
            'categories' => CategoryResource::collection((new Categories)->table($request,$parent_id)),
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
        $data = $request->all();
        $category = Category::create($data);
        if(!empty($data['parent']))
        {
           $node =  Category::find($data['parent']);
           $node->appendNode($category);
        }
        if (!empty($data['image'])) {
            $image = array_column($data['image'], 'image')[0];
            $path = Storage::path($image);
            $category->addMedia($path)->toMediaCollection('images');
        }
        return redirect()->route('category.edit', $category->id);

    }

    public function update(Request $request, Category $category)

    {

        $data = $request->all();

        $category->update($data);

        if(!empty($data['parent']))
        {
            $node =  Category::find($data['parent']);
            $node->appendNode($category);
        }else{
            $category->saveAsRoot();
        }
        info($data['image']);
        if (!empty($data['image']) && count($data['image']) >= 1) {
            $image= array_column($data['image'], 'image');
            if(isset($image[0])){
                $category->clearMediaCollection('images');
                $path = Storage::path($image[0]);
                $category->addMedia($path)->toMediaCollection('images');
            }else{
                $category->clearMediaCollection('images');
            }
        }
        return redirect()->route('category.index');
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
        return redirect()->route('category.index');
    }
    public function sort(Request $request){

        $data = $request->all();

        if($data['id'] === 'categories')
        {
            Category::rebuildTree($data['el']);
        }else{
            $node =  Category::find($data['id']);
             Category::rebuildSubtree($node,$data['el']);
        }

        return response()->noContent();
    }
}
