<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FeatureValueCreateRequest;
use App\Http\Requests\Admin\FeatureValueUpdateRequest;
use App\Http\Resources\Admin\Feature\FeatureResource;
use App\Http\Resources\Admin\FeatureValue\FeatureValueResource;
use App\Models\Feature;
use App\Models\FeatureValue;
use App\Services\Crud\FeatureValue\FeatureValueService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FeatureValueController extends Controller
{
    private FeatureValueService $service;

    public function __construct(FeatureValueService $featureValueService)
    {
        $this->service = $featureValueService;
    }

    /**
     * @param  FeatureValue  $featureValue
     */
    public function create(Feature $feature): Response
    {
        return Inertia::render('FeatureValues/Create', [
            'feature_value' => FeatureValueResource::make(new FeatureValue()),
            'feature_options' => createOptions(FeatureResource::collection(Feature::all())->resolve()),
            'parent_route_id' => $feature->id,
        ]);
    }

    public function store(Feature $feature, FeatureValueCreateRequest $request): RedirectResponse
    {
        $feature_value = $this->service->createItem($request);

        return redirect()->route('feature.feature_value.edit', [$feature_value->feature_id, $feature_value->id])->with('message', trans('messages.success.create'));
    }

    public function edit(Feature $feature, FeatureValue $featureValue): Response
    {
        return Inertia::render('FeatureValues/Edit', [
            'feature_value' => FeatureValueResource::make($featureValue)->resolve(),
            'feature_options' => createOptions(FeatureResource::collection(Feature::all())->resolve()),
            'parent_route_id' => $feature->id,
        ]);
    }

    public function update(Feature $feature, FeatureValue $featureValue, FeatureValueUpdateRequest $request): RedirectResponse
    {
        $featureValue = $this->service->updateItem($featureValue, $request);

        return redirect()->route('feature.show', $featureValue->feature_id)->with('message', trans('messages.success.update'));
    }

    public function destroy(Request $request): void
    {
        $this->service->deleteItems($request);

        to_route('feature.index')->with('message', trans('messages.success.delete'));
    }
}
