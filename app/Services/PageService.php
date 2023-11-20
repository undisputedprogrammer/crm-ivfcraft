<?php

namespace App\Services;

use App\Models\Campaign;
use Carbon\Carbon;
use App\Models\Lead;
use App\Models\User;
use App\Models\Center;
use App\Models\Doctor;
use App\Models\Journal;
use App\Models\Message;
use App\Models\Followup;
use App\Models\Hospital;
use App\Models\Source;
use Carbon\CarbonConverterInterface;
use Complex\Functions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class PageService
{
    public function getLeads($user, $selectedLeads, $selectedCenter, $search, $status, $is_valid, $is_genuine, $creation_date, $processed)
    {
        if($search != null){
            $leadsQuery = Lead::with(['followups' => function ($qr) {
                return $qr->with(['remarks']);
            }, 'appointment', 'source'])->where('hospital_id', $user->hospital_id)->where('name', 'like', '%' . $search . '%')
            ->orWhere('phone', 'like', '%' . $search . '%');

            $leadsQuery->when($user->hasRole('agent'), function ($query) use ($user) {
                return $query->where('assigned_to', $user->id);
            });

            return $this->returnLeads($user,$selectedLeads,$selectedCenter,$leadsQuery,$status, $creation_date, $processed);
        }

        if($creation_date != null){
            $leadsQuery = Lead::with(['followups' => function ($qr) {
                return $qr->with(['remarks']);
            }, 'appointment', 'source'])->where('hospital_id', $user->hospital_id)->whereDate('created_at',$creation_date);

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
            }, 'appointment', 'source'])->where('hospital_id', $user->hospital_id)->whereDate('followup_created_at',$today);

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
                }, 'appointment', 'source'])->where('hospital_id', $user->hospital_id);
            }
            else{
                $leadsQuery = Lead::with(['followups' => function ($qr) {
                    return $qr->with(['remarks']);
                }, 'appointment', 'source'])->where('hospital_id', $user->hospital_id)->where('status', $status);
            }

        }
        else{
            $leadsQuery = Lead::with(['followups' => function ($qr) {
                return $qr->with(['remarks']);
            },'appointment', 'source'])->where('hospital_id', $user->hospital_id)->where('status', '=', 'Created');
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
        $process_chart_data = json_encode($this->getProcessChartData());
        $valid_chart_data = json_encode($this->getValidChartData());
        $genuine_chart_data = json_encode($this->getGenuineChartData());
        return compact('lpm', 'ftm', 'lcm', 'pf', 'hospitals', 'centers','journal','process_chart_data','valid_chart_data','genuine_chart_data');
    }

    public function getPerformaceOverview($from = null, $to = null){
        if ($from != null && $to != null) {
            $fromDate = Carbon::createFromFormat('Y-m-d',$from)->format('Y-m-d');
            $toDate = Carbon::createFromFormat('Y-m-d', $to)->format('Y-m-d');
        }else{
            $fromDate = Carbon::today()->startOfMonth()->format('Y-m-d');
            $toDate = Carbon::today()->format('Y-m-d');
        }

        $hospital = auth()->user()->hospital;
        $hospitals = [$hospital];
        $centers = $hospitals[0]->centers;


        $authUser = auth()->user();

        if($authUser->hasRole('admin')) {

            $lpm = Lead::forHospital($hospital->id)->whereDate('created_at', '>=', $fromDate)->whereDate('created_at','<=', $toDate)->count();

            $ftm = Lead::forHospital($hospital->id)->where('status', '<>', 'Created')->whereDate('created_at', '>=', $fromDate)->whereDate('created_at','<=', $toDate)->count();

            $lcm = Lead::forHospital($hospital->id)->where('status', 'Consulted')->whereDate('created_at', '>=', $fromDate)->whereYear('created_at', '<=', $toDate)->count();

            $pf = Followup::whereHas('lead', function ($query) use($hospital){
                $query->where('hospital_id', $hospital->id);
            })->where('actual_date', null)->count();

        } else {
            $lpm = Lead::forAgent($authUser->id)->whereDate('created_at', '>=', $fromDate)->whereDate('created_at', '<=', $toDate)->count();

            $ftm = Lead::forAgent($authUser->id)->where('status', '<>', 'Created')->whereDate('created_at', '>=', $fromDate)->whereDate('created_at', '<=', $toDate)->count();

            $lcm = Lead::forAgent($authUser->id)->where('status', 'Consulted')->whereDate('created_at', '>=', $fromDate)->whereDate('created_at', '<=', $toDate)->count();


            $pf = Followup::whereHas('lead', function ($query) use($authUser){
                $query->where('assigned_to', $authUser->id);
            })->where('actual_date', null)->count();
        }

        $process_chart_data = json_encode($this->getProcessChartData($fromDate, $toDate));
        $valid_chart_data = json_encode($this->getValidChartData($fromDate, $toDate));
        $genuine_chart_data = json_encode($this->getGenuineChartData($fromDate, $toDate));

        return compact('lpm', 'ftm', 'lcm', 'pf', 'hospitals', 'centers','process_chart_data','valid_chart_data','genuine_chart_data');
    }

    public function agentsPerformance($from, $to)
    {
        if ($from != null && $to != null) {
            $fromDate = Carbon::createFromFormat('Y-m-d',$from)->format('Y-m-d');
            $toDate = Carbon::createFromFormat('Y-m-d', $to)->format('Y-m-d');
        } else {
            $fromDate = Carbon::today()->startOfMonth()->format('Y-m-d');
            $toDate = Carbon::today()->format('Y-m-d');
        }

        DB::statement("SET SQL_MODE=''");
        $hospital = auth()->user()->hospital;

        $lpm = Lead::forHospital($hospital->id)->whereDate('created_at', '>=', $fromDate)->whereDate('created_at', '<=', $toDate)->select('assigned_to', DB::raw('count(leads.id) as count'))->groupBy('assigned_to')->get();

        // $ftm = Lead::forHospital($hospital->id)->where('followup_created', true)->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->count();
        $ftm = Lead::forHospital($hospital->id)->where('status', '<>', 'Created')->whereDate('leads.created_at', '>=', $fromDate)->whereDate('leads.created_at', '<=', $toDate)->join('followups','leads.id','=','followups.lead_id')->where('followups.actual_date','!=',null)->select('leads.assigned_to', DB::raw('COUNT(followups.id) as count'))->groupBy('leads.assigned_to')->get();

        // $lcm = Lead::forHospital($hospital->id)->where('status', 'Consulted')->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->count();
        $lcm = Lead::forHospital($hospital->id)->where('status', 'Consulted')->whereDate('created_at', '>=', $fromDate)->whereDate('created_at', '<=', $toDate)->select('assigned_to', DB::raw('count(leads.id) as count'))->groupBy('assigned_to')->get();

        $pf = DB::table('followups')
            ->join('leads as l', 'l.id', '=', 'followups.lead_id')
            ->where('l.hospital_id', $hospital->id)
            ->whereDate('l.created_at', '>=', $fromDate)
            ->whereDate('l.created_at', '<=', $toDate)
            ->where('followups.actual_date', null)
            ->select('l.assigned_to', DB::raw('COUNT(l.id) as count'))
            ->groupBy('l.assigned_to')
            ->get();

        $responsive_followups = Followup::whereHas('lead', function($q) use($hospital){
            return $q->forHospital($hospital->id);
        })->join('leads','followups.lead_id','=','leads.id')->whereDate('leads.created_at', '>=', $fromDate)->whereDate('leads.created_at', '<=', $toDate)->select('leads.assigned_to', DB::raw('COUNT(CASE WHEN followups.call_status = "Responsive" THEN 1 END) as responsive'), DB::raw('COUNT(CASE WHEN followups.call_status != "Responsive" THEN 1 END) as non_responsive'))->groupBy('leads.assigned_to')->get();

        $followup_initiated_leads = Lead::forHospital($hospital->id)->whereDate('created_at','>=',$fromDate)->whereDate('created_at','<=',$toDate)->where('status','!=','Created')->select('assigned_to', DB::raw('COUNT(leads.id) as count'))->groupBy('assigned_to')->get();

        DB::statement("SET SQL_MODE='only_full_group_by'");

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
        foreach($responsive_followups as $rf) {
            $results[$rf->assigned_to]['responsive_followups'] = $rf->responsive;
            $results[$rf->assigned_to]['non_responsive_followups'] = $rf->non_responsive;
        }
        foreach($followup_initiated_leads as $fil){
            $results[$fil->assigned_to]['followup_initiated_leads'] = $fil->count;
        }

        return ['counts' => $results, 'agents' => collect($hospital->agents())->pluck('name', 'id')];
    }

    public function getCampaignReport($from, $to){
        if($from != null && $to != null){
            $fromDate = Carbon::createFromFormat('Y-m-d', $from)->format('Y-m-d');
            $toDate = Carbon::createFromFormat('Y-m-d', $to)->format('Y-m-d');
        }
        else{
            $fromDate = Carbon::today()->startOfMonth()->format('Y-m-d');
            $toDate = Carbon::today()->format('Y-m-d');
        }

        $hospital = auth()->user()->hospital_id;

        $campaigns = Campaign::all();

        if(auth()->user()->hasRole('admin')){
            $campaignQuery = Lead::forHospital($hospital);
        }else{
            $campaignQuery = Lead::forHospital($hospital)->where('assigned_to', auth()->user()->id);
        }

        // $campaignQuery =

        $total_leads = $campaignQuery->whereDate('created_at','>=',$fromDate)->whereDate('created_at','<=',$toDate)->select('campaign',DB::raw('COUNT(DISTINCT leads.id) as total_leads, COUNT(DISTINCT CASE WHEN leads.status != "Created" THEN leads.id END) as followup_initiated_leads, COUNT(DISTINCT CASE WHEN leads.status = "Consulted" THEN leads.id END) as leads_converted, COUNT(DISTINCT CASE WHEN leads.customer_segment = "hot" THEN leads.id END) as hot_leads, COUNT(DISTINCT CASE WHEN leads.customer_segment = "warm" THEN leads.id END) as warm_leads, COUNT(DISTINCT CASE WHEN leads.customer_segment = "cold" THEN leads.id END) as cold_leads, COUNT(DISTINCT CASE WHEN leads.is_valid = true THEN leads.id END) as valid_leads, COUNT(DISTINCT CASE WHEN leads.is_genuine = true THEN leads.id END) as genuine_leads'))->groupBy('campaign')->get();

        // $followup_initiated_leads = Lead::forHospital($hospital)->whereMonth('created_at', $searchMonth)->whereYear('created_at',$searchYear)->select('campaign', DB::raw('SUM(CASE WHEN leads.status != "Created" THEN 1 END) as followup_initiated_leads'))->groupBy('campaign')->get();

        // $leads_converted = Lead::forHospital($hospital)->whereMonth('created_at',$searchMonth)->whereYear('created_at',$searchYear)->where('status','Consulted')->select('campaign',DB::raw('count(leads.id) as count'))->groupBy('campaign')->get();

        // $hot_leads = Lead::forHospital($hospital)->whereMonth('created_at',$searchMonth)->whereYear('created_at',$searchYear)->where('customer_segment','hot')->select('campaign',DB::raw('count(leads.id) as count'))->groupBy('campaign')->get();

        // $warm_leads = Lead::forHospital($hospital)->whereMonth('created_at',$searchMonth)->whereYear('created_at',$searchYear)->where('customer_segment','warm')->select('campaign',DB::raw('count(leads.id) as count'))->groupBy('campaign')->get();

        // $cold_leads = Lead::forHospital($hospital)->whereMonth('created_at',$searchMonth)->whereYear('created_at',$searchYear)->where('customer_segment','cold')->select('campaign',DB::raw('count(leads.id) as count'))->groupBy('campaign')->get();

        // $valid_leads = Lead::forHospital($hospital)->whereMonth('created_at',$searchMonth)->whereYear('created_at',$searchYear)->where('is_valid', true)->select('campaign', DB::raw('count(leads.id) as count'))->groupBy('campaign')->get();

        // $genuine_leads = Lead::forHospital($hospital)->whereMonth('created_at',$searchMonth)->whereYear('created_at',$searchYear)->where('is_genuine', true)->select('campaign', DB::raw('count(leads.id) as count'))->groupBy('campaign')->get();

        $responsive_followups = Followup::whereHas('lead', function ($q) use ($hospital){
            return $q->forHospital($hospital);
        })->whereDate('leads.created_at','>=',$fromDate)->whereDate('leads.created_at','<=',$toDate)->join('leads', 'followups.lead_id', '=', 'leads.id')->select('leads.campaign', DB::raw('COUNT(CASE WHEN followups.call_status = "Responsive" THEN 1 END) as responsive_followups'), DB::raw('COUNT(CASE WHEN followups.call_status = "Not responsive" THEN 1 END) as non_responsive_followups'))->groupBy('leads.campaign')->get();

        $campaingReport = [];

        foreach($total_leads as $t){
            $campaingReport[$t->campaign]['total_leads'] = $t->total_leads;
            $campaingReport[$t->campaign]['followup_initiated_leads'] = $t->followup_initiated_leads;
            $campaingReport[$t->campaign]['leads_converted'] = $t->leads_converted;
            $campaingReport[$t->campaign]['hot_leads'] = $t->hot_leads;
            $campaingReport[$t->campaign]['warm_leads'] = $t->warm_leads;
            $campaingReport[$t->campaign]['cold_leads'] = $t->cold_leads;
            $campaingReport[$t->campaign]['valid_leads'] = $t->valid_leads;
            $campaingReport[$t->campaign]['genuine_leads'] = $t->genuine_leads;
        }

        // foreach($leads_converted as $lc){
        //     $campaingReport[$lc->campaign]['converted_leads'] = $lc->count;
        // }

        // foreach($hot_leads as $h){
        //     $campaingReport[$h->campaign]['hot_leads'] = $h->count;
        // }

        // foreach($warm_leads as $w){
        //     $campaingReport[$w->campaign]['warm_leads'] = $w->count;
        // }

        // foreach($cold_leads as $c){
        //     $campaingReport[$c->campaign]['cold_leads'] = $c->count;
        // }

        // foreach($valid_leads as $v){
        //     $campaingReport[$v->campaign]['valid_leads'] = $v->count;
        // }

        // foreach($genuine_leads as $g){
        //     $campaingReport[$g->campaign]['genuine_leads'] = $g->count;
        // }

        foreach($responsive_followups as $fp){
            $campaingReport[$fp->campaign]['responsive_followups'] = $fp->responsive_followups;
            $campaingReport[$fp->campaign]['non_responsive_followups'] = $fp->non_responsive_followups;
        }
        // foreach($followup_initiated_leads as $fil){
        //     $campaingReport[$fil->campaign]['followup_initiated_leads'] = $fil->followup_initiated_leads;
        // }

        return ['campaignReport' => $campaingReport, 'campaigns'=> $campaigns];
    }

    public function getSourceReport($from, $to){

        if($from != null && $to != null){
            $fromDate = Carbon::createFromFormat('Y-m-d',$from)->format('Y-m-d');
            $toDate = Carbon::createFromFormat('Y-m-d',$to)->format('Y-m-d');
        }
        else{
            $fromDate = Carbon::today()->startOfMonth()->format('Y-m-d');
            $toDate = Carbon::today()->format('Y-m-d');
        }

        $hospital_id = auth()->user()->hospital_id;
        DB::statement("SET SQL_MODE=''");

        if(auth()->user()->hasRole('admin')){
            $reportsQuery = Lead::forHospital($hospital_id);
        }else{
            $reportsQuery = Lead::forHospital($hospital_id)->where('leads.assigned_to',auth()->user()->id);
        }
        $reports = $reportsQuery->whereDate('leads.created_at', '>=', $fromDate)
        ->whereDate('leads.created_at', '<=', $toDate)
        ->join('sources','leads.source_id','=','sources.id')
        ->leftJoin('followups','leads.id','=','followups.lead_id')
        ->select('sources.name',
             DB::raw('COUNT(DISTINCT leads.id) as total_leads'),
             DB::raw('COUNT(DISTINCT CASE WHEN leads.is_valid = true THEN leads.id END) as valid_leads'),
             DB::raw('COUNT(DISTINCT CASE WHEN leads.is_genuine = true THEN leads.id END) as genuine_leads'), DB::raw('COUNT(DISTINCT CASE WHEN leads.customer_segment = "hot" THEN leads.id END) as hot_leads, COUNT(DISTINCT CASE WHEN leads.customer_segment = "warm" THEN leads.id END) as warm_leads, COUNT(DISTINCT CASE WHEN leads.customer_segment ="cold" THEN leads.id END) as cold_leads, COUNT(DISTINCT CASE WHEN leads.status = "Consulted" THEN leads.id END) as converted_leads, SUM(CASE WHEN followups.call_status = "Responsive" THEN 1 END) as responsive_followups, SUM(CASE WHEN followups.call_status = "Not responsive" THEN 1 END) as non_responsive_followups, COUNT(DISTINCT CASE WHEN leads.status != "Created" THEN leads.id END) as followup_initiated_leads'))
        ->groupBy('source_id')->get();

        DB::statement("SET SQL_MODE='only_full_group_by'");


        $sourceReport = [];

        foreach($reports as $r){
            $sourceReport[$r->name]['total_leads'] = $r->total_leads;
            $sourceReport[$r->name]['valid_leads'] = $r->valid_leads;
            $sourceReport[$r->name]['genuine_leads'] = $r->genuine_leads;
            $sourceReport[$r->name]['hot_leads'] = $r->hot_leads;
            $sourceReport[$r->name]['warm_leads'] = $r->warm_leads;
            $sourceReport[$r->name]['cold_leads'] = $r->cold_leads;
            $sourceReport[$r->name]['converted_leads'] = $r->converted_leads;
            $sourceReport[$r->name]['responsive_followups'] = $r->responsive_followups;
            $sourceReport[$r->name]['non_responsive_followups'] = $r->non_responsive_followups;
            $sourceReport[$r->name]['followup_initiated_leads'] = $r->followup_initiated_leads;
        }

        return['sourceReport' => $sourceReport];
    }

    public function getProcessChartData($fromDate = null, $toDate = null){
        $process_chart_data = [];
        $hospitalID = auth()->user()->hospital_id;
        $user = Auth::user();

        if($fromDate == null || $toDate == null){
            $fromDate = Carbon::today()->startOfMonth()->format('Y-m-d');
            $toDate = Carbon::today()->format('Y-m-d');
        }

        $baseQuery = Lead::forHospital($hospitalID)->whereDate('created_at', '>=', $fromDate)->whereDate('created_at', '<=', $toDate);

        if($user->hasRole('agent')){
            $baseQuery->where('assigned_to',$user->id);
        }
        $newQuery = clone $baseQuery;
        $process_chart_data['unprocessed_leads'] = $newQuery->where('status','Created')->count();

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

    public function getValidChartData($fromDate = null, $toDate = null){
        if($fromDate == null || $toDate == null){
            $fromDate = Carbon::today()->startOfMonth()->format('Y-m-d');
            $toDate = Carbon::today()->format('Y-m-d');
        }
        $valid_chart_data = [];
        $hospitalID = auth()->user()->hospital_id;
        $user = Auth::user();
        $baseQuery = Lead::forHospital($hospitalID)->whereDate('created_at', '>=', $fromDate)->whereDate('created_at', '<=', $toDate);

        if($user->hasRole('agent')){
            $baseQuery->where('assigned_to',$user->id);
        }

        $newQuery = clone $baseQuery;
        $valid_chart_data['valid_leads'] = $newQuery->where('is_valid',true)->count();

        $newQuery = clone $baseQuery;
        $valid_chart_data['invalid_leads'] = $newQuery->where('is_valid',false)->count();

        return $valid_chart_data;
    }

    public function getGenuineChartData($fromDate = null, $toDate = null){
        if($fromDate == null || $toDate == null){
            $fromDate = Carbon::today()->startOfMonth()->format('Y-m-d');
            $toDate = Carbon::today()->format('Y-m-d');
        }
        $genuine_chart_data = [];
        $hospitalID = auth()->user()->hospital_id;
        $user = Auth::user();
        $baseQuery = Lead::forHospital($hospitalID)->whereDate('created_at', '>=', $fromDate)->whereDate('created_at', '<=', $toDate);

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
            }, 'source']);
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
            },'remarks', 'source']);
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

    public static function getSource($code, $name){
        $source = Source::where('code', $code)->get()->first();
        if($source == null){
            $source = Source::create([
                'code' => $code,
                'name' => $name
            ]);
        }

        return $source;
    }
}
