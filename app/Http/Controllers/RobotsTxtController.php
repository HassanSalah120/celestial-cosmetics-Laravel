<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RobotsTxtRule;
use App\Helpers\SettingsHelper;

class RobotsTxtController extends Controller
{
    /**
     * Generate and return the robots.txt content.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Check if we have custom rules
        $rulesCount = RobotsTxtRule::count();
        
        if ($rulesCount > 0) {
            // Generate from rules
            $content = RobotsTxtRule::generateRobotsTxt();
        } else {
            // Use content from settings
            $content = SettingsHelper::get('robots_txt_content', '');
            
            // If content is empty, generate a default one
            if (empty($content)) {
                $content = "User-agent: *\n";
                $content .= "Disallow: /admin\n";
                $content .= "Disallow: /login\n";
                $content .= "Disallow: /register\n";
                $content .= "Disallow: /cart\n";
                $content .= "Allow: /\n";
                $content .= "Sitemap: " . url('/sitemap.xml');
            }
        }
        
        return response($content, 200)->header('Content-Type', 'text/plain');
    }
} 