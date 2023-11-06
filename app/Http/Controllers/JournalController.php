<?php

namespace App\Http\Controllers;

use App\Models\Hospital;
use App\Models\Journal;
use App\Providers\HospitalComposerServiceProvider;
use App\Services\JournalService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Ynotz\SmartPages\Http\Controllers\SmartController;

class JournalController extends SmartController
{
    private $jService;

    public function __construct(Request $request, JournalService $jService)
    {
        $this->request = $request;
        $this->jService = $jService;
    }

    public function store(Request $request){

        $response = $this->jService->storeJournal($request);

        if(isset($response['error'])){
            return response()->json(['success'=>false, 'message'=>'An error occured','error'=>$response['error']]);
        }
        elseif(isset($response['existingJournal'])){
            return response()->json(['success'=>true, 'message'=>'Journal Updated!','journal'=>$response['existingJournal']]);
        }
        return response()->json(['success'=>true, 'message'=>'Journal Created!','journal'=>$response['journal']]);
    }

    public function fetch(Request $request){
        if($request->month != null){
            $date = Carbon::createFromFormat('Y-m',$request->month);
            $month = $date->format('m');
            $year = $date->format('Y');
        }else{
            $today = Carbon::now();
            $month  = $today->format('m');
            $year = $today->format('Y');
        }
        $userId = $request->user_id ?? auth()->user()->id;
        $journalsQuery = Journal::where('user_id', $userId)->whereMonth('date',$month)->whereYear('date',$year);

        $journals = $journalsQuery->latest()->get();
        if (!isset($request->user_id)) {
            return $this->buildResponse(
                'pages.own-journal',
                ['journals' => $journals]
            );
        }
        return response($journals);
    }
}
