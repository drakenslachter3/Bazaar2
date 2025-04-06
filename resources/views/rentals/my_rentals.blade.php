@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ __('Mijn Huurverzoeken') }}</h1>
    
    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link {{ !request('status') || request('status') == 'all' ? 'active' : '' }}" href="{{ route('my.rentals') }}">{{ __('Alle') }}</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request('status') == 'pending' ? 'active' : '' }}" href="{{ route('my.rentals', ['status' => 'pending']) }}">{{ __('In behandeling') }}</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request('status') == 'active' ? 'active' : '' }}" href="{{ route('my.rentals', ['status' => 'active']) }}">{{ __('Actief') }}</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request('status') == 'completed' ? 'active' : '' }}" href="{{ route('my.rentals', ['status' => 'completed']) }}">{{ __('Voltooid') }}</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request('status') == 'cancelled' ? 'active' : '' }}" href="{{ route('my.rentals', ['status' => 'cancelled']) }}">{{ __('Geannuleerd') }}</a>
        </li>
    </ul>
    
    <div class="row">
        @forelse($rentals as $rental)
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>
                            @switch($rental->status)
                                @case('pending')
                                    <span class="badge bg-warning text-dark">{{ __('In behandeling') }}</span>
                                    @break
                                @case('active')
                                    <span class="badge bg-success">{{ __('Actief') }}</span>
                                    @break
                                @case('completed')
                                    <span class="badge bg-info">{{ __('Voltooid') }}</span>
                                    @break
                                @case('cancelled')
                                    <span class="badge bg-danger">{{ __('Geannuleerd') }}</span>
                                    @break
                            @endswitch
                        </span>
                        <span class="text-muted">{{ __('Nr.') }} {{ $rental->id }}</span>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">{{ $rental->advertisement->title }}</h5>
                        
                        <div class="mb-3">
                            <strong>{{ __('Verhuurperiode') }}:</strong> {{ $rental->start_date->format('d-m-Y') }} t/m {{ $rental->end_date->format('d-m-Y') }}
                            <br>
                            <strong>{{ __('Verhuurder') }}:</strong> {{ $rental->advertisement->user->name }}
                            <br>
                            <strong>{{ __('Totale dagen') }}:</strong> {{ $rental->start_date->diffInDays($rental->end_date) + 1 }}
                            <br>
                            <strong>{{ __('Totale prijs') }}:</strong> €{{ number_format(($rental->advertisement->price * ($rental->start_date->diffInDays($rental->end_date) + 1)), 2) }}
                        </div>
                        
                        <div class="d-flex">
                            <a href="{{ route('advertisements.show', $rental->advertisement) }}" class="btn btn-outline-primary me-2">
                                <i class="fas fa-eye"></i> {{ __('Bekijk Product') }}
                            </a>
                            
                            @if($rental->status == 'pending')
                                <form action="{{ route('rentals.destroy', $rental) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" onclick="return confirm('{{ __('Weet je zeker dat je dit huurverzoek wilt annuleren?') }}')">
                                        <i class="fas fa-times"></i> {{ __('Annuleren') }}
                                    </button>
                                </form>
                            @endif
                            
                            @if($rental->status == 'completed')
                                <a href="{{ route('reviews.product.create', $rental->advertisement) }}" class="btn btn-outline-warning">
                                    <i class="fas fa-star"></i> {{ __('Beoordeel') }}
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="card-footer text-muted">
                        <div class="d-flex justify-content-between">
                            <small>{{ __('Aangevraagd op') }}: {{ $rental->created_at->format('d-m-Y H:i') }}</small>
                            
                            @if($rental->status == 'active')
                                <div>
                                    @if($rental->start_date->isFuture())
                                        <span class="badge bg-primary">{{ __('Start over') }} {{ now()->diffInDays($rental->start_date) }} {{ __('dagen') }}</span>
                                    @elseif($rental->end_date->isFuture())
                                        <span class="badge bg-primary">{{ __('Nog') }} {{ now()->diffInDays($rental->end_date) }} {{ __('dagen') }}</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    @if(request('status') == 'pending')
                        {{ __('Je hebt momenteel geen huurverzoeken in behandeling.') }}
                    @elseif(request('status') == 'active')
                        {{ __('Je hebt momenteel geen actieve huurperiodes.') }}
                    @elseif(request('status') == 'completed')
                        {{ __('Je hebt nog geen voltooide huurperiodes.') }}
                    @elseif(request('status') == 'cancelled')
                        {{ __('Je hebt nog geen geannuleerde huurverzoeken.') }}
                    @else
                        {{ __('Je hebt nog geen huurverzoeken ingediend.') }}
                    @endif
                </div>
                
                <div class="text-center mt-4">
                    <a href="{{ route('advertisements.index', ['type' => 'rent']) }}" class="btn btn-primary">
                        <i class="fas fa-search"></i> {{ __('Bekijk Verhuurbare Items') }}
                    </a>
                </div>
            </div>
        @endforelse
    </div>
    
    <div class="mt-4">
        {{ $rentals->withQueryString()->links() }}
    </div>
    
    @if(count($rentals) > 0)
        <div class="mt-4">
            <div class="card">
                <div class="card-header">{{ __('Kalender Weergave') }}</div>
                <div class="card-body">
                    <a href="{{ route('calendar.renter') }}" class="btn btn-outline-primary">
                        <i class="fas fa-calendar-alt"></i> {{ __('Bekijk in Kalender') }}
                    </a>
                    <p class="text-muted mt-2">{{ __('Bekijk al je huurperiodes in een handige kalenderweergave.') }}</p>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection