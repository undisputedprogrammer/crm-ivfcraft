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
        $campaigns = Campaign::paginate(3);
        return $this->buildResponse('pages.manage-campaigns', compact('campaigns'));
    }
    public function all(Request $request){
        $campaigns = Campaign::orderBy('name')->get();
        return response()->json([
            'campaigns' => $campaigns
        ]);
    }

    public function store(Request $request)
    {
        $campaign = Campaign::create([
            'name' => $request->input('name')
        ]);
        return response()->json([
            'success' => true,
            'campaign' => $campaign,
            'message' => 'New campaign created!'
        ]);
    }

    public function toggle($id)
    {
        $campaign = Campaign::find($id);
        $campaign->enabled = !$campaign->enabled;
        $campaign->save();
        $message = $campaign->enabled ? 'Campaign was enabled' : 'Campaign was disabled';
        return response()->json([
            'success' => true,
            'message' => $message,
            'mode' => $campaign->enabled ? 'success' : 'warning'
        ]);
    }
}
