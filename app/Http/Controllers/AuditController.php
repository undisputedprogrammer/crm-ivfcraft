<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function fetch(Request $request)
    {
        if($request->month != null){
            $date = Carbon::createFromFormat('Y-m',$request->month);
            $month = $date->format('m');
            $year = $date->format('Y');
        }
        else{
            $today = Carbon::now();
            $month = $today->format('m');
            $year = $today->format('Y');
        }

        $audits = Audit::where('user_id',$request->user_id)->whereYear('created_at',$year)->whereMonth('created_at',$month)->get();
        return response($audits);
    }
}
