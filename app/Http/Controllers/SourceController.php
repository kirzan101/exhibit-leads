<?php

namespace App\Http\Controllers;

use App\Http\Requests\SourceFormRequest;
use App\Http\Resources\SourceResource;
use App\Models\Source;
use App\Services\SourceService;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;

use function Termwind\render;

class SourceController extends Controller
{
    private SourceService $sourceService;

    public function __construct(SourceService $sourceService)
    {
        $this->sourceService = $sourceService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('read', Source::class);

        $sources = $this->sourceService->indexSource();

        return Inertia::render('Sources/IndexSource', [
            'sources' => SourceResource::collection($sources),
            'per_page' => 5
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Source::class);

        return Inertia::render('Sources/CreateSource');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SourceFormRequest $request)
    {
        $this->authorize('create', Source::class);

        ['result' => $result, 'message' => $message] = $this->sourceService->createSource($request->toArray());

        return redirect()->route('sources.index')->with($result, $message);
    }

    /**
     * Display the specified resource.
     */
    public function show(Source $source)
    {
        $this->authorize('read', Source::class);

        $source = $this->sourceService->showSource($source);

        return Inertia::render('Sources/ShowSource', [
            'source' => new SourceResource($source)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Source $source)
    {
        $this->authorize('update', Source::class);

        $source = $this->sourceService->showSource($source);

        return Inertia::render('Sources/EditSource', [
            'source' => new SourceResource($source)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SourceFormRequest $request, Source $source)
    {
        ['result' => $result, 'message' => $message] = $this->sourceService->updateSource($request->toArray(), $source);

        return redirect()->route('sources.index')->with($result, $message);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Source $source)
    {
        ['result' => $result, 'message' => $message] = $this->sourceService->deleteSource($source);

        return redirect()->route('sources.index')->with($result, $message);
    }
}
