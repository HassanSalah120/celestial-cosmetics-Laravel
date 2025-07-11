/**
 * Check if the current locale is RTL
 * 
 * @return bool
 */
function is_rtl()
{
    $rtlLanguages = ['ar']; // Languages that use RTL direction
    return in_array(app()->getLocale(), $rtlLanguages);
} 