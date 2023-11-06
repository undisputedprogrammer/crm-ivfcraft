<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use App\Services\MessageService;
use Ynotz\EasyAdmin\Traits\HasMVConnector;
use Ynotz\SmartPages\Http\Controllers\SmartController;

class MessageController extends SmartController
{
    use HasMVConnector;
    private $connectorService;

    public function __construct(Request $request, MessageService $service)
    {
        parent::__construct($request);

        $this->connectorService = $service;
    }

    public function index()
    {
        $messages = Message::orderBy('id', 'desc')->paginate(10);

        return $this->buildResponse('pages.messages',compact('messages'));
    }

    public function message(Request $request){

        return response()->json(['success'=>true, 'message'=>'Message sent to '.$request->lead_name]);

    }
}
