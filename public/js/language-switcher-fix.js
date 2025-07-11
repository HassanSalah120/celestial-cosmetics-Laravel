document.addEventListener('DOMContentLoaded', function() {
    // Fix language switcher display
    const fixLanguageSwitcher = function() {
        const currentLang = document.documentElement.lang.toLowerCase();
        const languageSwitchers = document.querySelectorAll('.language-switcher span');
        
        languageSwitchers.forEach(function(switcher) {
            if (currentLang === 'ar') {
                switcher.textContent = 'AR';
            } else {
                switcher.textContent = 'EN';
            }
        });
        
        // Fix language dropdown highlighting
        const languageDropdownItems = document.querySelectorAll('.dropdown-menu a');
        languageDropdownItems.forEach(function(item) {
            const href = item.getAttribute('href');
            if (!href) return;
            
            // Remove all active classes first
            item.classList.remove('bg-teal-50', 'text-teal-700', 'font-medium');
            item.classList.add('text-gray-700', 'hover:bg-teal-50', 'hover:text-teal-700', 'border-transparent');
            
            // Check if this is the current language
            if ((currentLang === 'ar' && href.includes('/language/ar')) || 
                (currentLang !== 'ar' && href.includes('/language/en'))) {
                item.classList.remove('text-gray-700');
                item.classList.add('bg-teal-50', 'text-teal-700', 'font-medium', 'border-accent');
            }
        });
    };
    
    // Run immediately
    fixLanguageSwitcher();
    
    // Also run after a short delay to ensure it catches any dynamic content
    setTimeout(fixLanguageSwitcher, 500);
}); 