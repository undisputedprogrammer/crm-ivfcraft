<?php

namespace App\Http\Controllers;

use App\Services\AdhocRequirementsService;
use Illuminate\Http\Request;
use Ynotz\SmartPages\Http\Controllers\SmartController;

class AdhocRequirementsController extends SmartController
{
    public function reAssignedLeads(Request $request, AdhocRequirementsService $service)
    {
        try {
            return $this->buildResponse(
                'pages.reassignments',
                [
                    'success' => true,
                    'reassignments' => $service->reassignedLeads($request->all())
                ]
            );
        } catch (\Throwable $e) {
            return $this->buildResponse(
                'pages.reassignments',
                [
                    'success' => false,
                    'errors' => $e->__toString()
                ]
            );
        }
    }
}
