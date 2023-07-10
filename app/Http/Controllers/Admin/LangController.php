<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LangCreateRequest;
use App\Http\Requests\Admin\LangUpdateRequest;
use App\Http\Resources\Lang\LangResource;
use App\Http\Resources\Lang\LangResourceIndex;
use App\Models\Lang;
use App\Services\Crud\Lang\LangService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class LangController extends Controller
{
    protected LangService $service;

    /**
     * @param LangService $langService
     */
    public function __construct(LangService $langService)
    {
        $this->service = $langService;

    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        abort_unless(Auth::user()->hasAnyRole(['admin']), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $data = $this->service->getItems($request);
        return Inertia::render('Langs/Index', [
            'langs' => LangResourceIndex::collection($data),
            'date_format_options' => createOptions(Lang::DATE_FORMAT,'All'),
            'date_format_full_options' => createOptions(Lang::DATE_FORMAT_FULL,'All'),
            'active_options' => createOptions(Lang::ACTIVE,'All'),
            'table_search' => $request->get('search'),
            'table_filter' => $request->get('filter')
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Langs/Create', [
            'lang' => LangResource::make(new Lang())->resolve(),
            'date_format_options' => createOptions(Lang::DATE_FORMAT),
            'date_format_full_options' => createOptions(Lang::DATE_FORMAT_FULL),
            'active_options' => createOptions(Lang::ACTIVE),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LangCreateRequest $request): \Illuminate\Http\RedirectResponse
    {
        $this->service->createItem($request);
        return redirect()->route('lang.index')->with('message',trans('messages.success.create'));
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Lang $lang)
    {
        return Inertia::render('Langs/Edit', [
            'lang' => LangResource::make($lang)->resolve(),
            'date_format_options' => createOptions(Lang::DATE_FORMAT),
            'date_format_full_options' => createOptions(Lang::DATE_FORMAT_FULL),
            'active_options' => createOptions(Lang::ACTIVE),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LangUpdateRequest $request, Lang $lang)
    {
        $this->service->updateItem($lang,$request);
        return redirect()->route('lang.index')->with('message',trans('messages.success.update'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $this->service->deleteItems($request);
        return redirect()->route('lang.index')->with('message',trans('messages.success.delete'));
    }
}
