<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SeoComponentController extends Controller
{
    /**
     * Show the SEO component documentation page.
     *
     * @return \Illuminate\View\View
     */
    public function docs()
    {
        return view('admin.docs.seo-component')
            ->with('isAdminPage', true);
    }
}
