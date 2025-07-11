<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\NewsletterController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ActivityController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Middleware\CheckRegistrationEnabled;
use App\Http\Middleware\CheckSocialLoginEnabled;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\Admin\SeoComponentController;
use App\Http\Controllers\Admin\HomepageController;
use App\Http\Controllers\Admin\OfferController;
use App\Http\Controllers\OffersController;
use App\Http\Controllers\LegalPageController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\ThemeController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\Admin\HeaderNavigationController;
use App\Http\Controllers\WishlistController;

// Language Routes - Make both GET and POST available
Route::get('/language/{locale}', [App\Http\Controllers\LanguageController::class, 'switchLanguage'])->name('locale.switch');
Route::post('/language/{locale}', [App\Http\Controllers\LanguageController::class, 'switchLanguage'])->name('language.switch.post');
Route::get('/set-locale/{locale}', [App\Http\Controllers\LanguageController::class, 'switchLanguage'])->name('set-locale');

// Main Pages
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [App\Http\Controllers\AboutController::class, 'index'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::post('/contact', [App\Http\Controllers\ContactController::class, 'submit'])->name('contact.submit');
Route::get('/testimonials', [HomeController::class, 'testimonials'])->name('testimonials');
Route::post('/testimonials/submit', [HomeController::class, 'submitTestimonial'])->name('testimonials.submit');

// Offers Routes
Route::get('/offers', [OffersController::class, 'index'])->name('offers.index');

// Product Routes
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/categories', [ProductController::class, 'categories'])->name('categories.index');
Route::get('/products/category/{slug}', [ProductController::class, 'category'])->name('products.category');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/search', [ProductController::class, 'search'])->name('products.search');
Route::get('/api/search/autocomplete', [ProductController::class, 'autocomplete'])->name('products.autocomplete');

// Review Routes
Route::middleware(['auth', 'verified'])->group(function () {
    // General testimonials only (overall brand experience)
    Route::post('/reviews', [App\Http\Controllers\ReviewController::class, 'store'])->name('reviews.submit');
    Route::put('/reviews/{review}', [App\Http\Controllers\ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/reviews/{review}', [App\Http\Controllers\ReviewController::class, 'destroy'])->name('reviews.destroy');
});

// Cart Routes
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update/{product}', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove/{product}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/add-offer/{offer}', [CartController::class, 'addOffer'])->name('cart.add.offer');
Route::post('/cart/remove-offer/{offer}', [CartController::class, 'removeOffer'])->name('cart.remove.offer');
Route::get('/cart/mini', [CartController::class, 'miniCart'])->name('cart.mini');

// Wishlist Routes
Route::get('/wishlist', [App\Http\Controllers\WishlistController::class, 'index'])->name('wishlist.index');
Route::post('/wishlist/add/{product}', [App\Http\Controllers\WishlistController::class, 'add'])->name('wishlist.add');
Route::delete('/wishlist/remove/{product}', [App\Http\Controllers\WishlistController::class, 'remove'])->name('wishlist.remove');
Route::get('/wishlist/check/{product}', [App\Http\Controllers\WishlistController::class, 'check'])->name('wishlist.check');
Route::delete('/wishlist/clear', [App\Http\Controllers\WishlistController::class, 'clear'])->name('wishlist.clear');

// Newsletter Routes
Route::post('/newsletter/subscribe', [App\Http\Controllers\NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');
Route::get('/newsletter/unsubscribe/{token}', [App\Http\Controllers\NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');

// Admin Newsletter Routes
Route::prefix('admin/newsletters')->name('admin.newsletters.')->middleware(['auth', 'verified', 'permission:manage_settings'])->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\NewsletterController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\Admin\NewsletterController::class, 'create'])->name('create');
    Route::post('/send', [App\Http\Controllers\Admin\NewsletterController::class, 'send'])->name('send');
    Route::get('/{subscriber}', [App\Http\Controllers\Admin\NewsletterController::class, 'show'])->name('show');
    Route::delete('/{subscriber}', [App\Http\Controllers\Admin\NewsletterController::class, 'destroy'])->name('destroy');
    Route::patch('/{subscriber}/toggle-status', [App\Http\Controllers\Admin\NewsletterController::class, 'toggleStatus'])->name('toggle-status');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login']);
    
    // Registration routes with registration check middleware
    Route::middleware([CheckRegistrationEnabled::class])->group(function() {
        Route::get('/register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
        Route::post('/register', [App\Http\Controllers\Auth\RegisterController::class, 'register']);
    });
    
    // Social Authentication Routes with social login check middleware
    Route::middleware([CheckSocialLoginEnabled::class])->group(function() {
        Route::get('/auth/google', [App\Http\Controllers\Auth\SocialAuthController::class, 'redirectToGoogle'])->name('auth.google');
        Route::get('/auth/google/callback', [App\Http\Controllers\Auth\SocialAuthController::class, 'handleGoogleCallback']);
    });
    
    // Password Reset Routes
    Route::get('/forgot-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');
    
    // Email Verification Routes
    Route::get('/email/verify', [App\Http\Controllers\Auth\VerificationController::class, 'show'])
        ->name('verification.notice');
        
    Route::get('/email/verify/{id}/{hash}', [App\Http\Controllers\Auth\VerificationController::class, 'verify'])
        ->middleware('signed')
        ->name('verification.verify');
        
    Route::post('/email/verification-notification', [App\Http\Controllers\Auth\VerificationController::class, 'resend'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
});

// Protected Routes (Require verified email)
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard route - redirects to admin dashboard if user has permission
    Route::get('/dashboard', function() {
        if (auth()->user()->hasPermission('view_dashboard')) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('home')->with('error', 'You do not have permission to access the dashboard.');
    })->name('dashboard');
    
    // User profile routes
    Route::get('/profile', [App\Http\Controllers\UserController::class, 'profile'])->name('profile');
    Route::put('/profile', [App\Http\Controllers\UserController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [App\Http\Controllers\UserController::class, 'updatePassword'])->name('profile.update.password');
    Route::delete('/profile', [App\Http\Controllers\UserController::class, 'destroy'])->name('profile.destroy');
    
    // Address management routes
    Route::resource('addresses', App\Http\Controllers\AddressController::class)->except(['show']);
    Route::post('/addresses/{address}/default', [App\Http\Controllers\AddressController::class, 'setDefault'])->name('addresses.default');
    
    // Order history routes
    Route::get('/orders', [App\Http\Controllers\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [App\Http\Controllers\OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{orderId}/bundles/{itemId}', [App\Http\Controllers\OrderController::class, 'bundleDetails'])->name('orders.bundle-details');
    Route::get('/orders/{id}/confirmation', [App\Http\Controllers\OrderController::class, 'confirmation'])->name('orders.confirmation');
    Route::get('/orders/{id}/success', [App\Http\Controllers\CheckoutController::class, 'success'])->name('checkout.success');
    
    // Checkout routes (only verified users can checkout)
    Route::get('/checkout', [App\Http\Controllers\CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/process', [App\Http\Controllers\CheckoutController::class, 'processCheckout'])->name('checkout.process');
    Route::post('/checkout/apply-coupon', [App\Http\Controllers\CheckoutController::class, 'applyCoupon'])->name('checkout.apply-coupon');
    Route::post('/checkout/remove-coupon', [App\Http\Controllers\CheckoutController::class, 'removeCoupon'])->name('checkout.remove-coupon');
    Route::post('/checkout/update-shipping', [App\Http\Controllers\CheckoutController::class, 'updateShippingMethod'])->name('checkout.update-shipping');
    Route::post('/checkout/update-payment', [App\Http\Controllers\CheckoutController::class, 'updatePaymentMethod'])->name('checkout.update-payment');
    Route::post('/checkout/select-address', [App\Http\Controllers\CheckoutController::class, 'selectAddress'])->name('checkout.select-address');
    Route::get('/checkout/confirmation/{id}', [App\Http\Controllers\CheckoutController::class, 'confirmation'])->name('checkout.confirmation');
    Route::post('/checkout/stripe/create-payment-intent', [App\Http\Controllers\CheckoutController::class, 'createStripePaymentIntent'])
        ->middleware(['web'])
        ->name('checkout.stripe.create-payment-intent');
    Route::get('/checkout/success/{id}', [App\Http\Controllers\CheckoutController::class, 'success'])->name('checkout.success.alternate');
});


// SEO Routes
Route::get('robots.txt', [App\Http\Controllers\RobotsTxtController::class, 'index']);

// Legal Pages
Route::get('/terms', [LegalPageController::class, 'terms'])->name('terms');
Route::get('/privacy', [LegalPageController::class, 'privacy'])->name('privacy');
Route::get('/shipping', [LegalPageController::class, 'shipping'])->name('shipping');
Route::get('/refunds', [LegalPageController::class, 'refunds'])->name('refunds');

// Stripe Webhook Route - No CSRF protection, unprotected
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook'])
    ->middleware('throttle:60,1')
    ->name('stripe.webhook');

// Admin Routes
Route::middleware(['auth', 'verified', 'permission:access_admin_panel', \App\Http\Middleware\ForceAdminLayout::class])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/data', [AdminDashboardController::class, 'dashboardData'])->name('dashboard.data');
    
    // AJAX route for refreshing dashboard data
    Route::get('/dashboard/refresh', [App\Http\Controllers\UserController::class, 'refreshDashboard'])
        ->middleware('permission:view_dashboard')
        ->name('dashboard.refresh');
    
    // Activity Log - requires view_activity_logs permission
    Route::middleware('permission:view_activity_logs')->group(function() {
        Route::get('/activities', [ActivityController::class, 'index'])->name('activities.index');
        Route::get('/activities/{activity}', [ActivityController::class, 'show'])->name('activities.show');
    });
    
    // Reports & Analytics - requires view_reports permission
    Route::middleware('permission:view_reports')->group(function() {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
        Route::get('/reports/products', [ReportController::class, 'products'])->name('reports.products');
        Route::get('/reports/customers', [ReportController::class, 'customers'])->name('reports.customers');
    });
    
    // Product Batch Operations - requires manage_products permission
    Route::middleware('permission:manage_products')->prefix('products/batch')->name('products.batch.')->group(function() {
        Route::post('/update-prices', [App\Http\Controllers\Admin\ProductBatchController::class, 'updatePrices'])->name('update-prices');
        Route::post('/update-stock', [App\Http\Controllers\Admin\ProductBatchController::class, 'updateStock'])->name('update-stock');
        Route::post('/update-visibility', [App\Http\Controllers\Admin\ProductBatchController::class, 'updateVisibility'])->name('update-visibility');
        Route::post('/update-featured', [App\Http\Controllers\Admin\ProductBatchController::class, 'updateFeatured'])->name('update-featured');
    });
    
    // Marketing & Coupons Routes
    Route::middleware('permission:manage_marketing')->group(function() {
        Route::get('/coupons', [App\Http\Controllers\Admin\CouponController::class, 'index'])->name('coupons.index');
        Route::get('/coupons/create', [App\Http\Controllers\Admin\CouponController::class, 'create'])->name('coupons.create');
        Route::post('/coupons', [App\Http\Controllers\Admin\CouponController::class, 'store'])->name('coupons.store');
        Route::get('/coupons/{coupon}', [App\Http\Controllers\Admin\CouponController::class, 'show'])->name('coupons.show');
        Route::get('/coupons/{coupon}/edit', [App\Http\Controllers\Admin\CouponController::class, 'edit'])->name('coupons.edit');
        Route::put('/coupons/{coupon}', [App\Http\Controllers\Admin\CouponController::class, 'update'])->name('coupons.update');
        Route::delete('/coupons/{coupon}', [App\Http\Controllers\Admin\CouponController::class, 'destroy'])->name('coupons.destroy');
        Route::post('/coupons/{coupon}/duplicate', [App\Http\Controllers\Admin\CouponController::class, 'duplicate'])->name('coupons.duplicate');
        Route::post('/coupons/{coupon}/toggle-status', [App\Http\Controllers\Admin\CouponController::class, 'toggleStatus'])->name('coupons.toggle-status');
        Route::get('/marketing/analytics', [App\Http\Controllers\Admin\CouponController::class, 'analytics'])->name('marketing.analytics');
    });
    
    // Settings Management - requires manage_settings permission
    Route::middleware('permission:manage_settings')->group(function() {
        // Individual Settings Pages
        Route::get('/settings/general', [App\Http\Controllers\Admin\SettingsController::class, 'general'])->name('settings.general');
        Route::put('/settings/general', [App\Http\Controllers\Admin\SettingsController::class, 'updateGeneral'])->name('settings.general.update');
        
        Route::get('/settings/currency', [App\Http\Controllers\Admin\SettingsController::class, 'currency'])->name('settings.currency');
        Route::put('/settings/currency', [App\Http\Controllers\Admin\SettingsController::class, 'updateCurrency'])->name('settings.currency.update');
        
        Route::get('/settings/language', [App\Http\Controllers\Admin\SettingsController::class, 'language'])->name('settings.language');
        Route::put('/settings/language', [App\Http\Controllers\Admin\SettingsController::class, 'updateLanguage'])->name('settings.language.update');
        
        // Store Hours Management
        Route::get('/store-hours', [App\Http\Controllers\Admin\StoreHoursController::class, 'index'])->name('store-hours.index');
        Route::get('/store-hours/edit', [App\Http\Controllers\Admin\StoreHoursController::class, 'edit'])->name('store-hours.edit');
        Route::put('/store-hours', [App\Http\Controllers\Admin\StoreHoursController::class, 'update'])->name('store-hours.update');
        
        // Footer Management
        Route::get('/footer', [App\Http\Controllers\Admin\FooterController::class, 'index'])->name('footer.index');
        Route::post('/footer/sections', [App\Http\Controllers\Admin\FooterController::class, 'storeSection'])->name('footer.sections.store');
        Route::put('/footer/sections/{section}', [App\Http\Controllers\Admin\FooterController::class, 'updateSection'])->name('footer.sections.update');
        Route::delete('/footer/sections/{section}', [App\Http\Controllers\Admin\FooterController::class, 'destroySection'])->name('footer.sections.destroy');
        Route::post('/footer/links', [App\Http\Controllers\Admin\FooterController::class, 'storeLink'])->name('footer.links.store');
        Route::put('/footer/links/{link}', [App\Http\Controllers\Admin\FooterController::class, 'updateLink'])->name('footer.links.update');
        Route::delete('/footer/links/{link}', [App\Http\Controllers\Admin\FooterController::class, 'destroyLink'])->name('footer.links.destroy');
        Route::put('/footer/settings', [App\Http\Controllers\Admin\FooterController::class, 'updateSettings'])->name('footer.settings.update');
        
        // Payment Settings
        Route::get('/settings/payment', [App\Http\Controllers\Admin\SettingsController::class, 'payment'])->name('settings.payment');
        Route::put('/settings/payment', [App\Http\Controllers\Admin\SettingsController::class, 'updatePayment'])->name('settings.payment.update');
        
        // About Page Management
        Route::get('/about', [App\Http\Controllers\Admin\AboutPageController::class, 'edit'])->name('about.edit');
        Route::put('/about', [App\Http\Controllers\Admin\AboutPageController::class, 'update'])->name('about.update');
        Route::delete('/about/values/{id}', [App\Http\Controllers\Admin\AboutPageController::class, 'deleteValue'])->name('about.values.delete');
        Route::delete('/about/members/{id}', [App\Http\Controllers\Admin\AboutPageController::class, 'deleteMember'])->name('about.members.delete');
        Route::delete('/about/certifications/{number}', [App\Http\Controllers\Admin\AboutPageController::class, 'deleteCertification'])->name('about.certifications.delete');
        Route::post('/about/order', [App\Http\Controllers\Admin\AboutPageController::class, 'updateOrder'])->name('about.update-order');
        
        // Settings Translations Management
        Route::get('/settings/translations', [App\Http\Controllers\Admin\SettingTranslationController::class, 'index'])->name('settings.translations.index');
        Route::get('/settings/{setting}/translations', [App\Http\Controllers\Admin\SettingTranslationController::class, 'edit'])->name('settings.translations.edit');
        Route::put('/settings/{setting}/translations', [App\Http\Controllers\Admin\SettingTranslationController::class, 'update'])->name('settings.translations.update');
        Route::delete('/settings/{setting}/translations/{locale}', [App\Http\Controllers\Admin\SettingTranslationController::class, 'destroy'])->name('settings.translations.destroy');
        
        // Shipping Settings
        Route::get('/shipping', [App\Http\Controllers\Admin\ShippingController::class, 'index'])->name('shipping.index');
        Route::post('/shipping/update-general', [App\Http\Controllers\Admin\ShippingController::class, 'updateGeneral'])->name('shipping.update-general');
        Route::post('/shipping/update-methods', [App\Http\Controllers\Admin\ShippingController::class, 'updateMethods'])->name('shipping.update-methods');
        Route::post('/shipping/update-country-fees', [App\Http\Controllers\Admin\ShippingController::class, 'updateCountryFees'])->name('shipping.update-country-fees');
        
        // Contact Messages Management
        Route::middleware('permission:manage_contact_messages')->group(function() {
            Route::get('/contact-messages', [App\Http\Controllers\Admin\ContactMessageController::class, 'index'])->name('contact-messages.index');
            Route::get('/contact-messages/{message}', [App\Http\Controllers\Admin\ContactMessageController::class, 'show'])->name('contact-messages.show');
            Route::put('/contact-messages/{message}/status', [App\Http\Controllers\Admin\ContactMessageController::class, 'updateStatus'])->name('contact-messages.update-status');
            Route::post('/contact-messages/{message}/reply', [App\Http\Controllers\Admin\ContactMessageController::class, 'reply'])->name('contact-messages.reply');
            Route::delete('/contact-messages/{message}', [App\Http\Controllers\Admin\ContactMessageController::class, 'destroy'])->name('contact-messages.destroy');
        });
        
       
        // SEO Management
        Route::get('/seo', [App\Http\Controllers\Admin\SeoController::class, 'index'])->name('seo.index');
        
        // SEO Docs component
        Route::get('/seo/docs/component', [SeoComponentController::class, 'docs'])
            ->middleware('permission:manage_settings')
            ->name('seo.docs.component');
        
        Route::put('/seo/settings', [App\Http\Controllers\Admin\SeoController::class, 'updateSettings'])->name('seo.update-settings');
        
        Route::get('/seo/products', [App\Http\Controllers\Admin\SeoController::class, 'products'])->name('seo.products');
        Route::get('/seo/categories', [App\Http\Controllers\Admin\SeoController::class, 'categories'])->name('seo.categories');
        Route::get('/seo/products/{id}/edit', [App\Http\Controllers\Admin\SeoController::class, 'editProduct'])->name('seo.edit-product');
        Route::put('/seo/products/{id}', [App\Http\Controllers\Admin\SeoController::class, 'updateProduct'])->name('seo.update-product');
        Route::get('/seo/categories/{id}/edit', [App\Http\Controllers\Admin\SeoController::class, 'editCategory'])->name('seo.edit-category');
        Route::put('/seo/categories/{id}', [App\Http\Controllers\Admin\SeoController::class, 'updateCategory'])->name('seo.update-category');
        Route::get('/seo/generate-sitemap', [App\Http\Controllers\Admin\SeoController::class, 'generateSitemap'])->name('seo.generate-sitemap');
        
        // Homepage SEO Management
        Route::get('/seo/homepage', [App\Http\Controllers\Admin\SeoController::class, 'editHomepage'])->name('seo.edit-homepage');
        Route::put('/seo/homepage', [App\Http\Controllers\Admin\SeoController::class, 'updateHomepage'])->name('seo.update-homepage');
        
        // Homepage Content Management
        Route::get('/homepage-content', [App\Http\Controllers\Admin\HomepageController::class, 'editContent'])->name('homepage-content');
        Route::put('/homepage-content', [App\Http\Controllers\Admin\HomepageController::class, 'updateContent'])->name('homepage-content.update');

        // Testimonials Management
        Route::resource('testimonials', App\Http\Controllers\Admin\TestimonialsController::class);
        Route::patch('/testimonials/{testimonial}/toggle-approval', [App\Http\Controllers\Admin\TestimonialsController::class, 'toggleApproval'])->name('testimonials.toggle-approval');
        Route::patch('/testimonials/{testimonial}/toggle-featured', [App\Http\Controllers\Admin\TestimonialsController::class, 'toggleFeatured'])->name('testimonials.toggle-featured');

        // Clear Cache Utility
        Route::post('/clear-cache', [App\Http\Controllers\Admin\SettingsController::class, 'clearCache'])->name('clear-cache');

        // Health Checker
        Route::get('/health-checker', [App\Http\Controllers\Admin\SeoController::class, 'healthChecker'])->name('health-checker');

        // Robots.txt Management
        Route::get('/robots-txt', [App\Http\Controllers\Admin\SeoController::class, 'robotsTxt'])->name('robots-txt');
        Route::post('/robots-txt', [App\Http\Controllers\Admin\SeoController::class, 'updateRobotsTxt'])->name('update-robots-txt');
        Route::post('/robots-txt/rules', [App\Http\Controllers\Admin\SeoController::class, 'manageRobotRules'])->name('manage-robot-rules');

        // Redirects Management
        Route::get('/redirects', [App\Http\Controllers\Admin\SeoController::class, 'redirects'])->name('redirects');
        Route::post('/redirects', [App\Http\Controllers\Admin\SeoController::class, 'storeRedirect'])->name('store-redirect');
        Route::put('/redirects/{id}', [App\Http\Controllers\Admin\SeoController::class, 'updateRedirect'])->name('update-redirect');
        Route::delete('/redirects/{id}', [App\Http\Controllers\Admin\SeoController::class, 'destroyRedirect'])->name('destroy-redirect');

        // Structured Data Management
        Route::get('/structured-data', [App\Http\Controllers\Admin\SeoController::class, 'structuredData'])->name('structured-data');
        Route::post('/structured-data/settings', [App\Http\Controllers\Admin\SeoController::class, 'updateStructuredDataSettings'])->name('update-structured-data-settings');
        Route::get('/structured-data/product-sample', [App\Http\Controllers\Admin\SeoController::class, 'getProductSample'])->name('structured-data-product-sample');
        Route::post('/structured-data', [App\Http\Controllers\Admin\SeoController::class, 'storeStructuredData'])->name('store-structured-data');
        Route::put('/structured-data/{id}', [App\Http\Controllers\Admin\SeoController::class, 'updateStructuredData'])->name('update-structured-data');
        Route::delete('/structured-data/{id}', [App\Http\Controllers\Admin\SeoController::class, 'destroyStructuredData'])->name('destroy-structured-data');
        Route::get('/structured-data/{id}/edit', [App\Http\Controllers\Admin\SeoController::class, 'editStructuredData'])->name('edit-structured-data');

        // Sitemap Viewer
        Route::get('/sitemap-viewer', [App\Http\Controllers\Admin\SeoController::class, 'sitemapViewer'])->name('sitemap-viewer');
        
        // SEO Suggestion Tool
        Route::get('/seo/suggestion-tool', [App\Http\Controllers\Admin\SeoController::class, 'seoSuggestionTool'])
            ->name('admin.seo.suggestion-tool');
        Route::post('/seo/generate-suggestions', [App\Http\Controllers\Admin\SeoController::class, 'generateSeoSuggestions'])->name('seo.generate-suggestions');

        // SEO Settings - requires manage_seo permission
        Route::middleware('permission:manage_seo')->group(function() {
            // Routes already defined above - removed to prevent duplication
        });
    });
    
    // Products Management - requires manage_products permission
    Route::middleware('permission:manage_products')->group(function() {
        Route::resource('products', App\Http\Controllers\Admin\ProductController::class);
        Route::delete('/products/{product}/images/{image}', [App\Http\Controllers\Admin\ProductController::class, 'destroyImage'])->name('products.images.destroy');
        Route::delete('/products/{product}/featured-image', [App\Http\Controllers\Admin\ProductController::class, 'destroyFeaturedImage'])->name('admin.products.featured-image.destroy');
        Route::post('/products/{id}/remove-image', [App\Http\Controllers\Admin\ProductController::class, 'removeImage'])->name('products.remove-image');
        Route::get('/products/{id}/remove-gallery-image', [App\Http\Controllers\Admin\ProductController::class, 'removeImage'])->name('products.remove-gallery-image');
        
        // Categories Management (part of product management)
        Route::resource('categories', CategoryController::class);
    });
    
    // Orders Management - requires manage_orders permission
    Route::middleware('permission:manage_orders')->group(function() {
        Route::resource('orders', App\Http\Controllers\Admin\OrderController::class);
        Route::get('/orders/{order}/test-email', [App\Http\Controllers\Admin\OrderController::class, 'testEmail'])->name('orders.test-email');
        Route::match(['get', 'post'], '/orders/{order}/generate-tracking', [App\Http\Controllers\Admin\OrderController::class, 'generateTracking'])->name('orders.generate-tracking');
        Route::get('/orders/{order}/shipping-label', [App\Http\Controllers\Admin\OrderController::class, 'shippingLabel'])->name('orders.shipping-label');
    });
    
    // Users Management - requires manage_users permission
    Route::middleware('permission:manage_users')->group(function() {
        Route::resource('users', AdminUserController::class);
        
        // Roles & Permissions Management
        Route::get('/roles', [App\Http\Controllers\Admin\RoleController::class, 'index'])->name('roles.index');
        Route::get('/roles/create', [App\Http\Controllers\Admin\RoleController::class, 'create'])->name('roles.create');
        Route::post('/roles', [App\Http\Controllers\Admin\RoleController::class, 'store'])->name('roles.store');
        Route::get('/roles/{user}/edit', [App\Http\Controllers\Admin\RoleController::class, 'edit'])->name('roles.edit');
        Route::put('/roles/{user}', [App\Http\Controllers\Admin\RoleController::class, 'update'])->name('roles.update');
        Route::delete('/roles/{user}', [App\Http\Controllers\Admin\RoleController::class, 'destroy'])->name('roles.destroy');
    });
    
    // Offers Management Routes
    Route::resource('offers', OfferController::class);
    Route::patch('offers/{offer}/toggle-status', [OfferController::class, 'toggleStatus'])->name('offers.toggle-status');
    Route::post('offers/{offer}/duplicate', [OfferController::class, 'duplicate'])->name('offers.duplicate');
    Route::post('/offers/update-order', [OfferController::class, 'updateOrder'])->name('offers.update-order');
    Route::post('/offers/{offer}/toggle-active', [OfferController::class, 'toggleActive'])->name('offers.toggle-active');

    // Header Navigation Management
    Route::middleware(['permission:manage_settings'])->group(function () {
        Route::resource('header-navigation', HeaderNavigationController::class)->except(['create', 'edit']);
        Route::post('header-navigation/update-order', [HeaderNavigationController::class, 'updateOrder'])->name('header-navigation.update-order');
        Route::get('header-navigation/{headerNavigation}/edit', [HeaderNavigationController::class, 'edit'])->name('header-navigation.edit');
        Route::post('header-navigation/settings', [HeaderNavigationController::class, 'updateSettings'])->name('header-navigation.settings');
    });

    // Print routes
    Route::get('/print/order/{id}', [App\Http\Controllers\Admin\PrintController::class, 'orderPDF'])
        ->middleware('permission:view_orders')
        ->name('print.order');
    Route::get('/print/product/{id}', [App\Http\Controllers\Admin\PrintController::class, 'productPDF'])
        ->middleware('permission:view_products')
        ->name('print.product');
    Route::get('/print/offer/{id}', [App\Http\Controllers\Admin\PrintController::class, 'offerPDF'])
        ->middleware('permission:view_products')
        ->name('print.offer');
    Route::get('/print/customer/{id}', [App\Http\Controllers\Admin\PrintController::class, 'customerPDF'])
        ->middleware('permission:view_users')
        ->name('print.customer');
    Route::get('/print/inventory', [App\Http\Controllers\Admin\PrintController::class, 'inventoryPDF'])
        ->middleware('permission:view_reports')
        ->name('print.inventory');
    Route::get('/print/sales-report', [App\Http\Controllers\Admin\PrintController::class, 'salesReportPDF'])
        ->middleware('permission:view_reports')
        ->name('print.sales-report');

    // About Page
    Route::get('about/edit', [\App\Http\Controllers\Admin\AboutPageController::class, 'edit'])->name('about.edit');
    Route::post('about/update', [\App\Http\Controllers\Admin\AboutPageController::class, 'update'])->name('about.update');
    Route::post('about/fix-update', [\App\Http\Controllers\Admin\AboutPageFixController::class, 'update'])->name('about.fix-update');
    
    // Simple About Page Editor
    Route::get('about/simple', [\App\Http\Controllers\Admin\AboutPageSimpleController::class, 'edit'])->name('about.simple');
    Route::post('about/simple/update', [\App\Http\Controllers\Admin\AboutPageSimpleController::class, 'update'])->name('about.simple.update');
});

// SEO Suggestion Tool routes
Route::post('/admin/seo/generate-suggestions', [App\Http\Controllers\Admin\SeoController::class, 'generateSuggestions'])
    ->name('admin.seo.generate-suggestions');
    
Route::post('/admin/seo/apply-suggestions', [App\Http\Controllers\Admin\SeoController::class, 'applySuggestions'])
    ->name('admin.seo.apply-suggestions');
    
Route::post('/admin/seo/apply-bulk-suggestions', [App\Http\Controllers\Admin\SeoController::class, 'applyBulkSuggestions'])
    ->name('admin.seo.apply-bulk-suggestions');

// SEO Suggestion Tool view route
Route::get('/admin/seo/suggestion-tool', [App\Http\Controllers\Admin\SeoController::class, 'seoSuggestionTool'])
    ->name('admin.seo.suggestion-tool');

// Email Management Routes
Route::prefix('admin/emails')->name('admin.emails.')->middleware(['auth', 'verified', 'permission:manage_settings'])->group(function () {
    // Email Templates
    Route::get('/templates', [App\Http\Controllers\Admin\EmailTemplateController::class, 'index'])->name('templates.index');
    Route::get('/templates/create', [App\Http\Controllers\Admin\EmailTemplateController::class, 'create'])->name('templates.create');
    Route::post('/templates', [App\Http\Controllers\Admin\EmailTemplateController::class, 'store'])->name('templates.store');
    Route::get('/templates/{template}', [App\Http\Controllers\Admin\EmailTemplateController::class, 'show'])->name('templates.show');
    Route::get('/templates/{template}/edit', [App\Http\Controllers\Admin\EmailTemplateController::class, 'edit'])->name('templates.edit');
    Route::put('/templates/{template}', [App\Http\Controllers\Admin\EmailTemplateController::class, 'update'])->name('templates.update');
    Route::delete('/templates/{template}', [App\Http\Controllers\Admin\EmailTemplateController::class, 'destroy'])->name('templates.destroy');
    Route::get('/templates/{template}/preview', [App\Http\Controllers\Admin\EmailTemplateController::class, 'preview'])->name('templates.preview');
    Route::post('/templates/{template}/clone', [App\Http\Controllers\Admin\EmailTemplateController::class, 'clone'])->name('templates.clone');
    
    // Email Logs
    Route::get('/logs', [App\Http\Controllers\Admin\EmailTemplateController::class, 'logs'])->name('logs.index');
    Route::get('/logs/{log}', [App\Http\Controllers\Admin\EmailTemplateController::class, 'showLog'])->name('logs.show');
    
    // Test Email
    Route::get('/test', [App\Http\Controllers\Admin\EmailTemplateController::class, 'showTestEmailForm'])->name('test');
    Route::post('/test', [App\Http\Controllers\Admin\EmailTemplateController::class, 'sendTestEmail'])->name('test.send');
});

// Admin Reviews Routes
Route::get('/admin/reviews', [AdminReviewController::class, 'index'])->name('admin.reviews.index');
Route::delete('/admin/reviews/{review}', [AdminReviewController::class, 'destroy'])->name('admin.reviews.destroy');
Route::patch('/admin/reviews/{review}/toggle-approval', [AdminReviewController::class, 'toggleApproval'])->name('admin.reviews.toggle-approval');

// Theme Management
Route::prefix('admin/theme')->name('admin.theme.')->middleware(['auth', 'verified', 'permission:manage_settings'])->group(function () {
    Route::get('/', [ThemeController::class, 'index'])->name('index');
    Route::post('/', [ThemeController::class, 'update'])->name('update');
    Route::get('/create', [ThemeController::class, 'create'])->name('create');
    Route::post('/store', [ThemeController::class, 'store'])->name('store');
    Route::get('/{theme}/edit', [ThemeController::class, 'edit'])->name('edit');
    Route::put('/{theme}', [ThemeController::class, 'updateTheme'])->name('update-theme');
    Route::delete('/{theme}', [ThemeController::class, 'destroy'])->name('destroy');
    Route::post('/{theme}/duplicate', [ThemeController::class, 'duplicate'])->name('duplicate');
    Route::get('/showcase', [ThemeController::class, 'showcase'])->name('showcase');
    Route::get('/direct-apply/{id}', [ThemeController::class, 'directApply'])->name('direct-apply');
    Route::get('/create-starlight', [ThemeController::class, 'createStarlightTheme'])->name('create-starlight');
});

// Fix for admin.settings.index route
Route::get('/admin/settings', function() {
    return redirect()->route('admin.settings.general');
})->name('admin.settings.index');
