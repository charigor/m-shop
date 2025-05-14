<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Traits\MediaUploadingTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BrandCreateRequest;
use App\Http\Requests\Admin\BrandUpdateRequest;
use App\Http\Requests\Admin\Image\BrandUploadImageRequest;
use App\Http\Resources\Brand\BrandResource;
use App\Http\Resources\Brand\BrandResourceIndex;
use App\Models\Brand;
use App\Services\Crud\Brand\BrandService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class BrandController extends Controller
{
    use MediaUploadingTrait;

    private BrandService $service;

    public function __construct(BrandService $brandService)
    {
        $this->service = $brandService;

    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): \Inertia\Response
    {
        abort_unless(Auth::user()->hasAnyRole(['admin']), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $data = $this->service->getItems($request);

        return Inertia::render('Brands/Index', [
            'brands' => BrandResourceIndex::collection($data),
            'table_search' => $request->get('search'),
            'table_filter' => $request->get('filter'),
            'active_options' => createOptions(Brand::ACTIVE, 'All'),
        ]);
    }

    public function create(): \Inertia\Response
    {
        $response = [
            'brand' => BrandResource::make(new Brand),
        ];

        return Inertia::render('Brands/Create', $response);
    }

    public function store(BrandCreateRequest $request): RedirectResponse
    {

        $brand = $this->service->createItem($request);

        return redirect()->route('brand.edit', $brand->id)->with('message', trans('messages.success.create'));

    }

    public function edit(Brand $brand): \Inertia\Response
    {
        $response = [
            'brand' => BrandResource::make($brand->load('media'))->resolve(),
        ];

        return Inertia::render('Brands/Edit', $response);
    }

    public function update(BrandUpdateRequest $request, Brand $brand): RedirectResponse
    {
        $this->service->updateItem($brand, $request);

        return redirect()->route('brand.index')->with('message', trans('messages.success.update'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $this->service->deleteItems($request);

        return redirect()->route('brand.index')->with('message', trans('messages.success.delete'));
    }

    public function storeMedia(BrandUploadImageRequest $request): JsonResponse
    {
        return $this->saveMedia($request);
    }
}
