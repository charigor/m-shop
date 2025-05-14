<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AttributeGroupCreateRequest;
use App\Http\Requests\Admin\AttributeGroupUpdateRequest;
use App\Http\Resources\Attribute\AttributeResourceIndex;
use App\Http\Resources\AttributeGroup\AttributeGroupResource;
use App\Http\Resources\AttributeGroup\AttributeGroupResourceIndex;
use App\Models\AttributeGroup;
use App\Services\Crud\AttributeGroup\AttributeGroupService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AttributeGroupController extends Controller
{
    private AttributeGroupService $service;

    public function __construct(AttributeGroupService $attributeGroupService)
    {
        $this->service = $attributeGroupService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): \Inertia\Response
    {
        abort_unless(Auth::user()->hasAnyRole(['admin']), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $data = $this->service->getItems($request);

        return Inertia::render('AttributeGroups/Index', [
            'attribute_groups' => AttributeGroupResourceIndex::collection($data),
            'table_search' => $request->get('search'),
            'table_filter' => $request->get('filter'),
            'group_type_options' => createOptions(AttributeGroup::GROUP_TYPE, 'All'),
            'is_color_group_options' => createOptions(AttributeGroup::IS_COLOR_GROUP, 'All'),
        ]);
    }

    public function create(): \Inertia\Response
    {
        return Inertia::render('AttributeGroups/Create', [
            'attribute_group' => AttributeGroupResource::make(new AttributeGroup),
            'group_type_options' => createOptions(AttributeGroup::GROUP_TYPE),
        ]);
    }

    public function store(AttributeGroupCreateRequest $request): RedirectResponse
    {

        $attribute_group = $this->service->createItem($request);

        return redirect()->route('attribute_group.edit', $attribute_group->id)->with('message', trans('messages.success.create'));

    }

    public function edit(AttributeGroup $attributeGroup): \Inertia\Response
    {
        return Inertia::render('AttributeGroups/Edit', [
            'attribute_group' => AttributeGroupResource::make($attributeGroup)->resolve(),
            'group_type_options' => createOptions(AttributeGroup::GROUP_TYPE),
        ]);
    }

    public function show(Request $request, AttributeGroup $attributeGroup): \Inertia\Response
    {

        $data = $this->service->getAttributeItems($request, $attributeGroup);

        return Inertia::render('Attributes/Index', [
            'attributes' => AttributeResourceIndex::collection($data),
            'table_search' => $request->get('search'),
            'table_filter' => $request->get('filter'),
        ]);
    }

    public function update(AttributeGroupUpdateRequest $request, AttributeGroup $attributeGroup): RedirectResponse
    {

        $this->service->updateItem($attributeGroup, $request);

        return redirect()->route('attribute_group.index')->with('message', trans('messages.success.update'));
    }

    public function sort(Request $request): RedirectResponse
    {
        $this->service->sortItem($request);

        return back()->with('message', trans('messages.success.sort'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        AttributeGroup::whereIn('id', $request->ids)->delete();

        return redirect()->route('attribute_group.index')->with('message', trans('messages.success.delete'));
    }
}
