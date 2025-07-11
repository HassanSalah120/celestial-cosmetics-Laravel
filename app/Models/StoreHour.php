<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class StoreHour extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'day',
        'hours',
        'day_number',
    ];

    /**
     * Get the translated day name.
     *
     * @return string
     */
    public function getTranslatedDayAttribute()
    {
        // Check multiple ways if we're in Arabic mode
        $isArabic = Session::get('locale') === 'ar' || App::getLocale() === 'ar';
        
        if ($isArabic) {
            $dayKey = strtolower($this->day);
            
            // Direct translation mapping for days
            $translations = [
                'monday' => 'الاثنين',
                'tuesday' => 'الثلاثاء',
                'wednesday' => 'الأربعاء',
                'thursday' => 'الخميس',
                'friday' => 'الجمعة',
                'saturday' => 'السبت',
                'sunday' => 'الأحد',
            ];
            
            return $translations[$dayKey] ?? $this->day;
        }
        
        return $this->day;
    }

    /**
     * Get the translated hours.
     *
     * @return string
     */
    public function getTranslatedHoursAttribute()
    {
        // Check multiple ways if we're in Arabic mode
        $isArabic = Session::get('locale') === 'ar' || App::getLocale() === 'ar';
        
        if (strtolower($this->hours) === 'closed') {
            return $isArabic ? 'مغلق' : 'Closed';
        }

        if ($isArabic) {
            $hours = $this->hours;
            $hours = str_replace(' AM', ' ص', $hours);
            $hours = str_replace(' PM', ' م', $hours);
            return $hours;
        }
        
        return $this->hours;
    }
} 