<?php

namespace App\Jobs;

use App\Services\WhatsAppApiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendBulkMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public $lead_id;
    public $template;
    public function __construct($lead_id, $template)
    {
        $this->lead_id = $lead_id;
        $this->template = $template;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        WhatsAppApiService::bulkMessage($this->lead_id, $this->template);
    }
}
