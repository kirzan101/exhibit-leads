<?php

namespace App\Services;

use App\Helpers\Helper;
use App\Models\ActivityLog;
use App\Models\Source;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SourceService
{
    public $last_id = null;
    public $module_name = 'sources';

    /**
     * index of source service
     *
     * @return Collection
     */
    public function indexSource(): Collection
    {
        $sources = Source::orderBy('id', 'desc')->get();

        return $sources;
    }

    /**
     * create source service
     *
     * @param array $request
     * @return array
     */
    public function createSource(array $request): array
    {
        try {
            DB::beginTransaction();

            $source = Source::create($request);
            $this->last_id = $source->id;

            $return_values = ['result' => 'success', 'message' => 'Successfully saved!', 'subject' => $this->last_id];
        } catch (Exception $e) {
            DB::rollBack();

            $return_values = ['result' => 'error', 'message' => $e->getMessage(), 'subject' => $this->last_id];
        }
        DB::commit();

        ActivityLog::create([
            'name' => $this->module_name,
            'description' => $return_values['message'],
            'event' => 'create',
            'status' => $return_values['result'],
            'browser' => json_encode(Helper::deviceInfo()),
            'properties' => json_encode($request),
            'causer_id' => Auth::user()->id,
            'subject_id' => $return_values['subject']
        ]);

        return $return_values;
    }

    /**
     * update source service
     *
     * @param array $request
     * @param Source $source
     * @return array
     */
    public function updateSource(array $request, Source $source): array
    {
        try {
            DB::beginTransaction();

            $source = tap($source)->update($request);
            $this->last_id = $source->id;

            $return_values = ['result' => 'success', 'message' => 'Successfully updated!', 'subject' => $this->last_id];
        } catch (Exception $e) {
            DB::rollBack();

            $return_values = ['result' => 'error', 'message' => $e->getMessage(), 'subject' => $this->last_id];
        }
        DB::commit();

        ActivityLog::create([
            'name' => $this->module_name,
            'description' => $return_values['message'],
            'event' => 'update',
            'status' => $return_values['result'],
            'browser' => json_encode(Helper::deviceInfo()),
            'properties' => json_encode($request),
            'causer_id' => Auth::user()->id,
            'subject_id' => $return_values['subject']
        ]);

        return $return_values;
    }

    /**
     * delete source service
     *
     * @param Source $source
     * @return array
     */
    public function deleteSource(Source $source): array
    {
        try {
            DB::beginTransaction();

            $this->last_id = $source->id;
            $source = $source->delete();

            $return_values = ['result' => 'success', 'message' => 'Successfully deleted!', 'subject' => $this->last_id];
        } catch (Exception $e) {
            DB::rollBack();

            $return_values = ['result' => 'error', 'message' => $e->getMessage(), 'subject' => $this->last_id];
        }
        DB::commit();

        ActivityLog::create([
            'name' => $this->module_name,
            'description' => $return_values['message'],
            'event' => 'delete',
            'status' => $return_values['result'],
            'browser' => json_encode(Helper::deviceInfo()),
            'properties' => '{"source_id":' . $this->last_id . '}',
            'causer_id' => Auth::user()->id,
            'subject_id' => $return_values['subject']
        ]);

        return $return_values;
    }

    /**
     * show service resource
     *
     * @param Source $source
     * @return Source
     */
    public function showSource(Source $source): Source
    {
        return $source;
    }
}
