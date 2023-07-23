<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\AttributeCreateRequest;
use App\Http\Requests\Admin\AttributeGroupUpdateRequest;
use App\Http\Requests\Admin\AttributeUpdateRequest;
use App\Http\Resources\Attribute\AttributeResource;
use App\Http\Resources\Attribute\AttributeResourceIndex;
use App\Http\Resources\AttributeGroup\AttributeGroupResource;
use App\Models\Attribute;
use App\Models\AttributeGroup;
use App\Services\Crud\Attribute\AttributeService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Inertia\Inertia;

class AttributeController extends Controller
{

    private AttributeService $service;

    /**
     * @param AttributeService $attributeService
     */
    public function __construct(AttributeService   $attributeService)
    {
        $this->service = $attributeService;
    }

    /**
     * @param AttributeGroup $attributeGroup
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

    /**
     * @param AttributeGroup $attributeGroup
     * @param AttributeCreateRequest $request
     * @return RedirectResponse
     */
    public function store(AttributeGroup $attributeGroup,AttributeCreateRequest $request): \Illuminate\Http\RedirectResponse
    {
        $attribute =  $this->service->createItem($request);

        return redirect()->route('attribute_group.attribute.edit', [$attributeGroup->id,$attribute->id])->with('message',trans('messages.success.create'));
    }


    /**
     * @param AttributeGroup $attributeGroup
     * @param Attribute $attribute
     * @return \Inertia\Response
     */
    public function edit(AttributeGroup $attributeGroup,Attribute $attribute)
    {
        return Inertia::render('Attributes/Edit', [
            'attribute' => AttributeResource::make($attribute)->resolve(),
            'group_type_options' => createOptions(AttributeGroupResource::collection(AttributeGroup::all())->resolve()),
            'parent_route_id' => $attributeGroup->id,
        ]);
    }

    /**
     * @param AttributeUpdateRequest $request
     * @param AttributeGroup $attributeGroup
     * @param Attribute $attribute
     * @return RedirectResponse
     */
    public function update(AttributeGroup $attributeGroup, Attribute $attribute,AttributeUpdateRequest $request)
    {
        $this->service->updateItem($attribute,$request);
        return redirect()->route('attribute_group.show',$attributeGroup->id)->with('message',trans('messages.success.update'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function sort(Request $request): RedirectResponse
    {
        $this->service->sortItem($request);
        return back()->with('message',trans('messages.success.sort'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function destroy(Request $request): RedirectResponse
    {
        Attribute::whereIn('id',$request->ids)->delete();
        return back()->with('message',trans('messages.success.delete'));
    }
}
