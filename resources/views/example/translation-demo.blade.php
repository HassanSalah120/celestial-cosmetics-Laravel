@extends('layouts.app')

@section('content')
<div class="container my-5">
    <h1 class="mb-4">Translation Demo</h1>
    
    <div class="card mb-4">
        <div class="card-header">
            <h5>Basic Translations</h5>
        </div>
        <div class="card-body">
            <p>Using the @t directive: @t('messages.welcome')</p>
            <p>Using the helper function: {{ t('messages.welcome') }}</p>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">
            <h5>JSON Translations</h5>
        </div>
        <div class="card-body">
            <p>Using the @tj directive: @tj('Welcome to our store')</p>
            <p>Using the helper function: {{ tjs('Shop now and discover our beautiful products') }}</p>
            <p>With parameters: {{ tjs('Hello, :name', ['name' => 'User']) }}</p>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">
            <h5>RTL Support</h5>
        </div>
        <div class="card-body">
            <p>Current direction: {{ locale_direction() }}</p>
            <p>Is RTL? {{ is_rtl() ? 'Yes' : 'No' }}</p>
            
            @rtl
                <div class="alert alert-info">
                    This content is only visible in RTL mode.
                </div>
            @else
                <div class="alert alert-primary">
                    This content is only visible in LTR mode.
                </div>
            @endrtl
            
            <div class="mt-3">
                <a href="{{ route('locale.switch', ['locale' => 'en']) }}" class="btn btn-outline-primary me-2">Switch to English</a>
                <a href="{{ route('locale.switch', ['locale' => 'ar']) }}" class="btn btn-outline-primary">Switch to Arabic</a>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h5>Dynamic Content with Translations</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <h3>{{ tjs('Discover our latest arrivals') }}</h3>
                            <p>{{ tjs('Our cosmetics are cruelty-free and eco-friendly') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <h3>{{ tjs('Join our newsletter') }}</h3>
                            <p>{{ tjs('Stay updated with our latest products and offers') }}</p>
                            <form>
                                <div class="mb-3">
                                    <input type="email" class="form-control" placeholder="{{ tjs('Enter your email address') }}">
                                </div>
                                <button type="submit" class="btn btn-primary">{{ tjs('Subscribe') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 