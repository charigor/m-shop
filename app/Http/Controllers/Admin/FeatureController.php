<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FeatureCreateRequest;
use App\Http\Requests\Admin\FeatureUpdateRequest;
use App\Http\Resources\Admin\Feature\FeatureResource;
use App\Http\Resources\Admin\Feature\FeatureTableResource;
use App\Http\Resources\Admin\FeatureValue\FeatureValueResource;
use App\Models\Feature;
use App\Models\FeatureValueProduct;
use App\Models\Product;
use App\Models\User;
use App\Services\Crud\Feature\FeatureService;
use App\Services\Crud\FeatureValue\FeatureValueService;
use App\Services\Test\ConcreateCalss;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class FeatureController extends Controller
{
    private FeatureService $service;
    /**
     * @param FeatureService $featureService
     */
    public function __construct(FeatureService $featureService)
    {
        $this->service = $featureService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        abort_unless(Auth::user()->hasAnyRole(['admin']), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $data = $this->service->getItems($request);
        return Inertia::render('Features/Index', [
            'features' => FeatureTableResource::collection($data),
            'table_search' => $request->get('search'),
            'table_filter' => $request->get('filter')
        ]);
    }

    /**
     * @return \Inertia\Response
     */
    public function create(): \Inertia\Response
    {
        return Inertia::render('Features/Create', [
            'attribute_group' => FeatureResource::make(new Feature()),
        ]);
    }


    public function store(FeatureCreateRequest $request)
    {

        $feature =  $this->service->createItem($request);

        return redirect()->route('feature.edit', $feature->id)->with('message',trans('messages.success.create'));
    }

    public function show(Request $request,Feature $feature): \Inertia\Response
    {

        $data = (new FeatureValueService())->getItems($request,$feature);
        return Inertia::render('FeatureValues/Index', [
            'feature_values' => FeatureValueResource::collection($data),
            'table_search' => $request->get('search'),
            'table_filter' => $request->get('filter'),
        ]);
    }

    /**
     * @param Feature $feature
     * @return \Inertia\Response
     */
    public function edit(Feature $feature): \Inertia\Response
    {
        return Inertia::render('Features/Edit', [
            'feature' => FeatureResource::make($feature)->resolve()
        ]);
    }

    /**
     * @param FeatureUpdateRequest $request
     * @param Feature $feature
     * @return RedirectResponse
     */
    public function update(FeatureUpdateRequest $request, Feature $feature): RedirectResponse
    {

        $this->service->updateItem($feature,$request);
        return redirect()->route('feature.index')->with('message',trans('messages.success.update'));
    }
    /**
     * @param Request $request
     */
    public function sort(Request $request): RedirectResponse
    {
        $this->service->sortItem($request);
        return back()->with('message',trans('messages.success.sort'));
    }

    /**
     * @param Request $request
     * @return void
     */
    public function destroy(Request $request): void
    {
        $this->service->deleteItems($request);
        to_route('feature.index')->with('message',trans('messages.success.delete'));
    }

}
