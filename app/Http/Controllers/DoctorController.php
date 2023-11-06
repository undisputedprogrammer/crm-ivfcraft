<?php

namespace App\Http\Controllers;

use App\Models\Center;
use App\Models\Doctor;
use Illuminate\Http\Request;
use App\Services\DoctorService;
use Ynotz\EasyAdmin\Traits\HasMVConnector;
use Ynotz\SmartPages\Http\Controllers\SmartController;

class DoctorController extends SmartController
{
    use HasMVConnector;
    private $connectorService;

    public function __construct(Request $request, DoctorService $service)
    {
        parent::__construct($request);
        $this->connectorService = $service;
    }

    public function index(Request $request)
    {
        $selectedCenter = $request->center;
        $doctorsQuery = Doctor::whereHas('center', function($q) use($request){
            return $q->where('hospital_id',$request->user()->hospital_id);
        })->orderBy('id', 'desc');
        if($selectedCenter != null && $selectedCenter != 'all'){
            $doctorsQuery->where('center_id',$selectedCenter);
        }
        $doctors = $doctorsQuery->paginate(10);

        $centers = Center::where('hospital_id',$request->user()->hospital_id)->get();
        return $this->buildResponse('pages.doctors',['doctors' => $doctors,'centers'=>$centers,'selectedCenter'=>$selectedCenter]);
    }
}
