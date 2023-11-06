<?php

namespace App\Services;

use App\Models\Message;
use Ynotz\EasyAdmin\Traits\IsModelViewConnector;
use Ynotz\EasyAdmin\Contracts\ModelViewConnector;

class MessageService implements ModelViewConnector{

    use IsModelViewConnector;

    public function __construct()
    {
        $this->modelClass = Message::class;
    }

    public function getStoreValidationRules(): array
    {
        return [
            'template' => ['required', 'string'],
            'message' => ['required', 'string']
        ];
    }

    public function getUpdateValidationRules(): array
    {
        return [
            'template' => ['required', 'string'],
            'message' => ['required', 'string']
        ];
    }

    // public function message($request){

    // }
}
?>
