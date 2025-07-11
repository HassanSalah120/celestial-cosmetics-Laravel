<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StoreHour;
use Illuminate\Http\Request;

class StoreHoursController extends Controller
{
    /**
     * Display a listing of the store hours.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $storeHours = StoreHour::orderBy('id')->get();
        
        return view('admin.store-hours.index', compact('storeHours'));
    }

    /**
     * Show the form for editing the store hours.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        $storeHours = StoreHour::orderBy('id')->get();
        
        return view('admin.store-hours.edit', compact('storeHours'));
    }

    /**
     * Update the store hours.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $request->validate([
            'days' => 'required|array',
            'days.*.id' => 'required|exists:store_hours,id',
            'days.*.hours' => 'required|string|max:100',
        ]);

        foreach ($request->days as $day) {
            StoreHour::where('id', $day['id'])->update([
                'hours' => $day['hours']
            ]);
        }

        return redirect()->route('admin.store-hours.index')
            ->with('success', 'Store hours updated successfully.');
    }
} 