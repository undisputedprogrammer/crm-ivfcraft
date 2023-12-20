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
}
