<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Services\QuestionService;
use Illuminate\Http\Client\ResponseSequence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Ynotz\SmartPages\Http\Controllers\SmartController;

class QuestionController extends SmartController
{
    protected $connectorService;
    public function __construct(Request $request, QuestionService $service)
    {
        parent::__construct($request);
        $this->connectorService = $service;
    }

    public function store(Request $request)
    {
        $result = $this->connectorService->processAndStore($request->question);

        return response()->json($result);
    }

    public function update(Request $request)
    {
        $result = $this->connectorService->processAndUpdate($request->id, $request->question);

        return response()->json($result);
    }
}
