@php
use App\Facades\Settings;

// Set defaults if values aren't provided
$metaTitle = $title ?? Settings::get('default_meta_title', config('app.name'));
$metaDescription = $description ?? Settings::get('default_meta_description', '');
$metaKeywords = $keywords ?? Settings::get('default_meta_keywords', '');
$ogImage = $ogImage ?? Settings::get('og_default_image', '');
$ogType = $type ?? 'website';
$canonical = $canonical ?? url()->current();

// Ensure all values are strings to prevent htmlspecialchars() errors
$metaTitle = is_array($metaTitle) ? json_encode($metaTitle) : (string)$metaTitle;
$metaDescription = is_array($metaDescription) ? json_encode($metaDescription) : (string)$metaDescription;
$metaKeywords = is_array($metaKeywords) ? implode(', ', $metaKeywords) : (string)$metaKeywords;
$ogType = is_array($ogType) ? 'website' : (string)$ogType;
$ogImage = is_array($ogImage) ? (empty($ogImage) ? '' : (string)$ogImage[0]) : (string)$ogImage;
$canonical = is_array($canonical) ? url()->current() : (string)$canonical;
@endphp

<!-- SEO Meta Tags -->
<title>{{ $metaTitle }}</title>
<meta name="description" content="{{ $metaDescription }}">
<meta name="keywords" content="{{ $metaKeywords }}">

<!-- Canonical URL -->
<link rel="canonical" href="{{ $canonical }}">

<!-- Open Graph Meta Tags -->
<meta property="og:title" content="{{ $metaTitle }}">
<meta property="og:description" content="{{ $metaDescription }}">
<meta property="og:type" content="{{ $ogType }}">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:site_name" content="{{ Settings::get('og_site_name', config('app.name')) }}">
@if($ogImage)
    <meta property="og:image" content="{{ asset('storage/' . $ogImage) }}">
@endif

<!-- Twitter Card Meta Tags -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $metaTitle }}">
<meta name="twitter:description" content="{{ $metaDescription }}">
@if(Settings::get('twitter_site'))
    <meta name="twitter:site" content="{{ Settings::get('twitter_site') }}">
@endif
@if(Settings::get('twitter_creator'))
    <meta name="twitter:creator" content="{{ Settings::get('twitter_creator') }}">
@endif
@if($ogImage)
    <meta name="twitter:image" content="{{ asset('storage/' . $ogImage) }}">
@endif

<!-- Robots Meta Tags -->
@if(Settings::get('enable_robots_txt', true))
    <meta name="robots" content="{{ $robots ?? Settings::get('default_robots_content', 'index, follow') }}">
@endif 