<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Facades\Settings;

class CheckAuthSettings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auth:settings-check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the current status of authentication-related settings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $registrationEnabled = Settings::get('enable_registration', '1') == '1';
        $socialLoginEnabled = Settings::get('enable_social_login', '1') == '1';

        $this->info('Authentication Settings Status:');
        $this->info('--------------------------------');
        $this->info('User Registration: ' . ($registrationEnabled ? 'ENABLED' : 'DISABLED'));
        $this->info('Social Login: ' . ($socialLoginEnabled ? 'ENABLED' : 'DISABLED'));
        $this->info('--------------------------------');
        
        $this->info('Middleware Status:');
        if ($registrationEnabled) {
            $this->info('Registration routes are currently accessible.');
        } else {
            $this->warn('Registration routes are currently blocked by middleware.');
        }
        
        if ($socialLoginEnabled) {
            $this->info('Social login routes are currently accessible.');
        } else {
            $this->warn('Social login routes are currently blocked by middleware.');
        }
        
        return Command::SUCCESS;
    }
} 