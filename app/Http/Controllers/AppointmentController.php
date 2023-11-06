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
use Illuminate\Support\Facades\Auth;
use Ynotz\SmartPages\Http\Controllers\SmartController;

class AppointmentController extends SmartController
{
    protected $connectorService;

    public function __construct(Request $request, AppointmentService $service)
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

    public function consulted(Request $request)
    {
        $result = $this->connectorService->processConsult($request->lead_id, $request->followup_id, $request->followup_date);

        return response()->json($result);
    }

    public function updateAppointment(Request $request){
        // info('Inside controller function');
        $response = $this->connectorService->updateAppointment($request);
        return response()->json($response);
    }
}
