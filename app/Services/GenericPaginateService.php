<?php

namespace App\Services;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Facades\DB;

class GenericPaginateService
{
    /**
     * Get the paginated of table
     *
     * @param string $table
     * @param array $request
     * @return Paginator
     */
    public function index(string $table,array $request) : Paginator
    {
        $query = DB::table($table);

        //set default values
        $per_page = (array_key_exists('per_page', $request) && $request['per_page'] != null) ? (int)$request['per_page'] : 5;
        $sort_by = (array_key_exists('sort_by', $request) && $request['sort_by'] != null) ? $request['sort_by'] : 'id';
        $sort = 'desc';
        if (array_key_exists('is_sort_desc', $request) && $request['is_sort_desc'] != null) {
            $sort = ($request['is_sort_desc'] == 'true') ? 'desc' : 'asc';
        }

        // search filter
        if (array_key_exists('search', $request) && !empty($request['search'])) {
            $query->where(function ($query) use ($request) {
                $query->where('name', 'LIKE', '%' . $request['search'] . '%');
            });
        }

        return $query->orderBy($sort_by, $sort)->paginate($per_page);
    }
}