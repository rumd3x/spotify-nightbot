<?php

namespace App\Jobs;

use App\Repositories\UserRepository;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class UpdateAllUsersSongsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info("Executing Job UpdateAllUsersSongsJob");

        UserRepository::chunkEnableds(function($users) {
            foreach ($users as $user) {
                SpotifyPollingJob::dispatch($user);
            }
        });
    }
}
