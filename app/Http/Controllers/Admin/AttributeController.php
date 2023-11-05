<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AttributeCreateRequest;
use App\Http\Requests\Admin\AttributeUpdateRequest;
use App\Http\Resources\Attribute\AttributeResource;
use App\Http\Resources\AttributeGroup\AttributeGroupResource;
use App\Models\Attribute;
use App\Models\AttributeGroup;
use App\Services\Crud\Attribute\AttributeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AttributeController extends Controller
{
    private AttributeService $service;

    public function __construct(AttributeService $attributeService)
    {
        $this->service = $attributeService;
    }

    /**
     * @return \Inertia\Response
     */
    public function create(AttributeGroup $attributeGroup)
    {
        return Inertia::render('Attributes/Create', [
            'attribute' => AttributeResource::make(new Attribute()),
            'group_type_options' => createOptions(AttributeGroupResource::collection(AttributeGroup::all())->resolve()),
            'parent_route_id' => $attributeGroup->id,
        ]);
    }

    public function store(AttributeGroup $attributeGroup, AttributeCreateRequest $request): RedirectResponse
    {
        $attribute = $this->service->createItem($request);

        return redirect()->route('attribute_group.attribute.edit', [$attributeGroup->id, $attribute->id])->with('message', trans('messages.success.create'));
    }

    /**
     * @return \Inertia\Response
     */
    public function edit(AttributeGroup $attributeGroup, Attribute $attribute)
    {
        return Inertia::render('Attributes/Edit', [
            'attribute' => AttributeResource::make($attribute)->resolve(),
            'group_type_options' => createOptions(AttributeGroupResource::collection(AttributeGroup::all())->resolve()),
            'parent_route_id' => $attributeGroup->id,
        ]);
    }

    /**
     * @return RedirectResponse
     */
    public function update(AttributeGroup $attributeGroup, Attribute $attribute, AttributeUpdateRequest $request)
    {
        $this->service->updateItem($attribute, $request);

        return redirect()->route('attribute_group.show', $attributeGroup->id)->with('message', trans('messages.success.update'));
    }

    public function sort(Request $request): RedirectResponse
    {
        $this->service->sortItem($request);

        return back()->with('message', trans('messages.success.sort'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Attribute::whereIn('id', $request->ids)->delete();

        return back()->with('message', trans('messages.success.delete'));
    }
}
