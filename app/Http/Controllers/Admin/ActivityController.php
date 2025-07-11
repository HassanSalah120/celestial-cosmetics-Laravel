<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Activity;

class ActivityController extends Controller
{
    /**
     * Display a listing of activity logs.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Get all activities and paginate them
        $activities = Activity::orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('admin.activities.index', compact('activities'));
    }

    /**
     * Display the specified activity log.
     *
     * @param  \App\Models\Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function show(Activity $activity)
    {
        return view('admin.activities.show', compact('activity'));
    }
} 