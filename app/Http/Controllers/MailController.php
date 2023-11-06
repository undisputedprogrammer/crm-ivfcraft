<?php

namespace App\Http\Controllers;

use App\Services\MailService;
use Illuminate\Http\Request;
use Ynotz\EasyAdmin\Traits\HasMVConnector;
use Ynotz\SmartPages\Http\Controllers\SmartController;

class MailController extends SmartController
{
    use HasMVConnector;
    private $connectorService;

    public function __construct(Request $request, MailService $service)
    {
        parent::__construct($request);
        $this->connectorService = $service;
    }

    public function custom(){
        $response = $this->connectorService->sendCustomMail($this->request);
        return response()->json($response,200);
    }
}
