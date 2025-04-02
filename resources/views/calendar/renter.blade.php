@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ __('My Rental Calendar') }}</h1>
    <p class="lead">{{ __('View the schedule of your rentals.') }}</p>
    
    <div class="card">
        <div class="card-body">
            <div id="rental-calendar" data-events='{{ json_encode($events) }}'></div>
        </div>
    </div>
    
    <div class="mt-4">
        <a href="{{ route('rentals.index') }}" class="btn btn-outline-primary">
            <i class="fas fa-list"></i> {{ __('View Rentals List') }}
        </a>
    </div>
</div>
@endsection

@push('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css' rel='stylesheet' />
@endpush

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js'></script>
<script>
    // Vertaling-object voor de kalender
    const translations = {
        today: '{{ __('today') }}',
        month: '{{ __('month') }}',
        week: '{{ __('week') }}',
        list: '{{ __('list') }}'
    };
</script>
<script src="{{ asset('js/rental-calendar.js') }}"></script>
@endpush

@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ __('Rental Management Calendar') }}</h1>
    <p class="lead">{{ __('View the schedule of your rented items and expiring advertisements.') }}</p>
    
    <div class="card">
        <div class="card-body">
            <div id="rental-calendar" data-events='{{ json_encode($events) }}'></div>
        </div>
    </div>
    
    <div class="mt-4 d-flex gap-3">
        <a href="{{ route('advertisements.index') }}" class="btn btn-outline-primary">
            <i class="fas fa-list"></i> {{ __('View All Advertisements') }}
        </a>
        <a href="{{ route('advertisements.upcoming') }}" class="btn btn-outline-warning">
            <i class="fas fa-exclamation-triangle"></i> {{ __('View Expiring Advertisements') }}
        </a>
    </div>
</div>
@endsection

@push('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css' rel='stylesheet' />
@endpush

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js'></script>
<script>
    // Vertaling-object voor de kalender
    const translations = {
        today: '{{ __('today') }}',
        month: '{{ __('month') }}',
        week: '{{ __('week') }}',
        list: '{{ __('list') }}'
    };
</script>
<script src="{{ asset('js/rental-calendar.js') }}"></script>
@endpush