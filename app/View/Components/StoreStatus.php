<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\StoreHour;
use Carbon\Carbon;

class StoreStatus extends Component
{
    /**
     * Whether the store is currently open.
     *
     * @var bool
     */
    public $isOpen;

    /**
     * The current day name.
     *
     * @var string
     */
    public $currentDayName;

    /**
     * The timezone used for store hours.
     *
     * @var string
     */
    public $storeTimezone;

    /**
     * Today's store hours.
     *
     * @var string
     */
    public $todayHours;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->storeTimezone = 'Africa/Cairo';
        $this->determineStoreStatus();
    }

    /**
     * Determine if the store is currently open or closed.
     *
     * @return void
     */
    private function determineStoreStatus()
    {
        // Default to closed
        $this->isOpen = false;
        
        // Get current time in store timezone
        $currentTime = now()->timezone($this->storeTimezone);
        $this->currentDayName = $currentTime->format('l'); // Gets current day name (Monday, Tuesday, etc.)
        
        // Find today's store hours
        $todayHours = StoreHour::where('day', $this->currentDayName)->first();
        
        if ($todayHours) {
            $this->todayHours = $todayHours->hours;
            
            if (strtolower($todayHours->hours) !== 'closed') {
                // Parse opening and closing times
                $parts = explode(' - ', $todayHours->hours);
                
                if (count($parts) == 2) {
                    $openingTime = $this->parseTimeString($parts[0]);
                    $closingTime = $this->parseTimeString($parts[1]);
                    
                    // Check if current time is between opening and closing times
                    if ($openingTime && $closingTime) {
                        $currentTimeObj = Carbon::createFromTimeString($currentTime->format('H:i'), $this->storeTimezone);
                        $this->isOpen = $currentTimeObj->between($openingTime, $closingTime);
                    }
                }
            }
        }
    }
    
    /**
     * Parse a time string like "9:00 AM" into a Carbon object
     *
     * @param string $timeString
     * @return \Carbon\Carbon|null
     */
    private function parseTimeString($timeString)
    {
        try {
            return Carbon::createFromFormat('g:i A', $timeString, $this->storeTimezone);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.store-status');
    }
} 