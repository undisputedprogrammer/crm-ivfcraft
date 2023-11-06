<?php
namespace App\Services;

use App\Helpers\ChatHelper;
use App\Models\ChatRoom;
use App\Models\InternalChat;
use Carbon\Carbon;

class InternalChatService
{
    public function postMessage($sender_id,$chat_room_id , $text = null, $images = [])
    {
        $message = InternalChat::create([
            'sender_id' => $sender_id,
            'chat_room_id' => $chat_room_id,
            'message' => $text ?? '',
            'created_at' => Carbon::now()->timestamp
        ]);
        if (count($images) > 0) {
            $message->addMediaFromEAInput('chat_pics', $images);
        }
        return $message;
    }

    public function getMessages($lastid = null)
    {
        $messages = $lastid != null ? InternalChat::where('id', '>', $lastid)
            ->orderBy('created_at')
            ->get() : [];
        // $lastid = $lastid ?? ->id;
        if (!isset($lastid)) {
            $lchat = InternalChat::orderBy('id', 'desc')->limit(10)->get()->first();
            if ($lchat != null) {
                $lastid = $lchat->id;
            }
        }
        return [
            'messages' => $messages,
            'lastid' => count($messages) > 0 ? $messages->last()->id : $lastid
        ];
    }

    public function olderMessages($ChatRoomId, $earliestMsgId)
    {
        $messages = InternalChat::where('chat_room_id', $ChatRoomId)
            ->where('id', '<', $earliestMsgId)
            ->orderBy('created_at', 'desc')
            ->limit(config('chatSettings.previous_load_count'))
            ->get();
        return [
            'messages' => array_reverse($messages->toArray())
        ];
    }

    public function getChatRoom($entityId = null, $entityType = null, $chatRoomId = null)
    {
        return ChatHelper::getChatRoom(
            $entityId,
            $entityType,
            $chatRoomId
        );
    }
}
?>
