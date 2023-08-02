<?php

namespace App\Services;

use App\Models\Source;
use Illuminate\Database\Eloquent\Collection;

class SourceService
{
    /**
     * index of source service
     *
     * @return Collection
     */
    public function indexSource() : Collection
    {
        $sources = Source::orderBy('id', 'desc')->get();

        return $sources;
    }

    /**
     * create source service
     *
     * @param array $request
     * @return Source
     */
    public function createSource(array $request) : Source
    {
        $source = Source::create($request);
        
        return $source;
    }

    /**
     * update source service
     *
     * @param array $request
     * @param Source $source
     * @return Source
     */
    public function updateSource(array $request, Source $source) : Source
    {
        $source = tap($source)->update($request);

        return $source;
    }

    /**
     * delete source service
     *
     * @param Source $source
     * @return boolean
     */
    public function deleteSource(Source $source) : bool
    {
        $source = $source->delete();

        return $source;
    }

    /**
     * show service resource
     *
     * @param Source $source
     * @return Source
     */
    public function showSource(Source $source) : Source
    {
        return $source;
    }
}