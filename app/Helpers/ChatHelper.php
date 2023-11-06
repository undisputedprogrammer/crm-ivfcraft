<?php
namespace App\Helpers;

use App\Models\User;
use App\Models\ChatRoom;

class ChatHelper
{
    public static function createRoom($entityId, $entityType)
    {
        switch($entityType) {
            case 'App\Models\User':
                $room = ChatRoom::create([
                    'type' => 'one-to-one',
                ]);
                break;
            default:
                $room = ChatRoom::create([
                    'type' => 'public',
                    'chatable_id' => $entityId,
                    'chatable_type' => $entityType
                ]);
                break;
        }
        return $room;
    }

    public static function getChatRoom($entityId = null, $entityType = null, $chatRoomId = null)
    {
        if (isset($chatRoomId)) {
            return ChatRoom::find($chatRoomId);
        }
        switch($entityType) {
            case 'App\Models\User':
                $authUser = auth()->user();
                $peer = User::find($entityId);
                $room = null;
                foreach ($authUser->chatRooms as $cr) {
                    if($cr->peer && $peer && ($cr->peer->id == $peer->id)) {
                        $room = $cr;
                    }
                }
                if(!isset($room)){
                    $room = ChatRoom::create([
                        'type' => 'one-to-one',
                    ]);
                    $room->users()->save($authUser);
                    $room->users()->save($peer);
                }
                break;
            default:
                $entity = $entityType::find($entityId);
                $room = $entity->chatRoom ?? ChatRoom::create([
                    'type' => 'public',
                    'chatable_id' => $entityId,
                    'chatable_type' => $entityType
                ]);
                break;
        }
        return $room;
    }
}
?>
