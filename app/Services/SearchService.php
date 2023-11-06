<?php

namespace App\Services;

use DB;
use App\Models\Followup;
use Illuminate\Support\Facades\Auth;

class SearchService
{

    public function getResults($request)
    {
        $user = $request->user();

        if ($request->search_type == 'actual_date')
        {
            $query = Followup::where('actual_date', '!=', null);

        }
        else {
            $query = Followup::query();

        }

        $query->where($request->search_type, '>=', $request->from_date)
        ->where($request->search_type, '<=', $request->to_date)
        ->with(['lead' => function ($q) {
            return $q->with(['appointment','assigned']);
        }, 'remarks', 'user']);

        $filters = [
            'is_valid' => 'is_valid',
            'is_genuine' => 'is_genuine',
            'lead_status' => 'status',
            'agent' => 'assigned_to',
            'center' => 'center_id',
            'call_status' => 'call_status'
        ];

        foreach ($filters as $param => $column) {
            if ($request->$param !== null && $request->$param != 'null') {
                $query->whereHas('lead', function ($query) use ($request, $param, $column) {
                    $query->where($column, $request->$param);
                });
            }
        }



        if(!$request->user()->hasRole('admin')){
            $query->whereHas('lead', function ($query){
                $query->where('assigned_to',Auth::user()->id);
            });
        }elseif($request->user()->hasRole('admin')){
            $query->whereHas('lead', function($q) {
                return $q->where('hospital_id', auth()->user()->hospital_id);
            });
        }

        $followups = $query->paginate(10);

        $table = view('partials.search-results-table', compact('followups'))->render();


        return [
            'success' => true,
            'message' => 'Search successful',
            'followups' => $followups,
            'table_html' => $table,
            'pagination_type' => $request->search_type,
            'request' => $request->is_valid,
        ];
    }
}
