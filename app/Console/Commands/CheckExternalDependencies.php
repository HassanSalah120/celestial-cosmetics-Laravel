<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CheckExternalDependencies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'security:dependencies 
                            {--notify : Send notification with results}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check external dependencies for security issues';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking external dependencies for security issues...');
        $issues = [];
        
        // Check Composer dependencies
        $this->checkComposerDependencies($issues);
        
        // Check NPM dependencies if package.json exists
        if (File::exists(base_path('package.json'))) {
            $this->checkNpmDependencies($issues);
        }
        
        // Check CDN resources in views
        $this->checkCdnResources($issues);
        
        // Output results
        if (count($issues) > 0) {
            $this->error(count($issues) . ' security issues found in external dependencies:');
            foreach ($issues as $issue) {
                $this->warn("- {$issue['package']}: {$issue['description']}");
            }
            
            // Log issues
            Log::channel('security')->warning('Security issues found in external dependencies', [
                'count' => count($issues),
                'issues' => $issues
            ]);
            
            // Send notification if requested
            if ($this->option('notify') && config('app.admin_email')) {
                $this->sendNotification($issues);
            }
            
            return 1;
        }
        
        $this->info('No security issues found in external dependencies.');
        return 0;
    }
    
    /**
     * Check Composer dependencies for security issues
     */
    private function checkComposerDependencies(&$issues)
    {
        $this->info('Checking Composer dependencies...');
        
        $process = Process::fromShellCommandline('composer audit --format=json');
        $process->run();
        
        if ($process->isSuccessful()) {
            $output = $process->getOutput();
            $jsonOutput = json_decode($output, true);
            
            if (json_last_error() === JSON_ERROR_NONE && isset($jsonOutput['advisories'])) {
                foreach ($jsonOutput['advisories'] as $advisory) {
                    $issues[] = [
                        'type' => 'composer',
                        'package' => $advisory['package'] ?? 'Unknown Package',
                        'version' => $advisory['version'] ?? 'Unknown',
                        'description' => $advisory['title'] ?? $advisory['cve'] ?? 'Security vulnerability',
                        'severity' => $advisory['severity'] ?? 'unknown'
                    ];
                }
            }
        } else {
            $this->warn('Could not check Composer dependencies: ' . $process->getErrorOutput());
        }
    }
    
    /**
     * Check NPM dependencies for security issues
     */
    private function checkNpmDependencies(&$issues)
    {
        $this->info('Checking NPM dependencies...');
        
        $process = Process::fromShellCommandline('npm audit --json');
        $process->run();
        
        if ($process->isSuccessful()) {
            $output = $process->getOutput();
            $jsonOutput = json_decode($output, true);
            
            if (json_last_error() === JSON_ERROR_NONE && isset($jsonOutput['vulnerabilities'])) {
                foreach ($jsonOutput['vulnerabilities'] as $package => $vuln) {
                    $issues[] = [
                        'type' => 'npm',
                        'package' => $package,
                        'version' => $vuln['via'][0]['version'] ?? 'Unknown',
                        'description' => $vuln['via'][0]['title'] ?? 'Security vulnerability',
                        'severity' => $vuln['via'][0]['severity'] ?? 'unknown'
                    ];
                }
            }
        } else {
            $this->warn('Could not check NPM dependencies: ' . $process->getErrorOutput());
        }
    }
    
    /**
     * Check CDN resources in view files for known vulnerable versions
     */
    private function checkCdnResources(&$issues)
    {
        $this->info('Checking CDN resources in views...');
        
        // Get all view files
        $viewFiles = File::glob(resource_path('views/**/*.{blade.php,php}'), GLOB_BRACE);
        
        // Known vulnerable library versions (example)
        $vulnerableCdns = [
            'jquery' => [
                'pattern' => '/src=["\']https?:\/\/(?:code\.jquery\.com|cdnjs\.cloudflare\.com\/ajax\/libs\/jquery)\/([0-9.]+)\/jquery(?:\.min)?\.js["\']/',
                'versions' => ['<3.5.0' => 'XSS vulnerability CVE-2020-11023']
            ],
            'bootstrap' => [
                'pattern' => '/href=["\']https?:\/\/(?:maxcdn\.bootstrapcdn\.com|cdnjs\.cloudflare\.com\/ajax\/libs)\/bootstrap\/([0-9.]+)\/css\/bootstrap(?:\.min)?\.css["\']/',
                'versions' => ['<4.3.1' => 'XSS vulnerability CVE-2019-8331']
            ]
        ];
        
        foreach ($viewFiles as $file) {
            $content = File::get($file);
            
            foreach ($vulnerableCdns as $library => $data) {
                if (preg_match_all($data['pattern'], $content, $matches, PREG_SET_ORDER)) {
                    foreach ($matches as $match) {
                        $version = $match[1] ?? '';
                        
                        foreach ($data['versions'] as $vulnerableVersion => $description) {
                            if ($this->isVersionVulnerable($version, $vulnerableVersion)) {
                                $issues[] = [
                                    'type' => 'cdn',
                                    'package' => $library,
                                    'version' => $version,
                                    'description' => $description,
                                    'file' => str_replace(base_path(), '', $file),
                                    'severity' => 'high'
                                ];
                            }
                        }
                    }
                }
            }
        }
    }
    
    /**
     * Check if a version is vulnerable based on comparison string
     */
    private function isVersionVulnerable($version, $compareString)
    {
        $operator = substr($compareString, 0, 1);
        
        if (in_array($operator, ['<', '>', '=', '!'])) {
            $compareVersion = substr($compareString, 1);
            return version_compare($version, $compareVersion, $operator);
        } else {
            return version_compare($version, $compareString, '==');
        }
    }
    
    /**
     * Send email notification about security issues
     */
    private function sendNotification($issues)
    {
        $adminEmail = config('app.admin_email');
        if (!$adminEmail) {
            $this->warn('Admin email not configured. Skipping notification.');
            return;
        }
        
        $summary = count($issues) . " security issues found in external dependencies\n\n";
        
        foreach ($issues as $issue) {
            $summary .= "- {$issue['package']} (v{$issue['version']}): {$issue['description']}\n";
        }
        
        // Using Laravel's notification system would be better in a real application
        try {
            Mail::raw($summary, function ($message) use ($adminEmail) {
                $message->to($adminEmail)
                    ->subject('[SECURITY] Vulnerable Dependencies Found');
            });
            $this->info('Notification sent to ' . $adminEmail);
        } catch (\Exception $e) {
            $this->error('Failed to send notification: ' . $e->getMessage());
        }
    }
} 