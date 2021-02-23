<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;

class ArtisanCommandJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
    * Artisan command to be run
    *
    * @var string
    */
    private $command;
    /**
    * Argument to the artisan command
    *
    * @var array
    */
    private $arguments;
    /**
    * Creates an Instance of ArtisanCommandJob
    *
    * @param string $command
    * @param array $arguments
    */
    public function __construct(string $command, array $arguments = [])
    {
        $this->command = $command;
        $this->arguments = $arguments;
    }
    /**
    * Execute the job.
    *
    * @return void
    */
    public function handle()
    {
        Artisan::call($this->command, $this->arguments);
    }
}
