document.addEventListener('DOMContentLoaded', function() {
    // Check if we're using Arabic
    const isArabic = document.documentElement.lang === 'ar';
    
    if (isArabic) {
        // Find all links and buttons in the profile dropdown
        const menuItems = {
            'Admin Dashboard': 'لوحة تحكم المسؤول',
            'Admin Panel': 'لوحة الإدارة',
            'Profile': 'الملف الشخصي',
            'My Orders': 'طلباتي',
            'Logout': 'تسجيل الخروج'
        };
        
        // Replace text in all elements
        document.querySelectorAll('a, button').forEach(element => {
            const text = element.textContent.trim();
            if (menuItems[text]) {
                element.textContent = menuItems[text];
            }
        });
    }
}); 