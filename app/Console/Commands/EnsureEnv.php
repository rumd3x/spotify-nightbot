<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class EnsureEnv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'env:ensure';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ensure .env file exists and is populated properly and consistently';

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
        $appKey = env('APP_KEY');
        if ($appKey === null) {
            $this->info('APP KEY non existent, creating entry...');
            $this->setEnvironmentValue('APP_KEY', '');
        }

        if (empty($appKey)) {
            $this->info('Generating APP KEY...');
            Artisan::call('key:generate', ['--show' => true]);
            $key = Artisan::output();
            $this->setEnvironmentValue('APP_KEY', trim($key));
        }

        $this->setEnvironmentValue('MAIL_DRIVER', 'smtp');
        $this->setEnvironmentValue('MAIL_HOST', $this->getOSEnvVar('MAIL_HOST'));
        $this->setEnvironmentValue('MAIL_PORT', $this->getOSEnvVar('MAIL_PORT'));
        $this->setEnvironmentValue('MAIL_USERNAME', $this->getOSEnvVar('MAIL_USERNAME'));
        $this->setEnvironmentValue('MAIL_FROM_ADDRESS', $this->getOSEnvVar('MAIL_FROM_ADDRESS'));
        $this->setEnvironmentValue('MAIL_FROM_NAME', $this->getOSEnvVar('MAIL_FROM_NAME'));
        $this->setEnvironmentValue('MAIL_PASSWORD', $this->getOSEnvVar('MAIL_PASSWORD'));
        $this->setEnvironmentValue('MAIL_ENCRYPTION', $this->getOSEnvVar('MAIL_ENCRYPTION'));

        $this->setEnvironmentValue('DB_HOST', $this->getOSEnvVar('MYSQL_HOST'));
        $this->setEnvironmentValue('DB_USER', $this->getOSEnvVar('MYSQL_USER'));
        $this->setEnvironmentValue('DB_PASS', $this->getOSEnvVar('MYSQL_PASS'));

        $this->setEnvironmentValue('SPOTIFY_ID', $this->getOSEnvVar('SPOTIFY_ID'));
        $this->setEnvironmentValue('SPOTIFY_SECRET', $this->getOSEnvVar('SPOTIFY_SECRET'));   

        $this->info('Environment ok!');
    }

    private function getOSEnvVar(string $var, $default = '')
    {
        $value = getenv($var) ?: $default;
        if (strpos($value, ' ') !== false) {
            $value = "'$value'";
        }
        return $value;
    }

    private function setEnvironmentValue(string $key, string $value)
    {
        $key = strtoupper($key);
        $file_content = explode("\n", File::get('.env'));

        $found = false;
        for ($i = 0; $i < count($file_content); $i++) {
            if (strpos(strtoupper($file_content[$i]), $key) !== false) {
                $found = true;
                $file_content[$i] = "$key=$value";
            }
        }

        if ($found) {
            File::put('.env', implode("\n", $file_content));
            return;
        }

        File::append('.env', "$key=$value\n");
    }
}
