<?php

namespace App\Models;

class OneToOneChatRoom extends ChatRoom
{
    public function getPeer($id)
    {
        return $this->users()->query()->where('id', '<>', $id)->get()->first();
    }
}
