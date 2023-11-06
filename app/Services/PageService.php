<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Lead;
use App\Models\User;
use App\Models\Center;
use App\Models\Doctor;
use App\Models\Journal;
use App\Models\Message;
use App\Models\Followup;
use App\Models\Hospital;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class PageService
{
    public function getLeads($user, $selectedLeads, $selectedCenter, $search, $status, $is_valid, $is_genuine, $creation_date, $processed)
    {
        if($search != null){
            $leadsQuery = Lead::with(['followups' => function ($qr) {
                return $qr->with(['remarks']);
            }, 'appointment'])->where('hospital_id', $user->hospital_id)->where('name', 'like', '%' . $search . '%')
            ->orWhere('phone', 'like', '%' . $search . '%');

            $leadsQuery->when($user->hasRole('agent'), function ($query) use ($user) {
                return $query->where('assigned_to', $user->id);
            });

            return $this->returnLeads($user,$selectedLeads,$selectedCenter,$leadsQuery,$status, $creation_date, $processed);
        }

        if($creation_date != null){
            $leadsQuery = Lead::with(['followups' => function ($qr) {
                return $qr->with(['remarks']);
            }, 'appointment'])->where('hospital_id', $user->hospital_id)->whereDate('created_at',$creation_date);

            $leadsQuery->when($user->hasRole('agent'), function ($query) use ($user) {
                return $query->where('assigned_to', $user->id);
            });

            return $this->returnLeads($user,$selectedLeads,$selectedCenter,$leadsQuery,$status,$creation_date, $processed);
        }

        if($processed != null){
            info('processed is present');
            $today = Carbon::now()->toDateString();
            $leadsQuery = Lead::with(['followups' => function ($qr) {
                return $qr->with(['remarks']);
            }, 'appointment'])->where('hospital_id', $user->hospital_id)->whereDate('followup_created_at',$today);

            $leadsQuery->when($user->hasRole('agent'), function ($query) use ($user) {
                return $query->where('assigned_to', $user->id);
            });

            return $this->returnLeads($user,$selectedLeads,$selectedCenter,$leadsQuery,$status,$creation_date, $processed);
        }


        if($status != null && $status != 'none')
        {
            if($status == 'all'){
                $leadsQuery = Lead::with(['followups' => function ($qr) {
                    return $qr->with(['remarks']);
                }, 'appointment'])->where('hospital_id', $user->hospital_id);
            }
            else{
                $leadsQuery = Lead::with(['followups' => function ($qr) {
                    return $qr->with(['remarks']);
                }, 'appointment'])->where('hospital_id', $user->hospital_id)->where('status', $status);
            }

        }
        else{
            $leadsQuery = Lead::with(['followups' => function ($qr) {
                return $qr->with(['remarks']);
            },'appointment'])->where('hospital_id', $user->hospital_id)->where('status', '=', 'Created');
        }

        $leadsQuery->when($user->hasRole('agent'), function ($query) use ($user) {
            return $query->where('assigned_to', $user->id);
        });

        if($selectedCenter != null && $selectedCenter != 'all'){
            $leadsQuery->where('center_id',$selectedCenter);
        }

        if($is_valid != null){
            if($is_valid == 'true'){
                $leadsQuery->where('is_valid', true);
            }else{
                $leadsQuery->where('is_valid', false);
            }

        }

        if($is_genuine != null){
            if($is_genuine == 'true'){
                $leadsQuery->where('is_genuine', true);
            }else{
                $leadsQuery->where('is_genuine', false);
            }
        }



        $leads = $leadsQuery->paginate(30);

        $doctors = Doctor::all();
        $messageTemplates = Message::all();
        $centers = Center::where('hospital_id',$user->hospital_id)->get();

        if($selectedLeads != null){
            return compact('leads', 'doctors', 'messageTemplates','selectedLeads','centers','selectedCenter','status', 'is_valid', 'is_genuine');
        }
        else{
            return compact('leads', 'doctors', 'messageTemplates','centers','selectedCenter','status', 'is_valid', 'is_genuine');
        }

    }

    public function returnLeads($user, $selectedLeads, $selectedCenter, $leadsQuery, $status,$creation_date, $processed)
    {
        $leads = $leadsQuery->paginate(30);
        $doctors = Doctor::all();
        $messageTemplates = Message::all();
        $centers = Center::where('hospital_id',$user->hospital_id)->get();

        if($selectedLeads != null){
            return compact('leads', 'doctors', 'messageTemplates','selectedLeads','centers','selectedCenter','status');
        }
        elseif($creation_date != null){
            return compact('leads', 'doctors', 'messageTemplates','selectedLeads','centers','selectedCenter','status','creation_date');
        }
        elseif($processed != null){
            info('sending processed leads');
            return compact('leads', 'doctors', 'messageTemplates','selectedLeads','centers','selectedCenter','status','processed');
        }
        else{
            return compact('leads', 'doctors', 'messageTemplates','centers','selectedCenter','status');
        }
    }

    public function getOverviewData($month = null, $userId = null)
    {
        info('inside getoverviewdata function');
        if (isset($month)) {
            info('month is available '.$month);
            $searchedDate = Carbon::createFromFormat('Y-m',$month);
            $currentMonth = $searchedDate->format('m');
            $currentYear = $searchedDate->format('Y');
            $date = $searchedDate->format('Y-m-j');
        } else {
            $now = Carbon::now();
            $date = $now->format('Y-m-j');
            $currentMonth = $now->format('m');
            $currentYear = $now->format('Y');
        }
        $hospital = auth()->user()->hospital;
        $hospitals = [$hospital];
        $centers = $hospitals[0]->centers;

        if (isset($userId)) {
            /**
             * @var User
             */
            $authUser = User::find($userId);
        } else {
            /**
             * @var User
             */
            $authUser = auth()->user();
        }
        if($authUser->hasRole('admin')) {
            $lpm = Lead::forHospital($hospital->id)->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->count();

            $ftm = Lead::forHospital($hospital->id)->where('status', '<>', 'Created')->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->count();

            $lcm = Lead::forHospital($hospital->id)->where('status', 'Consulted')->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->count();


            $pf = Followup::whereHas('lead', function ($query) use($hospital){
                $query->where('hospital_id', $hospital->id);
            })->where('actual_date', null)->count();

        } else {
            $lpm = Lead::forAgent($authUser->id)->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->count();

            $ftm = Lead::forAgent($authUser->id)->where('status', '<>', 'Created')->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->count();

            $lcm = Lead::forAgent($authUser->id)->where('status', 'Consulted')->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->count();


            $pf = Followup::whereHas('lead', function ($query) use($authUser){
                $query->where('assigned_to', $authUser->id);
            })->where('actual_date', null)->count();
        }
        $journal = Journal::where('user_id',auth()->user()->id)->where('date',$date)->get()->first();
        // $process_chart_data = $this->getProcessChartData($currentMonth);
        $process_chart_data = json_encode($this->getProcessChartData($currentMonth));
        $valid_chart_data = json_encode($this->getValidChartData($currentMonth));
        $genuine_chart_data = json_encode($this->getGenuineChartData($currentMonth));
        return compact('lpm', 'ftm', 'lcm', 'pf', 'hospitals', 'centers','journal','process_chart_data','valid_chart_data','genuine_chart_data');
    }

    public function agentsPerformance($month)
    {
        if (isset($month)) {
            $searchedDate = Carbon::createFromFormat('Y-m',$month);
            $currentMonth = $searchedDate->format('m');
            $currentYear = $searchedDate->format('Y');
            $date = $searchedDate->format('Y-m-j');
        } else {
            $now = Carbon::now();
            $date = $now->format('Y-m-j');
            $currentMonth = $now->format('m');
            $currentYear = $now->format('Y');
        }
        $hospital = auth()->user()->hospital;
        $lpm = Lead::forHospital($hospital->id)->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->select('assigned_to', DB::raw('count(leads.id) as count'))->groupBy('assigned_to')->get();

        // $ftm = Lead::forHospital($hospital->id)->where('followup_created', true)->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->count();
        $ftm = Lead::forHospital($hospital->id)->where('status', '<>', 'Created')->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->select('assigned_to', DB::raw('count(leads.id) as count'))->groupBy('assigned_to')->get();

        // $lcm = Lead::forHospital($hospital->id)->where('status', 'Consulted')->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->count();
        $lcm = Lead::forHospital($hospital->id)->where('status', 'Consulted')->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->select('assigned_to', DB::raw('count(leads.id) as count'))->groupBy('assigned_to')->get();

        $pf = DB::table('followups')
            ->join('leads as l', 'l.id', '=', 'followups.lead_id')
            ->where('l.hospital_id', $hospital->id)
            ->where('followups.actual_date', null)
            ->select('l.assigned_to', DB::raw('COUNT(l.id) as count'))
            ->groupBy('l.assigned_to')
            ->get();
        $results = [];
        foreach ($lpm as $l) {
            $results[$l->assigned_to]['lpm'] = $l->count;
        }
        foreach ($ftm as $f) {
            $results[$f->assigned_to]['ftm'] = $f->count;
        }
        foreach ($lcm as $l) {
            $results[$l->assigned_to]['lcm'] = $l->count;
        }
        foreach ($pf as $p) {
            $results[$p->assigned_to]['pf'] = $p->count;
        }
        // $agents = [];
        // foreach (collect($hospital->agents())->pluck('name', 'id') as $id => $name) {
        //     $obj = new \stdClass();
        //     $obj->id = $id;
        //     $obj->name = $name;
        //     $agents[] = $obj;
        // }
        return ['counts' => $results, 'agents' => collect($hospital->agents())->pluck('name', 'id')];
    }

    public function getProcessChartData($currentMonth){
        $process_chart_data = [];
        $hospitalID = auth()->user()->hospital_id;
        $user = Auth::user();
        $baseQuery = Lead::forHospital($hospitalID)->whereMonth('created_at',$currentMonth);
        if($user->hasRole('agent')){
            $baseQuery->where('assigned_to',$user->id);
        }
        $newQuery = clone $baseQuery;
        $process_chart_data['unprocessed_leads'] = $newQuery->where('status','Created')->where('followup_created',false)->count();

        $newQuery = clone $baseQuery;
        $process_chart_data['followed_up_leads'] = $newQuery->where('status','Follow-up Started')->count();

        $newQuery = clone $baseQuery;
        $process_chart_data['appointments_created'] = $newQuery->where('status','Appointment Fixed')->count();

        $newQuery = clone $baseQuery;
        $process_chart_data['consulted'] =  $newQuery->where('status','Consulted')->count();

        $newQuery = clone $baseQuery;
        $process_chart_data['closed'] =$newQuery->where('status','Closed')->count();

        return $process_chart_data;
    }

    public function getValidChartData($currentMonth){
        $valid_chart_data = [];
        $hospitalID = auth()->user()->hospital_id;
        $user = Auth::user();
        $baseQuery = Lead::forHospital($hospitalID)->whereMonth('created_at',$currentMonth);
        if($user->hasRole('agent')){
            $baseQuery->where('assigned_to',$user->id);
        }

        $newQuery = clone $baseQuery;
        $valid_chart_data['valid_leads'] = $newQuery->where('is_valid',true)->count();

        $newQuery = clone $baseQuery;
        $valid_chart_data['invalid_leads'] = $newQuery->where('is_valid',false)->count();

        return $valid_chart_data;
    }

    public function getGenuineChartData($currentMonth){
        $genuine_chart_data = [];
        $hospitalID = auth()->user()->hospital_id;
        $user = Auth::user();
        $baseQuery = Lead::forHospital($hospitalID)->whereMonth('created_at',$currentMonth);
        if($user->hasRole('agent')){
            $baseQuery->where('assigned_to',$user->id);
        }

        $newQuery = clone $baseQuery;
        $genuine_chart_data['genuine_leads'] = $newQuery->where('is_genuine',true)->count();

        $newQuery = clone $baseQuery;
        $genuine_chart_data['false_leads'] = $newQuery->where('is_genuine',false)->count();

        return $genuine_chart_data;
    }

    public function getFollowupData($user, $selectedCenter)
    {

        $followupsQuery = Followup::whereHas('lead', function ($qr) use ($user) {
            return $qr->where('hospital_id',$user->hospital_id)->where('status','!=','Created');
        })->with(['lead'=>function($q) use($user){
            return $q->with(['appointment'=>function($qr){
                return $qr->with('doctor');
            }]);
        }, 'remarks'])
            ->whereDate('scheduled_date', '<=', date('Y-m-d'))
            ->where('actual_date', null);

        if ($user->hasRole('agent')) {
            $followupsQuery->whereHas('lead', function ($query) use ($user) {
                $query->where('assigned_to', $user->id);
            });
        }

        if($selectedCenter != null && $selectedCenter != 'all' && $user->hasRole('admin')){
            $followupsQuery->whereHas('lead', function ($qry) use($selectedCenter) {
                return $qry->where('center_id', $selectedCenter);
            });
        }

        $followups = $followupsQuery->paginate(30);
        $doctors = Doctor::all();
        $messageTemplates = Message::all();
        $centers = Center::where('hospital_id',$user->hospital_id)->get();

        return compact('followups', 'doctors','messageTemplates','centers','selectedCenter');
    }

    public function getSingleFollowupData($user, $id){
        $followup = Followup::whereHas('lead',function ($query) use($id,$user){
            return $query->where('hospital_id',$user->hospital_id)->where('id',$id)->when($user->hasRole('agent'), function ($qr) use ($user){
                return $qr->where('assigned_to',$user->id);
            });
        })->with(['lead'=>function ($q){
            return $q->with(['appointment'=> function($qry){
                return $qry->with('doctor');
            },'remarks']);
        },'remarks'])->latest()->get()->first();

        $doctors = Doctor::all();
        $messageTemplates = Message::all();
        $centers = Center::where('hospital_id',$user->hospital_id)->get();

        return ['followup'=>$followup, 'doctors'=>$doctors, 'messageTemplates'=>$messageTemplates, 'centers'=>$centers];
    }

    public function getAgents($centerId)
    {
        $center = Center::find($centerId);
        $agents = $center->agents();
        return [
            'agents' => $agents
        ];
    }
}
