<?php

namespace App\Http\Controllers;

use App\Services\InternalChatService;
use Illuminate\Http\Request;
use Ynotz\SmartPages\Http\Controllers\SmartController;

class InternalChatController extends SmartController
{
    private $icService;

    public function __construct(Request $request, InternalChatService $icService)
    {
        parent::__construct($request);
        $this->icService = $icService;
    }

    public function index()
    {
        /**
         * @var App\Models\User;
         */
        $user = auth()->user();
        $chatRooms = $user->publicChatRooms;
        $loadedChats = $chatRooms[0]->internalChats;
        $chatFriends = $user->chatFriends();
        return $this->buildResponse(
            'pages.internal-chat',
            [
                'user' => $user,
                'chatRooms' => $chatRooms,
                'loadedChats' => $loadedChats,
                'chatFriends' => $chatFriends,
            ]
        );
    }

    public function postMessage(Request $request)
    {
        try {
            $message = $this->icService->postMessage(
                $request->input('sender_id'),
                $request->input('chat_room_id'),
                $request->input('text_message'),
            );
            return response()->json(
                [
                    'success' => true,
                    'message' => $message
                ]
            );
        } catch (\Throwable $e) {
            info($e->__toString());
            return response()->json(
                [
                    'success' => false,
                ]
            );
        }
    }

    public function olderMessages()
    {
        return response()->json(
            $this->icService->olderMessages(
                $this->request->input('room_id'),
                $this->request->input('earliest_msg_id')
            )
        );
    }

    public function getChatRoom()
    {
        return response()->json(
            [
                'chatRoom' => $this->icService->getChatRoom(
                    $this->request->input('entity_id'),
                    $this->request->input('entity_type'),
                    $this->request->input('chat_room_id')
                )
            ]
        );
    }
}
