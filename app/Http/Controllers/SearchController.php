<?php

namespace App\Http\Controllers;

use App\Models\Followup;
use App\Services\SearchService;
use Illuminate\Http\Request;
use Ynotz\SmartPages\Http\Controllers\SmartController;

class SearchController extends SmartController
{
    protected $connectorService;
    public function __construct(Request $request, SearchService $service)
    {
        parent::__construct($request);
        $this->connectorService = $service;
    }

    public function index(Request $request)
    {
        $results = $this->connectorService->getResults($request);

        return response()->json($results);
    }
}
