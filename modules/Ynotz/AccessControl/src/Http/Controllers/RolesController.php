<?php

namespace Ynotz\AccessControl\Http\Controllers;

use Illuminate\Http\Request;
use Ynotz\AccessControl\Models\Role;
use Ynotz\EasyAdmin\Traits\HasMVConnector;
use Ynotz\AccessControl\Services\RoleService;
use Ynotz\SmartPages\Http\Controllers\SmartController;
use Ynotz\AccessControl\Http\Requests\RolesStoreRequest;

class RolesController extends SmartController
{
    use HasMVConnector;

    public function __construct(public RoleService $connectorService, Request $request){
        parent::__construct($request);
        $this->itemName = 'role';
        $this->indexView = 'easyadmin::admin.indexpanel';
        $this->createView = 'accesscontrol::roles.create';
        $this->editView = 'accesscontrol::roles.edit';
        $this->modelClass = Role::class;
    }

    public function store(RolesStoreRequest $request)
    {
        return $this->doStore($request);
    }
}
