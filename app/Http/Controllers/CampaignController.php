<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use Illuminate\Http\Request;
use Ynotz\SmartPages\Http\Controllers\SmartController;

class CampaignController extends SmartController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function index(Request $request){
        $campaigns = Campaign::orderBy('name')->paginate(30);
        return $this->buildResponse('pages.manage-campaigns', compact('campaigns'));
    }
    public function all(Request $request){
        $campaigns = Campaign::where('enabled', true)
            ->orderBy('name')->get();
        return response()->json([
            'campaigns' => $campaigns
        ]);
    }
    public function formOptions(Request $request){
        $campaigns = Campaign::enabledHospital(auth()->user()->hospital_id)
        ->where('enable_in_forms', 1)->orderBy('name')->get();
        return response()->json([
            'campaigns' => $campaigns
        ]);
    }

    public function store(Request $request)
    {
        $campaign = Campaign::create([
            'name' => $request->input('name'),
            'enable_in_forms' => true
        ]);
        $campaign->enableHospital(auth()->user()->hospital_id);

        return response()->json([
            'success' => true,
            'campaign' => $campaign,
            'message' => 'New campaign created!'
        ]);
    }

    public function toggle($id)
    {
        $campaign = Campaign::find($id);
        $hid = auth()->user()->hospital_id;
        // $campaign->enabled = !$campaign->enabled;
        if (in_array($hid, $campaign->enabled_hospitals ?? [])) {
            $campaign->disableHospital($hid);
            $message = 'Campaign was disabled';
            $mode = 'error';
        } else {
            $campaign->enableHospital($hid);
            $message = 'Campaign was enabled';
            $mode = 'success';
        }
        $campaign->save();
        return response()->json([
            'success' => true,
            'message' => $message,
            'mode' => $mode
        ]);
    }

    public function toggleForm($id)
    {
        $campaign = Campaign::find($id);
        $hid = auth()->user()->hospital_id;
        $campaign->enable_in_forms = !$campaign->enable_in_forms;
        $campaign->save();
        $message = $campaign->enable_in_forms ? 'Campaign will be shown in form options' : 'Campaign will not be shown in form options';
        return response()->json([
            'success' => true,
            'message' => $message,
            'mode' => $campaign->enable_in_forms ? 'success' : 'error'
        ]);
    }
}
