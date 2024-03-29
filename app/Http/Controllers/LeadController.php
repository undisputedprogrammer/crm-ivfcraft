<?php

namespace App\Http\Controllers;

use App\Helpers\PublicHelper;
use Carbon\Carbon;
use App\Models\Lead;
use App\Models\User;
use App\Models\Center;
use App\Models\Followup;
use App\Models\Remark;
use App\Models\Source;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Reader\Xls\RC4;
use Ynotz\SmartPages\Http\Controllers\SmartController;

class LeadController extends SmartController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function show($id)
    {
        $lead = Lead::where('id', $id)->with([
            'remarks',
            'followups',
            'assigned',
            'appointment',
            'chats',
            'hospital',
            'source'
        ])->get()->first();
        return $this->buildResponse('pages.lead-show', [
            'lead' => $lead,
            'doctors' => [],
            'messageTemplates' => []
        ]);
    }

    public function change(Request $request){
        $lead = Lead::find($request->lead_id);
        $lead->customer_segment = $request->customer_segment;
        $lead->save();
        return response()->json(['success'=>true, 'message'=>'Customer Segment Updated']);
    }

    public function changevalid(Request $request){
        $lead = Lead::find($request->lead_id);
        if($request->is_valid == true){
            $lead->is_valid = false;
            $lead->save();
            return response()->json(['success'=>true,'message'=>'Valid status set to false','is_valid'=>0]);
        }
        elseif($request->is_valid == false){
            $lead->is_valid = true;
            $lead->save();
            return response()->json(['success'=>true,'message'=>'Valid status set to true','is_valid'=>1]);
        }


        return response('Something went wrong',400);
    }

    public function changeGenuine(Request $request){
        $lead = Lead::find($request->lead_id);
        if($request->is_genuine == true){
            $lead->is_genuine = false;
            $lead->save();
            return response()->json(['success'=>true,'message'=>'Genuine status set to false','is_genuine'=>0]);
        }
        elseif($request->is_genuine == false){
            $lead->is_genuine = true;
            $lead->save();
            return response()->json(['success'=>true,'message'=>'Genuine status set to true','is_genuine'=>1]);
        }

        return response('Something went wrong',400);
    }

    public function answer(Request $request){
        $lead = Lead::find($request->lead_id);

        if($lead == null){
            return response()->json(['success'=>false,'message'=>'Lead not found']);
        }

        if($request->question == 'q_visit'){

            if($request->q_answer == 'null'){
                $lead->q_visit = null;
            }
            $lead->q_visit = $request->q_answer;

            if($lead->q_visit == 'yes'){
                $lead->customer_segment = 'hot';
            }elseif($lead->q_visit == 'no'){
                $lead->customer_segment = 'cold';
            }elseif($lead->q_visit == null || $lead->q_visit == 'null'){
                $lead->customer_segment = null;
            }
            $lead->save();

            return response()->json(['success'=>true, 'message'=>'Response Marked','q_visit'=>$lead->q_visit,'customer_segment'=>$lead->customer_segment,'answer'=>$request->q_answer]);
        }

        if($request->question == 'q_decide'){
            if($request->q_answer == 'null'){
                $lead->q_decide = '';
            }
            else{
                $lead->q_decide = $request->q_answer;
            }

            if($lead->q_decide == 'yes'){
                $lead->customer_segment = 'warm';
            }
            if($lead->q_decide == null || $lead->q_decide == 'null'){
                $lead->customer_segment = 'cold';
            }
            if($lead->q_decide == 'no'){
                $lead->customer_segment = 'cold';
            }

            $lead->save();

            return response()->json(['success'=>true, 'message'=>'Response Marked','q_decide'=>$lead->q_decide,'customer_segment'=>$lead->customer_segment,'answer'=>$request->q_answer]);


        }
    }

    public function close(Request $request){
        try {
            DB::beginTransaction();
            $lead = Lead::find($request->lead_id);

            if($lead ==  null){
                return response()->json(['success'=>false,'message'=>'Lead not found']);
            }

            // if($lead->status == 'Consulted'){
            //     $lead->status = 'Completed';
            // }
            // else{
            $lead->status = 'Closed';
            // }
            $lead->save();
            $followups = Followup::where('lead_id',$lead->id)->where('actual_date',null)->latest()->get();
            foreach ($followups as $followup) {
                if($followup != null){
                    $followup->actual_date = Carbon::now();
                    $followup->call_status = 'Responsive';
                    $followup->save();
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
        }
        $message = 'Lead closed successfully';

        return response()->json(['success'=>true, 'message'=> $message]);
    }

    public function update(Request $request)
    {
        $lead = Lead::find($request->lead_id);

        if($lead != null){
            $lead->name = $request->name;
            $lead->city = $request->city;
            $lead->email = $request->email;
            $lead->save();
            return response()->json(['success' => true, 'lead' => $lead, 'message' => 'Lead Updated Successfully']);
        }else{
            return response()->json(['success' => false, 'message' => 'Failed!, Could not update lead']);
        }
    }

    public function statusUpdate(Request $request)
    {
        $lead = Lead::find($request->lead_id);

        if($lead != null){
            $lead->status = $request->status;
            $lead->save();
            $followup = $this->createFollowup($lead, $request->followup_date);
            $today_date = Carbon::today()->format('M d, Y');
            if ($followup != null) {
                Remark::create([
                    'remarkable_type' => Followup::class,
                    'remarkable_id' => $followup->id,
                    'remark' => "Lead re-opened on $today_date: ".$request->remarks,
                    'user_id' => auth()->user()->id
                ]);
            }
            return response()->json(['success' => true, 'lead' => $lead, 'message' => 'Lead status Updated Successfully']);
        }else{
            return response()->json(['success' => false, 'message' => 'Failed!, Could not update lead']);
        }
    }

    public function setTreatmentStatus(Request $request){
        $lead = Lead::find($request->lead_id);
        $lead->treatment_status = $request->treatment_status;
        $lead->save();
        return response()->json(['success'=>true, 'treatment_status' => $lead->treatment_status]);
    }



    public function distribute(Request $request){
        if($request->selected_agents){
            $agent_ids = explode(",",$request->selected_agents);
        }else{
            return response()->json(['success'=>false, 'message'=>"No agents to distribute"]);
        }

        if($request->agent){
            $agent = User::find($request->agent);
            info('agent fecthed');
            $selected_agents = User::whereIn('id', $agent_ids)->get()->toArray();
            info('selected agents fetched');
            $agents_count = count($selected_agents);
            if($agents_count < 1){
                return response()->json(['success'=>false, 'message'=>'Not enough agents to assign'], 400);
            }
            $agent_leads = Lead::where('assigned_to',$agent->id)->whereNotIn('status',['Completed', 'Closed'])->get();

            $index = 0;
            foreach($agent_leads as $lead){
                $lead->assigned_to = $selected_agents[$index]['id'];
                $lead->save();
                if($index == $agents_count - 1 ){
                    $index = 0;
                }else{
                    $index++;
                }
            }
        }
        else{
            return response()->json(['success'=>false, 'message'=>"Could not find agent!"], 400);
        }

        return response()->json(['success'=>true, 'message'=>'Leads of '.$agent->name.' distributed to '.$agents_count.' agents']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|min:3',
            'city' => 'required|min:3',
            'phone' => 'required|min:10',
            'email' => 'required|email',
            'campaign' => 'required'
        ]);

        try{

            info('going to create lead');
            $center = Center::find($request->center);
            $agentIds = collect($center->agents())->pluck('id');
            // info('agent ids:');
            // info($agentIds);
            if(count($agentIds) < 1){
                return response()->json([
                    'success' => false,
                    'message' => "No agents available in the selected center",
                ], 400);
            }
            $last_assigned_index = array_search($center->last_assigned, $agentIds->toArray());

            // info("Last assigned index found: ".$last_assigned_index);

            if($last_assigned_index < count($agentIds)-1){
                $next_assign_index = $last_assigned_index + 1;
            }else{
                $next_assign_index = 0;
            }
            DB::beginTransaction();
            $lquery = Lead::where(
                'phone',
                PublicHelper::formatPhoneNumber($request->phone)
            )->where('hospital_id', auth()->user()->hospital_id);
            if (auth()->user()->hospital_id == 2) {
                $lquery->where('campaign', $request->campaign);
            }
            $existing_lead = $lquery->get()->first();
            // info('existing lead:');
            // info($existing_lead);
            if ($existing_lead != null){
                // info('lead exists');
                // if (auth()->user()->hospital_id != 1 && $existing_lead->campaign != $request->campaign) {
                //     $agentId = $existing_lead->assigned_to;
                // } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Lead creation failed: Duplicate entry.'
                    ]);
                // }
            } else {
                // info('new lead');
                $agentId = $agentIds[$next_assign_index];
                $center->last_assigned = $agentId;
                $center->save();
            }
            // info('agent id:');
            // info($agentId);
            $lead = Lead::create([
                'hospital_id' => Auth::user()->hospital_id,
                'center_id' => $request->center ? $request->center : User::find(Auth::user()->id)->centers()->first()->id,
                'name' => $request->name,
                'phone' => PublicHelper::formatPhoneNumber($request->phone),
                'email' => $request->email,
                'city' => $request->city,
                'assigned_to' => $agentId,
                'created_by' => Auth::user()->id,
                'source_id' => $request->source,
                'campaign' => ucwords(strtolower($request->campaign))
            ]);
            DB::commit();
        } catch(Exception $e) {
            DB::rollBack();
        }
        PublicHelper::checkAndStoreCampaign($request->campaign);

        $center->last_assigned = $agentIds[$next_assign_index];
        $center->save();

        $followup_created = $this->createFollowup($lead);

        if($followup_created != null){
            $lead->followup_created = true;
            // $lead->followup_created_at = Carbon::now();
            $lead->save();
        }else{
            $lead->delete();
            return response()->json([
                'success' => false,
                'message' => 'Could not create lead !'
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => "New lead created !",
            'lead' => $lead
        ], 200);
    }



    public function createFollowup($lead, $followup_date = null)
    {
        $pendingFolloup = DB::table('leads as l')
            ->join('followups as f', 'l.id', '=', 'f.lead_id')
            ->where('l.id', $lead->id)
            ->where('f.actual_date', null)
            ->get()->first();
        if (!isset($pendingFolloup)) {
            $followup = Followup::create([
                'lead_id' => $lead->id,
                'followup_count' => 1,
                'scheduled_date' => $followup_date != null ? Carbon::createFromFormat('Y-m-d', $followup_date) : Carbon::today(),
                'user_id' => $lead->assigned_to
            ]);
        } else {
            $followup = $pendingFolloup;
        }

        return $followup;
    }

    public function refer(Request $request){
        $request->validate([
            'name' => 'required|min:3',
            'city' => 'required|min:3',
            'phone' => 'required|min:10',
            'email' => 'required|email',
            'center' => 'required',
            'agent' => 'required'
        ]);

        $center = Center::find($request->center);
        $lquery = Lead::where(
                'phone',
                PublicHelper::formatPhoneNumber($request->phone)
            )->where('hospital_id', auth()->user()->hospital_id);
        if (auth()->user()->hospital_id == 2) {
            $lquery->where('campaign', $request->campaign);
        }
        $existing_lead = $lquery->get()->first();
        if ($existing_lead != null) {
            return response()->json([
                'success' => false,
                'message' => 'Lead creation failed: Duplicate entry.'
            ]);
        }
        if(!$request->source){
            return response()->json(['success'=>false, 'message' => 'Could not find source !']);
        }

        $source = Source::find($request->source);

        if(!$source){
            return response()->json(['success'=>false, 'message' => 'Could not find source !']);
        }

        $lquery = Lead::where(
            'phone',
            PublicHelper::formatPhoneNumber($request->phone)
        )->where('hospital_id', auth()->user()->hospital_id);
        if (auth()->user()->hospital_id == 2) {
            $lquery->where('campaign', $request->campaign);
        }
        $existing_lead = $lquery->get()->first();
        if ($existing_lead != null){
            if ($existing_lead->campaign != $request->campaign) {
                $agentId = $existing_lead->assigned_to;
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Could not create lead !'
                ]);
            }
        } else {
            $agentId = $request->agent;
        }
        $lead = Lead::create([
            'hospital_id' => Auth::user()->hospital_id,
            'center_id' => $request->center ? $request->center : User::find(Auth::user()->id)->centers()->first()->id,
            'name' => $request->name,
            'phone' => PublicHelper::formatPhoneNumber($request->phone),
            'email' => $request->email,
            'city' => $request->city,
            'assigned_to' => $agentId,
            'created_by' => Auth::user()->id,
            'source_id' => $source->id,
            'campaign' => ucwords(strtolower($request->campaign))
        ]);

        $followup_created = $this->createFollowup($lead);

        if($followup_created != null){
            $lead->followup_created = true;
            // $lead->followup_created_at = Carbon::now();
            $lead->save();
        }else{
            $lead->delete();
            return response()->json([
                'success' => false,
                'message' => 'Could not create lead !'
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => "Refered lead created !",
            'lead' => $lead
        ], 200);
    }
}
