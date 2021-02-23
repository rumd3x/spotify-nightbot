<?php

namespace App\Console\Commands;

use App\User;
use App\Timestamp;
use Carbon\Carbon;
use App\AppSetting;
use App\Utils\Calculator;
use App\Mail\SpreadsheetMail;
use Illuminate\Console\Command;
use App\VO\TimesheetCommandConfig;
use Illuminate\Support\Facades\Log;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Repositories\TimestampRepository;
use App\Exceptions\ConfigurationException;
use App\Repositories\AppSettingRepository;
use App\Exceptions\InvalidJobArgumentException;

class GenerateTimesheet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'timesheet:generate
                            {month? : The month to generate the timesheet, defaults to last month}
                            {year? : The year to generate the timesheet, default to current year}
                            {--target : Whether to generate target timesheet or normal timesheet}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate monthly timesheets';

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
        $config = $this->buildConfig();

        Log::info("Started {$config->generationDate->format('F')} timesheet generation");

        foreach (UserRepository::allActive() as $user) {
            Log::info("Generating {$user->first_name}'s timesheet");
            $this->makeTimesheet($config, $user);
        }
    }

    private function makeTimesheet(TimesheetCommandConfig $config, User $user)
    {
        $spreadsheet = IOFactory::load($config->templateFile);
        $worksheet = $spreadsheet->getActiveSheet();

        $worksheet->setCellValue($config->personNameCell, $user->name);
        $worksheet->setCellValue($config->monthHeaderCell, $config->generationDate->format($config->monthHeaderFormat));

        $entriesBuffer = collect();
        $currentRow = $config->initialRow;
        $currentDate = $config->generationDate->clone();
        while ($currentDate->month === $config->generationDate->month) {
            $currentCol = $config->initialCol;
            $entries = $this->getEntriesByDay($currentDate, $user, $config);
            if ($entries) {
                $indexedEntries = [];
                foreach ($entries as $entry) {
                    $indexedEntries[] = [
                        'col' => $currentCol,
                        'row' => $currentRow,
                        'ts' => $entry
                    ];
                    $currentCol++;
                }
                $entriesBuffer->push($indexedEntries);
            }
            $currentDate->addDay();
            $currentRow++;
        }

        if ($config->targetHours !== 0) {
            Log::info("Adjusting timestamps to fit in target {$config->targetHours} hours");
            $totalMonthMinutes = 0;
            foreach ($entriesBuffer as $dayEntries) {
                $workTime = $dayEntries[0]['ts']->diffInMinutes($dayEntries[3]['ts']);
                $lunchTime = $dayEntries[1]['ts']->diffInMinutes($dayEntries[2]['ts']);
                $totalMonthMinutes += $workTime - $lunchTime;
            }

            $totalMinutesPerDay = $totalMonthMinutes / $entriesBuffer->count();
            $targetMinutesPerDay = ($config->targetHours * 60) / $entriesBuffer->count();
            $minutesDelta = $totalMinutesPerDay - $targetMinutesPerDay;
            foreach ($entriesBuffer as $dayEntries) {
                $dayEntries[0]['ts']->addMinutes($minutesDelta);
            }
        }

        foreach ($entriesBuffer as $dayEntries) {
            foreach ($dayEntries as $entry) {
                $cell = $worksheet->getCell(sprintf('%s%d', $entry['col'], $entry['row']));
                $cell->setValue($entry['ts']->format('H:i'));
            }
        }

        $outputFilename = sprintf(
            'generated%s%s Timesheet - %s[%d]%s.%s',
            DIRECTORY_SEPARATOR,
            $config->generationDate->format('m. F'),
            $user->first_name,
            $user->id,
            $config->targetHours === 0 ? '' : ' [Target]',
            pathinfo($config->templateFile, PATHINFO_EXTENSION)
        );

        Log::info("Saving {$user->first_name} timesheet");
        $writer = IOFactory::createWriter($spreadsheet, ucfirst(pathinfo($config->templateFile, PATHINFO_EXTENSION)));
        $writer->save(Storage::disk('local')->path($outputFilename));

        $message = new SpreadsheetMail();
        $message->attach(Storage::disk('local')->path($outputFilename));
        $recipients = $config->recipientAddresses;
        $recipients[] = $user->email;

        Log::info("Dispatching {$user->first_name} timesheet to:", $recipients);
        $message->to($recipients);
        $message->subject(
            sprintf(
                "%s's %stimesheet %s",
                $user->first_name,
                $config->targetHours === 0 ? '' : 'Target ',
                $config->generationDate->format('F Y')
            )
        );
        Mail::queue($message);
    }

    /**
     * Builds the Config Object to be used
     *
     * @return TimesheetCommandConfig
     */
    private function buildConfig()
    {
        $generationDate = Carbon::now()->subMonth()->day(1);

        if (is_numeric($this->argument('year')) && ($this->argument('year') <= 1000 || $this->argument('year') >= 3000)) {
            throw new InvalidJobArgumentException(sprintf('"%d" is not a valid year.', $this->argument('year')));
        }

        if (is_numeric($this->argument('month')) && ($this->argument('month') <= 0 || $this->argument('month') >= 13)) {
            throw new InvalidJobArgumentException(sprintf('"%d" is not a valid month.', $this->argument('month')));
        }

        if ($this->argument('month') !== null && $this->argument('year') === null) {
            $currentYear = Carbon::now()->format('Y');
            $generationDate = Carbon::parse(sprintf('%d-%d-01', $currentYear, $this->argument('month')));
        }

        if ($this->argument('year') !== null) {
            $generationDate = Carbon::parse(sprintf('%d-%d-01', $this->argument('year'), $this->argument('month')));
        }

        $configuredHeaderCell = AppSettingRepository::get(AppSetting::SPREADSHEET_HEADER_MONTH_CELL);
        if (!$configuredHeaderCell) {
            throw new ConfigurationException(sprintf('Missing %s configuration', AppSetting::SPREADSHEET_HEADER_MONTH_CELL));
        }

        $configuredPersonNameCell = AppSettingRepository::get(AppSetting::SPREADSHEET_HEADER_PERSON_NAME);
        if (!$configuredPersonNameCell) {
            throw new ConfigurationException(sprintf('Missing %s configuration', AppSetting::SPREADSHEET_HEADER_PERSON_NAME));
        }

        $configuredHeaderFormat = AppSettingRepository::get(AppSetting::SPREADSHEET_HEADER_MONTH_FORMAT);
        if (!$configuredHeaderFormat) {
            Log::warning('Using default Y-m-d as header format');
            $configuredHeaderFormat = (object) ['value' => 'Y-m-d'];
        }

        $configuredTemplate = AppSettingRepository::get(AppSetting::SPREADSHEET_CURRENT_TEMPLATE_FILENAME);
        if (!$configuredTemplate) {
            throw new ConfigurationException(sprintf('Missing %s configuration', AppSetting::SPREADSHEET_CURRENT_TEMPLATE_FILENAME));
        }

        $configuredInitialRow = AppSettingRepository::get(AppSetting::SPREADSHEET_INITIAL_ROW);
        if (!$configuredInitialRow) {
            throw new ConfigurationException(sprintf('Missing %s configuration', AppSetting::SPREADSHEET_INITIAL_ROW));
        }

        $configuredInitialColumn = AppSettingRepository::get(AppSetting::SPREADSHEET_INITIAL_COLUMN);
        if (!$configuredInitialColumn) {
            throw new ConfigurationException(sprintf('Missing %s configuration', AppSetting::SPREADSHEET_INITIAL_COLUMN));
        }

        $configuredRecipients = AppSettingRepository::get(AppSetting::SPREADSHEET_GENERATION_EMAILS_REAL_RECIPIENTS);
        $configuredRecipients = array_filter(explode(',', $configuredRecipients));
        if (!$configuredRecipients) {
            Log::warning('No Recipients configured!');
        }

        if (!Storage::disk('local')->exists($configuredTemplate)) {
            throw new ConfigurationException(sprintf('File "%s" on configuration does not exist', $configuredTemplate));
        }

        Storage::disk('local')->makeDirectory('generated');
        $filePath = Storage::disk('local')->path($configuredTemplate);


        $targetHours = AppSettingRepository::get(AppSetting::SPREADSHEET_GENERATION_TARGET_HOURS);
        if ($this->option('target') && !$targetHours) {
            throw new ConfigurationException(sprintf('Missing %s configuration', AppSetting::SPREADSHEET_GENERATION_TARGET_HOURS));
        }

        if (!$this->option('target')) {
            $targetHours = 0;
        }

        $targetLunchMinutes = AppSettingRepository::get(AppSetting::TARGET_LUNCH_TIME);

        return new TimesheetCommandConfig(
            $generationDate,
            $configuredHeaderCell,
            $configuredPersonNameCell,
            $configuredHeaderFormat,
            $filePath,
            (int) $configuredInitialRow,
            $configuredInitialColumn,
            $configuredRecipients,
            (int) $targetHours,
            (int) $targetLunchMinutes 
        );
    }

    /**
     * Get normalized timestamps
     *
     * @param Carbon $day
     * @param User $user
     * @return Carbon[]
     */
    private function getEntriesByDay(Carbon $day, User $user, TimesheetCommandConfig $config)
    {
        $entries = TimestampRepository::getByDay($day, $user);
        if ($entries->isEmpty()) {
            return [];
        }

        if ($entries->count() === 1 && $entries->first()->entry) {
            return [
                $entries->first()->carbon,
                $entries->first()->carbon->setTimeFromTimeString('00:00'),
                $entries->first()->carbon->setTimeFromTimeString('00:00'),
                $entries->first()->carbon->addMinute(),
            ];
        }

        if ($entries->count() === 1 && !$entries->first()->entry) {
            return [
                $entries->first()->carbon->subMinute(),
                $entries->first()->carbon->setTimeFromTimeString('00:00'),
                $entries->first()->carbon->setTimeFromTimeString('00:00'),
                $entries->first()->carbon,
            ];
        }

        if ($entries->count() === 2 && $entries->first()->entry && !$entries[1]->entry) {
            return [
                $entries->first()->carbon,
                $entries->first()->carbon->setTimeFromTimeString('00:00'),
                $entries->first()->carbon->setTimeFromTimeString('00:00'),
                $entries[1]->carbon,
            ];
        }

        if ($entries->count() === 2 && !$entries->first()->entry && $entries[1]->entry) {
            return [
                $entries->first()->carbon,
                $entries->first()->carbon->setTimeFromTimeString('00:00'),
                $entries->first()->carbon->setTimeFromTimeString('00:00'),
                $entries[1]->carbon,
            ];
        }

        if ($entries->count() === 2 && $entries->first()->entry && $entries[1]->entry) {
            return [
                $entries->first()->carbon,
                $entries[1]->carbon->subMinute(),
                $entries[1]->carbon,
                $entries[1]->carbon->addMinute(),
            ];
        }

        if ($entries->count() === 2 && !$entries->first()->entry && !$entries[1]->entry) {
            return [
                $entries->first()->carbon->subMinute(),
                $entries->first()->carbon,
                $entries[1]->carbon->subMinute(),
                $entries[1]->carbon,
            ];
        }

        $earliestEntry = TimestampRepository::findEarliestByDay($day, $user, Timestamp::ENTRY_STATE_ENTER);
        $latestEntry = TimestampRepository::findLatestByDay($day, $user, Timestamp::ENTRY_STATE_ENTER);
        $earliestExit = TimestampRepository::findEarliestByDay($day, $user, Timestamp::ENTRY_STATE_EXIT);
        $latestExit = TimestampRepository::findLatestByDay($day, $user, Timestamp::ENTRY_STATE_EXIT);

        if (empty($earliestEntry)) {
            return [
                $entries->first()->carbon->subMinute(),
                $entries->first()->carbon,
                $entries[$entries->count() - 1]->carbon->subMinute(),
                $entries[$entries->count() - 1]->carbon,
            ];
        }

        if (empty($earliestExit)) {
            return [
                $entries->first()->carbon,
                $entries->first()->carbon->addMinute(),
                $entries[$entries->count() - 1]->carbon,
                $entries[$entries->count() - 1]->carbon->addMinute(),
            ];
        }

        $timeInsideInMinutes = Calculator::timeInside($day, $user);

        $rawSum = $earliestEntry->carbon->diffInMinutes($latestExit->carbon);

        $lunchTimeInMinutes = $rawSum - $timeInsideInMinutes;

        if ($latestExit->id === $earliestExit->id) {
            return [
                $earliestEntry->carbon,
                $latestEntry->carbon->subMinutes($lunchTimeInMinutes),
                $latestEntry->carbon,
                $latestExit->carbon,
            ];
        }

        if ($config->targetLunchMinutes !== 0 && $lunchTimeInMinutes < $config->targetLunchMinutes) {
            $minimumLunchTimeDiff = $config->targetLunchMinutes - $lunchTimeInMinutes;
            Log::info("[Day {$day->format('d')}]: Adding {$minimumLunchTimeDiff} minutes on lunch time to complete {$config->targetLunchMinutes}");

            return [
                $earliestEntry->carbon,
                $earliestExit->carbon,
                $earliestExit->carbon->addMinutes($lunchTimeInMinutes)->addMinutes($minimumLunchTimeDiff),
                $latestExit->carbon->addMinutes($minimumLunchTimeDiff),
            ];
        }

        return [
            $earliestEntry->carbon,
            $earliestExit->carbon,
            $earliestExit->carbon->addMinutes($lunchTimeInMinutes),
            $latestExit->carbon,
        ];
    }
}
