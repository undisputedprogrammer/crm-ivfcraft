<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\User;
use App\Models\Center;
use App\Models\Message;
use Illuminate\Http\Request;
use App\Services\MessageService;
use Ynotz\SmartPages\Http\Controllers\SmartController;

class TemplateController extends SmartController
{
    protected $connectorService;
    protected $request;
    public function __construct(Request $request, MessageService $service)
    {
        parent::__construct($request);
        $this->request = $request;
        $this->connectorService = $service;
    }

    public function index()
    {
        $messages = Message::orderBy('id', 'desc')->paginate(10);

        return $this->buildResponse('pages.template-new', compact('messages'));
    }

    public function store()
    {
        $params = $this->request->all();
        $vars = array();
        foreach ($params as $param => $name) {
            if (substr($param, 0, 3) == 'var' && $name != null) {
                $pair = 'data_'.substr($param, 4);
                if ($params[$pair]) {
                    $data = $params[$pair];
                    array_push($vars, [$name => $data]);
                }
            }
        }
        $template = Message::create([
            'template'=>$this->request->template,
            'body'=>$this->request->templatebody,
            'payload'=>json_encode($vars)
        ]);
        return response()->json(['success' => true, 'message' => 'Template Created !!', 'params' => $template]);
    }


    // The below 2 functions has to be moved to LeadController
    public function reassign(Request $request)
    {
        $leadQuery = Lead::where('hospital_id',$request->user()->hospital_id)->with(['assigned'=>function($q){
            return $q->with('centers');
        }]);

        if($request->center != null && $request->center != 'all'){
            info('center selected is '.$request->center);
            $leadQuery->where('center_id',$request->center);
        }

        if ($request->agent != null && $request->agent != 'all') {
            info('agent selected is '.$request->agent);
            $leadQuery->where('assigned_to', $request->agent);
        }

        $leads = $leadQuery->paginate(10);

        $agents = User::where('hospital_id',$request->user()->hospital_id)->whereHas('roles', function ($q) {
            $q->where('name', 'agent');
        })->get();

        $centers = Center::where('hospital_id',$request->user()->hospital_id)->get();

        $selectedCenter = $request->center ? $request->center : null;
        $selectedAgent = $request->agent ? $request->agent : null;
        return $this->buildResponse('pages.reassign-lead', compact('leads', 'agents', 'centers', 'selectedCenter','selectedAgent'));
    }

    public function assign(Request $request)
    {
        $i = 0;
        $leads = explode(",", $request->selectedLeads);
        foreach ($leads as $lead) {
            $l = Lead::find($lead);
            $l->assigned_to = $request->agent;
            $l->save();
        }
        return response()->json(['success' => true, 'message' => 'Successfully assigned ' . $i . ' leads', 'agent' => $request->agent, 'leads' => $request->selectedLeads]);
    }
}
