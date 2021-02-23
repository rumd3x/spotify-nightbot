<?php

namespace App\Jobs;

use Rumd3x\IFTTT\Event;
use Rumd3x\IFTTT\Trigger;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class IFTTTWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The event to be sent
     *
     * @var Event
     */
    private $event;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Event $event)
    {
        $this->event = $event;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $trigger = new Trigger(env('IFTTT_KEY'));
        $trigger->notify($this->event);
    }
}
