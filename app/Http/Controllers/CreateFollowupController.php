<?php

namespace App\Http\Controllers;

use App\Models\Center;
use Carbon\Carbon;
use App\Models\Lead;
use App\Models\Followup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CreateFollowupController extends Controller
{
    public function createFollowup(Request $request){
        // $leads = Lead::where('status', 'Created')->where('hospital_id', Auth::user()->hospital_id)->get();
        // foreach ($leads as $lead){
        //     Followup::create([
        //         'lead_id' => $lead->id,
        //         'followup_count' => 1,
        //         'scheduled_date' => Carbon::today(),
        //         'user_id' => $lead->assigned_to
        //     ]);

        //     $lead->followup_created = true;
        //     $lead->followup_created_at = Carbon::now();
        //     $lead->save();
        // }

        return redirect('/overview');
    }

    public function setLastAssigned(){
        $centers = Center::all();
        foreach($centers as $center){
            $agentIds = collect($center->agents())->pluck('id');
            if(count($agentIds) > 0){
                $center->last_assigned = $agentIds[0];
                $center->save();
            }
        }

        return redirect('/leads');
    }
}
