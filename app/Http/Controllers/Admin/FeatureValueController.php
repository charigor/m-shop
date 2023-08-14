<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FeatureValueCreateRequest;
use App\Http\Requests\Admin\FeatureValueUpdateRequest;
use App\Http\Resources\Admin\Feature\FeatureResource;
use App\Http\Resources\Admin\FeatureValue\FeatureValueResource;
use App\Models\Feature;
use App\Models\FeatureProduct;
use App\Models\FeatureValue;
use App\Services\Crud\FeatureValue\FeatureValueService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FeatureValueController extends Controller
{
    private FeatureValueService $service;

    /**
     * @param FeatureValueService $featureValueService
     */
    public function __construct(FeatureValueService $featureValueService)
    {
        $this->service = $featureValueService;
    }

    /**
     * @param FeatureValue $featureValue
     * @return Response
     */
    public function create(Feature $feature): Response
    {
        return Inertia::render('FeatureValues/Create', [
            'feature_value' => FeatureValueResource::make(new FeatureValue()),
            'feature_options' => createOptions(FeatureResource::collection(Feature::all())->resolve()),
            'parent_route_id' => $feature->id,
        ]);
    }

    /**
     * @param Feature $feature
     * @param FeatureValueCreateRequest $request
     * @return RedirectResponse
     */
    public function store(Feature $feature,FeatureValueCreateRequest $request): \Illuminate\Http\RedirectResponse
    {
        $feature_value =  $this->service->createItem($request);

        return redirect()->route('feature.feature_value.edit', [$feature_value->feature_id,$feature_value->id])->with('message',trans('messages.success.create'));
    }


    /**
     * @param Feature $feature
     * @param FeatureValue $featureValue
     * @return Response
     */
    public function edit(Feature $feature,FeatureValue $featureValue): Response
    {
        return Inertia::render('FeatureValues/Edit', [
            'feature_value' => FeatureValueResource::make($featureValue)->resolve(),
            'feature_options' => createOptions(FeatureResource::collection(Feature::all())->resolve()),
            'parent_route_id' => $feature->id,
        ]);
    }

    /**
     * @param FeatureValueUpdateRequest $request
     * @param Feature $feature
     * @param FeatureValue $featureValue
     * @return RedirectResponse
     */
    public function update(Feature $feature, FeatureValue $featureValue,FeatureValueUpdateRequest $request): RedirectResponse
    {
        $featureValue = $this->service->updateItem($featureValue,$request);
        return redirect()->route('feature.show',$featureValue->feature_id)->with('message',trans('messages.success.update'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function destroy(Request $request): RedirectResponse
    {
        FeatureValue::whereIn('id',$request->ids)->delete();
        foreach($request->ids as $id){
            FeatureProduct::where('feature_value_id',$id)->delete();
        }

        return back()->with('message',trans('messages.success.delete'));
    }
}
