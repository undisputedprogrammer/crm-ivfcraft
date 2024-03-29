<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Lead;
use App\Models\Center;
use App\Models\Remark;
use App\Models\Followup;
use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Services\AppointmentService;
use App\Services\ProcedureService;
use Illuminate\Support\Facades\Auth;
use Ynotz\SmartPages\Http\Controllers\SmartController;

class ProcedureController extends SmartController
{
    protected $connectorService;

    public function __construct(Request $request, ProcedureService $service)
    {
        parent::__construct($request);
        $this->connectorService = $service;
    }

    public function store(Request $request)
    {
        $response = $this->connectorService->processAndStore($request);

        return response()->json($response);

    }


    public function index(Request $request)
    {
        $selectedCenter = $request->center;

        $query = Appointment::whereHas('lead', function($q) use($request){
            return $q->where('hospital_id',$request->user()->hospital_id);
        })->with(['lead' => function ($q) {
            return $q->with('remarks');
        }, 'doctor'])->orderBy('appointment_date', 'asc');

        if (isset($this->request->from)) {
            $query->where('appointment_date', '>=', $this->request->from);
        }

        if (isset($this->request->to)) {
            $query->where('appointment_date', '<=', $this->request->to);
        }

        if(isset($this->request->center)) {
            if($selectedCenter != null && $selectedCenter != 'all'){
                $query->whereHas('lead', function ($q) use($selectedCenter){
                    return $q->where('center_id', $selectedCenter);
                });
            }
        }


        $appointments = $query->paginate(10);
        $centers = Center::where('hospital_id',$request->user()->hospital_id)->get();

        return $this->buildResponse('pages.appointments', ['appointments' => $appointments, 'centers'=>$centers,'selectedCenter'=>$selectedCenter]);
    }

    public function procedureComplete(Request $request)
    {
        // if($request->consulted_date != null){
        //     $this->connectorService->makeAppointmentifNotExist($request->lead_id, $request->consulted_date, $request->doctor);
        // }
        $result = $this->connectorService->processProcedure($request->lead_id, $request->followup_id, $request->followup_date);

        return response()->json($result);
    }

    public function update(Request $request){
        // info('Inside controller function');
        $response = $this->connectorService->updateProcedure($request);
        return response()->json($response);
    }
}
