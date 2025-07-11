<?php

namespace App\Helpers;

use Illuminate\Support\Facades\App;

class TimeHelper
{
    /**
     * Translate time format to the current locale
     *
     * @param string $timeString The time string to translate (e.g. "9:00 AM - 5:00 PM" or "Closed")
     * @return string The translated time string
     */
    public static function translateTimeFormat($timeString)
    {
        // Handle "Closed" case
        if (strtolower($timeString) === 'closed') {
            return __('time.closed');
        }
        
        // Handle time ranges like "9:00 AM - 5:00 PM"
        if (strpos($timeString, ' - ') !== false) {
            $parts = explode(' - ', $timeString);
            $startTime = self::translateTimePart($parts[0]);
            $endTime = self::translateTimePart($parts[1]);
            return $startTime . ' - ' . $endTime;
        }
        
        // Handle single time
        return self::translateTimePart($timeString);
    }
    
    /**
     * Translate a single time part (e.g. "9:00 AM")
     *
     * @param string $timePart The time part to translate
     * @return string The translated time part
     */
    private static function translateTimePart($timePart)
    {
        // Replace AM/PM with localized versions
        $timePart = str_replace(' AM', ' ' . __('time.am'), $timePart);
        $timePart = str_replace(' PM', ' ' . __('time.pm'), $timePart);
        
        return $timePart;
    }
    
    /**
     * Translate day name to the current locale
     *
     * @param string $day The English day name
     * @return string The translated day name
     */
    public static function translateDay($day)
    {
        // Map day names to translation keys
        $dayMap = [
            'Monday' => 'monday',
            'Tuesday' => 'tuesday',
            'Wednesday' => 'wednesday',
            'Thursday' => 'thursday',
            'Friday' => 'friday',
            'Saturday' => 'saturday',
            'Sunday' => 'sunday'
        ];
        
        $key = $dayMap[$day] ?? strtolower($day);
        return __("time.$key");
    }
} 