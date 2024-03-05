<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Lead;
use App\Models\Followup;
use App\Models\Remark;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Ynotz\SmartPages\Http\Controllers\SmartController;

class FollowupController extends SmartController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function initiate(Request $request)
    {
        $lead = Lead::find($request->lead_id);
        $converted = null;

        $current_followup = Followup::where('lead_id',$lead->id)->where('actual_date', null)->get()->first();
        $current_followup->actual_date = Carbon::now();
        $current_followup->next_followup_date = Carbon::createFromFormat('Y-m-d',$request->scheduled_date);
        $current_followup->user_id = Auth::user()->id;
        $current_followup->call_status = $request->call_status;
        $current_followup->save();
        $pendingFolloup = DB::table('leads as l')
            ->join('followups as f', 'l.id', '=', 'f.lead_id')
            ->where('l.id', $lead->id)
            ->where('f.actual_date', null)
            ->get()->first();
        if (!isset($pendingFolloup)) {
            $followup = Followup::create([
                'lead_id' => $request->lead_id,
                'followup_count' => $current_followup->followup_count + 1,
                'scheduled_date' => $current_followup->next_followup_date,
                'user_id' => $request->user()->id
            ]);
        } else {
            $followup = $pendingFolloup;
        }
        $lead->followup_created_at = Carbon::now();
        $lead->followup_created = true;
        $lead->status = "Follow-up Started";

        if($lead->call_status != "Responsive"){
            $lead->call_status = $request->call_status;
        }

        $lead->save();
        return response()->json(['success' => true, 'message' => 'Follow up has been initiated for this lead', 'followup' => $followup, 'completed_followup' => $current_followup, 'lead' => $lead]);
        // return response()->json(['success'=>true,'message'=>'converted '.$followup->converted]);
    }

    public function store(Request $request)
    {

        $followup_remark = Remark::create([
            'remarkable_type' => Followup::class,
            'remarkable_id' => $request->followup_id,
            'remark' => $request->remark,
            'user_id' => $request->user()->id
        ]);

        return response()->json(['success' => true, 'message' => 'Remark added', 'followup_remark' => $followup_remark]);
    }

    public function next(Request $request)
    {

        $followup = Followup::find($request->followup_id);
        $followup->actual_date = Carbon::now();
        $followup->next_followup_date = Carbon::createFromFormat('Y-m-d', $request->next_followup_date)->format('Y-m-d H:i:s');
        $followup->user_id = Auth::user()->id;
        $followup->call_status = $request->call_status;
        $followup->save();
        $converted = null;

        $followup->refresh();

        $pendingFolloup = DB::table('leads as l')
            ->join('followups as f', 'l.id', '=', 'f.lead_id')
            ->where('l.id', $request->lead_id)
            ->where('f.actual_date', null)
            ->get()->first();
        if (!isset($pendingFolloup)) {
            $next_followup = Followup::create([
                'lead_id' => $request->lead_id,
                'followup_count' => $followup->followup_count + 1,
                'scheduled_date' => $followup->next_followup_date,
                'converted' => $followup->converted,
                'consulted' => $followup->consulted,
                'user_id' => $request->user()->id
            ]);
        } else {
            $next_followup = $pendingFolloup;
        }

        $lead = Lead::find($request->lead_id);
        if($lead->call_status != "Responsive"){
            $lead->call_status = $request->call_status;
        }
        if ($request->lead_status != null) {
            $lead->status = $request->lead_status;
        }
        $lead->save();

        return response()->json(['success' => true, 'message' => 'Next follow up scheduled', 'followup' => $followup, 'next_followup' => $next_followup, 'remarks' => $followup->remarks, 'lead' => $lead]);
    }

    public function convert(Request $request)
    {
        $followup = Followup::find($request->followup_id);
        $followup->converted = true;
        $followup->save();
        $lead = Lead::find($request->lead_id);
        $lead->status = 'Appointment Fixed';
        $lead->save();
        return response()->json(['success' => true, 'message' => 'Lead converted to customer', 'followup' => $followup, 'lead' => $lead]);
    }


}
