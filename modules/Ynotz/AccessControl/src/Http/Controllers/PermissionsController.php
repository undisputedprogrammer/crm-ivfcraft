<?php

namespace Ynotz\AccessControl\Http\Controllers;

use Illuminate\Http\Request;
use Ynotz\AccessControl\Models\Permission;
use Ynotz\AccessControl\Services\PermissionService;
use Ynotz\SmartPages\Http\Controllers\SmartController;
use Ynotz\AccessControl\Http\Requests\PermissionsStoreRequest;
use Ynotz\EasyAdmin\Traits\HasMVConnector;

class PermissionsController extends SmartController
{
    use HasMVConnector;

    public function __construct(public PermissionService $connectorService, Request $request){
        parent::__construct($request);
        $this->itemName = 'permission';
        $this->indexView = 'easyadmin::admin.indexpanel';
        $this->createView = 'accesscontrol::permissions.create';
        $this->editView = 'accesscontrol::permissions.edit';
        $this->modelClass = Permission::class;
        $this->resultsName = 'permissions';
    }

    public function store(PermissionsStoreRequest $request)
    {
        return $this->doStore($request);
    }
}
