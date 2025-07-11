<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FooterLink extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'footer_links';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'column_id',
        'title',
        'url',
        'sort_order',
    ];

    /**
     * Get the section/column this link belongs to
     */
    public function column()
    {
        return $this->belongsTo(FooterSection::class, 'column_id');
    }

    /**
     * Get the localized title
     */
    public function getLocalizedTitleAttribute()
    {
        $isArabic = is_rtl();
        
        // Common translations
        if ($isArabic) {
            $translations = [
                'Home' => 'الرئيسية',
                'Products' => 'المنتجات',
                'About Us' => 'من نحن',
                'Contact' => 'اتصل بنا',
                'New Arrivals' => 'وصل حديثًا',
                'Terms & Conditions' => 'الشروط والأحكام',
                'Privacy Policy' => 'سياسة الخصوصية',
                'Shipping Policy' => 'سياسة الشحن',
                'Refunds' => 'سياسة الاسترداد',
                'FAQ' => 'الأسئلة الشائعة',
                'Blog' => 'المدونة',
                'Categories' => 'الفئات',
                'Special Offers' => 'عروض خاصة',
                'Newsletter' => 'النشرة الإخبارية',
                'Skincare' => 'العناية بالبشرة',
                'Makeup' => 'مكياج',
                'Body Care' => 'العناية بالجسم'
            ];
            
            if (isset($translations[$this->title])) {
                return $translations[$this->title];
            }
        }
        
        // Since we don't have separate fields for translations in this table,
        // we'll use the translation system
        if (!empty($this->translation_key)) {
            return __($this->translation_key);
        }
        
        return $this->title;
    }
}
