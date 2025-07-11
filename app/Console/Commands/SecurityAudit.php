<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Process\Process;

class SecurityAudit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'security:audit 
                            {--fix : Attempt to fix issues automatically}
                            {--export= : Export results to a file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run a comprehensive security audit on the application';

    /**
     * Results storage
     * 
     * @var array
     */
    protected $results = [
        'passed' => [],
        'failed' => [],
        'warnings' => [],
        'fixed' => []
    ];

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting security audit...');
        $startTime = microtime(true);
        
        $this->checkComposerDependencies();
        $this->checkFilePermissions();
        $this->checkEnvironmentConfig();
        $this->checkApplicationConfig();
        $this->checkDatabaseSecurity();
        $this->checkPasswordPolicies();
        $this->checkStorageSecurity();
        $this->checkSessionSecurity();
        $this->checkCorsConfig();
        
        $duration = round(microtime(true) - $startTime, 2);
        
        // Display summary
        $this->displaySummary($duration);
        
        // Export results if requested
        if ($exportPath = $this->option('export')) {
            $this->exportResults($exportPath);
        }
        
        // Return failure if any checks failed
        return count($this->results['failed']) > 0 ? 1 : 0;
    }

    /**
     * Display summary of results
     *
     * @param float $duration
     * @return void
     */
    private function displaySummary($duration)
    {
        $this->newLine();
        $this->info('Security audit completed in ' . $duration . ' seconds');
        
        $this->newLine();
        $this->info('SUMMARY:');
        $this->info('✓ ' . count($this->results['passed']) . ' checks passed');
        
        if (count($this->results['warnings']) > 0) {
            $this->warn('⚠ ' . count($this->results['warnings']) . ' warnings detected');
        }
        
        if (count($this->results['failed']) > 0) {
            $this->error('✗ ' . count($this->results['failed']) . ' security issues found');
        }
        
        if (count($this->results['fixed']) > 0) {
            $this->info('✓ ' . count($this->results['fixed']) . ' issues automatically fixed');
        }
    }
    
    /**
     * Export results to a file
     *
     * @param string $path
     * @return void
     */
    private function exportResults($path)
    {
        $data = json_encode([
            'timestamp' => now()->toIso8601String(),
            'environment' => app()->environment(),
            'results' => $this->results
        ], JSON_PRETTY_PRINT);
        
        File::put($path, $data);
        $this->info("Results exported to {$path}");
    }
    
    /**
     * Check Composer dependencies
     *
     * @return void
     */
    private function checkComposerDependencies()
    {
        $this->info('Checking for vulnerable dependencies...');
        $process = Process::fromShellCommandline('composer audit');
        $process->run();
        
        if (!$process->isSuccessful()) {
            $this->addResult('failed', 'Vulnerable dependencies found', $process->getOutput());
            
            if ($this->option('fix')) {
                $this->warn('Attempting to update vulnerable dependencies...');
                $updateProcess = Process::fromShellCommandline('composer update');
                $updateProcess->run();
                $this->addResult('fixed', 'Attempted to update vulnerable dependencies', $updateProcess->getOutput());
            }
        } else {
            $this->addResult('passed', 'No vulnerable dependencies found');
        }
    }
    
    /**
     * Check file permissions for sensitive files
     *
     * @return void
     */
    private function checkFilePermissions()
    {
        $this->info('Checking file permissions...');
        $filesToCheck = [
            base_path('.env'),
            base_path('storage/logs'),
            storage_path('framework/sessions'),
            storage_path('framework/views'),
            storage_path('framework/cache')
        ];
        
        foreach ($filesToCheck as $path) {
            if (!file_exists($path)) {
                continue;
            }
            
            $perms = fileperms($path);
            $worldWritable = ($perms & 0x0002) !== 0;
            
            if ($worldWritable) {
                $this->addResult('failed', "Security issue: {$path} is world-writable", "File permissions: " . substr(sprintf('%o', $perms), -4));
                
                if ($this->option('fix')) {
                    $this->warn("Fixing permissions on {$path}");
                    chmod($path, 0755);
                    clearstatcache(true, $path);
                    $newPerms = fileperms($path);
                    $stillWorldWritable = ($newPerms & 0x0002) !== 0;
                    
                    if (!$stillWorldWritable) {
                        $this->addResult('fixed', "Fixed permissions on {$path}", "New permissions: " . substr(sprintf('%o', $newPerms), -4));
                    } else {
                        $this->addResult('failed', "Failed to fix permissions on {$path}");
                    }
                }
            } else {
                $this->addResult('passed', "File permissions good for {$path}", "Permissions: " . substr(sprintf('%o', $perms), -4));
            }
        }
    }
    
    /**
     * Check environment configuration
     *
     * @return void
     */
    private function checkEnvironmentConfig()
    {
        $this->info('Checking environment configuration...');
        $envFile = file_get_contents(base_path('.env'));
        
        // Check if APP_DEBUG is true in production
        if (app()->environment('production') && config('app.debug')) {
            $this->addResult('failed', 'APP_DEBUG is enabled in production', 'Debug mode should be disabled in production for security');
            
            if ($this->option('fix')) {
                $this->warn('Setting APP_DEBUG=false in .env');
                $envFile = preg_replace('/APP_DEBUG=true/i', 'APP_DEBUG=false', $envFile);
                File::put(base_path('.env'), $envFile);
                $this->addResult('fixed', 'Set APP_DEBUG=false in .env file');
            }
        } else {
            $this->addResult('passed', 'APP_DEBUG is properly configured for the current environment');
        }
        
        // Check if APP_ENV is production
        if (strpos($envFile, 'APP_ENV=local') !== false && app()->environment('production')) {
            $this->addResult('failed', 'APP_ENV is set to local in a production environment', 'Environment should be set to production');
        } else {
            $this->addResult('passed', 'APP_ENV is properly configured for the current environment');
        }
        
        // Check APP_KEY length
        if (strlen(config('app.key')) < 32) {
            $this->addResult('failed', 'APP_KEY is too short or not set', 'Application key should be 32 characters');
            
            if ($this->option('fix')) {
                $this->warn('Generating new APP_KEY');
                Artisan::call('key:generate');
                $this->addResult('fixed', 'Generated new APP_KEY');
            }
        } else {
            $this->addResult('passed', 'APP_KEY is properly set');
        }
    }
    
    /**
     * Check application configuration
     *
     * @return void
     */
    private function checkApplicationConfig()
    {
        $this->info('Checking application configuration...');
        
        // Check for HTTPS enforcement
        if (app()->environment('production')) {
            if (!config('session.secure')) {
                $this->addResult('failed', 'Secure cookies are not enforced in production', 'session.secure should be true');
            } else {
                $this->addResult('passed', 'Secure cookies are enforced in production');
            }
            
            // Check if HTTPS is forced
            try {
                $appServiceProviderPath = app_path('Providers/AppServiceProvider.php');
                if (file_exists($appServiceProviderPath)) {
                    $appServiceProviderContent = File::get($appServiceProviderPath);
                    $hasForceScheme = strpos($appServiceProviderContent, 'forceScheme(\'https\')') !== false;
                    
                    if ($hasForceScheme) {
                        $this->addResult('passed', 'HTTPS is forced in production');
                    } else {
                        $this->addResult('failed', 'HTTPS is not forced in production', 'URL::forceScheme() should be used in AppServiceProvider');
                    }
                }
            } catch (\Exception $e) {
                $this->addResult('warning', 'Could not determine if HTTPS is enforced');
            }
        }
        
        // Check session configuration
        if (config('session.encrypt') !== true) {
            $this->addResult('failed', 'Session encryption is disabled', 'session.encrypt should be true');
        } else {
            $this->addResult('passed', 'Session encryption is enabled');
        }
        
        // Check if CSRF protection is enabled
        $kernel = app()->make(\App\Http\Kernel::class);
        $middlewareGroups = $kernel->getMiddlewareGroups();
        
        if (!isset($middlewareGroups['web'])) {
            $this->addResult('failed', 'Web middleware group may be missing (CSRF protection)', 'Check Kernel.php middleware groups');
        } else {
            $webMiddleware = $middlewareGroups['web'];
            
            $hasCsrfProtection = false;
            foreach ($webMiddleware as $middleware) {
                if (is_string($middleware) && 
                   (strpos($middleware, 'VerifyCsrfToken') !== false || 
                    strpos($middleware, 'Csrf') !== false)) {
                    $hasCsrfProtection = true;
                    break;
                }
            }
            
            if ($hasCsrfProtection) {
                $this->addResult('passed', 'CSRF protection is enabled');
            } else {
                $this->addResult('failed', 'CSRF middleware may be disabled', 'VerifyCsrfToken middleware should be enabled in web group');
            }
        }
        
        // Check trusted proxies configuration
        if (property_exists(\Illuminate\Http\Request::class, 'trustedProxies')) {
            $this->addResult('passed', 'Trusted proxies middleware is configured');
        } else {
            $this->addResult('warning', 'Trusted proxies configuration could not be verified', 'Check TrustProxies middleware');
        }
    }
    
    /**
     * Check database security
     *
     * @return void
     */
    private function checkDatabaseSecurity()
    {
        $this->info('Checking database security...');
        
        // Check for transaction isolation level
        try {
            // Check if we can get MySQL variables
            if (DB::connection()->getDriverName() === 'mysql') {
                $isolationLevel = DB::select("SELECT @@GLOBAL.tx_isolation as isolation_level")[0]->isolation_level ?? null;
                $isolationLevel = $isolationLevel ?? DB::select("SELECT @@GLOBAL.transaction_isolation as isolation_level")[0]->isolation_level ?? 'UNKNOWN';
                
                if (in_array($isolationLevel, ['REPEATABLE-READ', 'SERIALIZABLE'])) {
                    $this->addResult('passed', "Database transaction isolation level is secure ({$isolationLevel})");
                } else {
                    $this->addResult('warning', "Database transaction isolation level ({$isolationLevel}) might not be optimal", 'Consider REPEATABLE-READ or SERIALIZABLE for better security');
                }
            }
        } catch (\Exception $e) {
            $this->addResult('warning', 'Could not check database transaction isolation level');
        }
        
        // Check if users table has password hashing
        try {
            if (Schema::hasTable('users') && Schema::hasColumn('users', 'password')) {
                $user = DB::table('users')->first();
                
                if ($user) {
                    $userPasswordHash = $user->password ?? null;
                    
                    if ($userPasswordHash && 
                        strlen($userPasswordHash) > 20 && 
                        (strpos($userPasswordHash, '$2y$') === 0 || strpos($userPasswordHash, '$argon') === 0)) {
                        $this->addResult('passed', 'User passwords are properly hashed using Bcrypt or Argon2');
                    } else {
                        $this->addResult('failed', 'User passwords may not be properly hashed', 'Check password hashing implementation');
                    }
                } else {
                    $this->addResult('passed', 'No users found in database, skipping password hash check');
                }
            }
        } catch (\Exception $e) {
            $this->addResult('warning', 'Could not check user password hashing: ' . $e->getMessage());
        }
    }
    
    /**
     * Check password policies
     *
     * @return void
     */
    private function checkPasswordPolicies()
    {
        $this->info('Checking password policies...');
        
        // Check password validation rules in validation files or controllers
        $passwordRulesFound = false;
        $strongPasswordsFound = false;
        
        // Check in Auth controllers
        $authControllerFiles = [
            app_path('Http/Controllers/Auth/RegisterController.php'),
            app_path('Http/Controllers/Auth/ResetPasswordController.php'),
            app_path('Http/Controllers/UserController.php'),
            app_path('Http/Controllers/Auth/LoginController.php')
        ];
        
        foreach ($authControllerFiles as $file) {
            if (file_exists($file)) {
                $content = File::get($file);
                
                if (strpos($content, 'password') !== false) {
                    $passwordRulesFound = true;
                    
                    // Check for Laravel Password rules class
                    if ((strpos($content, 'Password::min') !== false || 
                         strpos($content, 'PasswordRule::min') !== false) &&
                        (strpos($content, '->mixedCase()') !== false &&
                         strpos($content, '->letters()') !== false &&
                         strpos($content, '->numbers()') !== false &&
                         strpos($content, '->symbols()') !== false)) {
                        $strongPasswordsFound = true;
                        $this->addResult('passed', "Strong password rules found in " . basename($file));
                    } 
                    // Check for regex pattern for strong passwords
                    else if (strpos($content, 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])') !== false) {
                        $strongPasswordsFound = true;
                        $this->addResult('passed', "Strong password rules found in " . basename($file));
                    } 
                    // Skip validation check for login controller
                    else if (basename($file) === 'LoginController.php') {
                        continue;
                    } 
                    else {
                        $this->addResult('warning', "Password rules in " . basename($file) . " could be stronger", 
                            "Consider adding complexity requirements with Password::min(8)->mixedCase()->letters()->numbers()->symbols()");
                    }
                }
            }
        }
        
        // Check in validation files if no strong password rules found
        if (!$strongPasswordsFound) {
            $validationFiles = File::glob(app_path('Http/Requests/*.php'));
            foreach ($validationFiles as $file) {
                $content = File::get($file);
                
                if (strpos($content, 'password') !== false) {
                    $passwordRulesFound = true;
                    
                    if (preg_match('/[\'"]password[\'"]\s*=>\s*\[(.*?)]/s', $content, $matches)) {
                        $passwordRules = $matches[1];
                        
                        $this->checkPasswordRuleStrength($passwordRules, basename($file));
                    }
                }
            }
        }
        
        if (!$passwordRulesFound) {
            $this->addResult('warning', 'Could not locate password validation rules', 'Make sure strong password policies are enforced');
        }
    }
    
    /**
     * Check password rule strength
     *
     * @param string $rules
     * @param string $file
     * @return void
     */
    private function checkPasswordRuleStrength($rules, $file)
    {
        $hasMinLength = preg_match('/min:(\d+)/', $rules, $minMatches);
        $minLength = $hasMinLength ? (int)$minMatches[1] : 0;
        
        $hasRequiredChars = strpos($rules, 'regex:') !== false || strpos($rules, 'confirmed') !== false || strpos($rules, 'Rules\Password') !== false;
        
        if ($minLength >= 8 && $hasRequiredChars) {
            $this->addResult('passed', "Strong password rules found in {$file}", "Minimum length: {$minLength}");
        } elseif ($minLength >= 6) {
            $this->addResult('warning', "Password rules in {$file} could be stronger", "Current minimum length: {$minLength}");
        } else {
            $this->addResult('failed', "Weak password rules in {$file}", "Minimum length should be at least 8 characters with complexity requirements");
        }
    }
    
    /**
     * Check storage security
     *
     * @return void
     */
    private function checkStorageSecurity()
    {
        $this->info('Checking storage security...');
        
        // Check for public storage symlink
        $publicStoragePath = public_path('storage');
        if (!file_exists($publicStoragePath)) {
            $this->addResult('warning', 'Public storage symlink not found', 'Run "php artisan storage:link" if you need publicly accessible storage');
        } else {
            $this->addResult('passed', 'Public storage symlink exists');
            
            // Check for sensitive files in public storage
            $sensitiveExtensions = ['sql', 'env', 'log', 'bak', 'config', 'private'];
            $sensitiveFiles = [];
            
            foreach ($sensitiveExtensions as $ext) {
                $files = File::glob(public_path("storage/**/*.{$ext}"), GLOB_BRACE);
                $sensitiveFiles = array_merge($sensitiveFiles, $files);
            }
            
            if (count($sensitiveFiles) > 0) {
                $this->addResult('failed', count($sensitiveFiles) . ' potentially sensitive files found in public storage', implode(', ', array_map('basename', $sensitiveFiles)));
            } else {
                $this->addResult('passed', 'No sensitive files found in public storage');
            }
        }
    }
    
    /**
     * Check session security
     *
     * @return void
     */
    private function checkSessionSecurity()
    {
        $this->info('Checking session security...');
        
        // Check session settings
        $sessionDriver = config('session.driver');
        if ($sessionDriver === 'cookie') {
            $this->addResult('warning', 'Session driver is set to "cookie"', 'Consider using a server-side session driver (file, database, redis)');
        } else {
            $this->addResult('passed', "Session driver is set to server-side ({$sessionDriver})");
        }
        
        // Check cookie settings
        if (config('session.secure') && config('session.http_only')) {
            $this->addResult('passed', 'Session cookies have secure and http_only flags enabled');
        } elseif (!config('session.secure')) {
            $this->addResult('failed', 'Session cookie secure flag is disabled', 'Enable secure flag for cookies in config/session.php');
        } elseif (!config('session.http_only')) {
            $this->addResult('failed', 'Session cookie http_only flag is disabled', 'Enable http_only flag for cookies in config/session.php');
        }
        
        // Check SameSite attribute
        $sameSite = config('session.same_site', null);
        if ($sameSite === 'lax' || $sameSite === 'strict') {
            $this->addResult('passed', "Session cookie SameSite attribute is set to {$sameSite}");
        } else {
            $this->addResult('warning', "Session cookie SameSite attribute is not optimal ({$sameSite})", 'Consider using "lax" or "strict"');
        }
    }
    
    /**
     * Check CORS configuration
     *
     * @return void
     */
    private function checkCorsConfig()
    {
        $this->info('Checking CORS configuration...');
        
        $corsConfig = config('cors', null);
        if ($corsConfig === null) {
            $this->addResult('warning', 'CORS configuration not found', 'Make sure CORS is properly configured if your application needs it');
            return;
        }
        
        // Check allowed origins
        $allowedOrigins = $corsConfig['allowed_origins'] ?? ['*'];
        if (in_array('*', $allowedOrigins)) {
            $this->addResult('warning', 'CORS allows any origin (*)', 'Specify exact origins for better security');
        } else {
            $this->addResult('passed', 'CORS allowed origins are restricted', 'Allowed origins: ' . implode(', ', $allowedOrigins));
        }
        
        // Check allowed methods
        $allowedMethods = $corsConfig['allowed_methods'] ?? ['*'];
        if (in_array('*', $allowedMethods)) {
            $this->addResult('warning', 'CORS allows any HTTP method (*)', 'Specify exact methods for better security');
        } else {
            $this->addResult('passed', 'CORS allowed methods are restricted', 'Allowed methods: ' . implode(', ', $allowedMethods));
        }
    }
    
    /**
     * Add a result to the results array
     *
     * @param string $type
     * @param string $message
     * @param string|null $details
     * @return void
     */
    private function addResult($type, $message, $details = null)
    {
        switch ($type) {
            case 'passed':
                $this->results['passed'][] = ['message' => $message, 'details' => $details];
                $this->line('<fg=green>✓</> ' . $message);
                break;
            case 'warning':
                $this->results['warnings'][] = ['message' => $message, 'details' => $details];
                $this->line('<fg=yellow>⚠</> ' . $message);
                break;
            case 'failed':
                $this->results['failed'][] = ['message' => $message, 'details' => $details];
                $this->line('<fg=red>✗</> ' . $message);
                break;
            case 'fixed':
                $this->results['fixed'][] = ['message' => $message, 'details' => $details];
                $this->line('<fg=green>✓</> Fixed: ' . $message);
                break;
        }
        
        // Show details if available
        if ($details && $type !== 'passed') {
            $this->line('  <fg=gray>' . $details . '</>');
        }
    }
} 