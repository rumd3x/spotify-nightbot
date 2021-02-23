<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Repositories\TimestampRepository;
use App\Timestamp;
use Illuminate\Support\Facades\Log;
use App\Repositories\UserRepository;

/**
 * Job de Sanitizaçao:
 *
 * - Verificar existencia de entrada sem saida e saida sem entrada e trata-las.
 * - Verificar existencia de duas ou mais entradas ou saidas consecutivas e manter apenas uma delas.
 */
class SanitizeTimestamps extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'timestamps:sanitize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ensure timestamps are well formed.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        foreach (UserRepository::allActive() as $user) {
            Log::info(sprintf("Sanitizing %s timestamps for day %s", $user->first_name, Carbon::yesterday()->format('Y-m-d')));

            $timestamps = TimestampRepository::getByDay(Carbon::yesterday(), $user);

            if ($timestamps->isEmpty()) {
                Log::info("No timestamps matching this criteria");
                continue;
            }

            foreach ($timestamps as $key => $ts) {
                Log::info(sprintf("Analyzing %s timestamp at %s", $ts->formatted_entry, $ts->carbon->format('H:i')));

                if ($key === 0 && !$ts->entry) {
                    Log::info(sprintf(
                        "Deleting timestamp at %s: Random exit without matching entry",
                        $ts->carbon->format('H:i')
                    ));

                    $ts->delete();
                    continue;
                }

                if ($key === count($timestamps)-1 && $ts->entry) {
                    Log::info(sprintf(
                        "Inserting timestamp at %s: Random entry without matching exit",
                        $ts->carbon->format('H:i')
                    ));

                    TimestampRepository::insert(Carbon::yesterday()->setTimeFromTimeString($ts->time)->addMinute(), $user, Timestamp::ENTRY_STATE_EXIT);
                    continue;
                }

                if ($key < count($timestamps)-1 && $ts->entry === $timestamps[$key+1]->entry) {
                    $diffToNext = Carbon::yesterday()->setTimeFromTimeString($ts->time)->diffInMinutes(Carbon::yesterday()->setTimeFromTimeString($timestamps[$key+1]->time));

                    if ($diffToNext > 60) {
                        Log::info(sprintf(
                            "Inserting timestamp at %s: Closing unclosed %s of timestamp at %s",
                            $timestamps[$key+1]->carbon->format('H:i'),
                            $ts->formatted_entry,
                            $ts->carbon->format('H:i')
                        ));

                        TimestampRepository::insert(Carbon::yesterday()->setTimeFromTimeString($timestamps[$key+1]->time)->subMinute(), $user, !$ts->entry);
                        continue;
                    }

                    Log::info(sprintf(
                        "Deleting timestamp: Duplicated %s of timestamp at %s",
                        $ts->formatted_entry,
                        $ts->carbon->format('H:i')
                    ));

                    if ($ts->entry) {
                        $timestamps[$key+1]->delete();
                        continue;
                    }

                    $ts->delete();
                }
            }
        }
    }
}
