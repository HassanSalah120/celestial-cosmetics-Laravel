<?php

namespace App\Http\Controllers;

use App\Models\LegalPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\App;

class LegalPageController extends Controller
{
    /**
     * Display a specific legal page.
     *
     * @param string $type
     * @return \Illuminate\View\View
     */
    public function show($type)
    {
        // Log the current locale and RTL status
        $isRtl = is_rtl();
        Log::info('Legal page language info', [
            'current_locale' => app()->getLocale(),
            'is_rtl' => $isRtl,
            'session_locale' => session('locale', 'not set'),
            'session_direction' => session('text_direction', 'not set')
        ]);
        
        $page = LegalPage::where('type', $type)
                        ->where('is_active', true)
                        ->firstOrFail();
        
        // Determine which content to use based on RTL status
        
        // Get title based on RTL status
        $title = $isRtl && !empty($page->title_ar) ? $page->title_ar : $page->title;
        
        // Get content based on RTL status
        $content = $isRtl && !empty($page->content_ar) ? $page->content_ar : $page->content;
        
        // Log the content being used
        Log::info('Legal page content info', [
            'is_rtl' => $isRtl,
            'title_used' => $title,
            'has_arabic_title' => !empty($page->title_ar),
            'has_arabic_content' => !empty($page->content_ar),
            'content_length' => strlen($content)
        ]);
        
        return view('legal.show', [
            'page' => $page,
            'title' => $title,
            'content' => $content
        ]);
    }

    /**
     * Display the terms and conditions page.
     *
     * @return \Illuminate\View\View
     */
    public function terms()
    {
        return $this->show('terms');
    }

    /**
     * Display the privacy policy page.
     *
     * @return \Illuminate\View\View
     */
    public function privacy()
    {
        return $this->show('privacy');
    }

    /**
     * Display the shipping policy page.
     *
     * @return \Illuminate\View\View
     */
    public function shipping()
    {
        return $this->show('shipping');
    }

    /**
     * Display the refund policy page.
     *
     * @return \Illuminate\View\View
     */
    public function refunds()
    {
        return $this->show('refunds');
    }
} 