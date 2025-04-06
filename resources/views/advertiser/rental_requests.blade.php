@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ __('Verhuurverzoeken beheren') }}</h1>
    
    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link {{ !request('status') || request('status') == 'all' ? 'active' : '' }}" href="{{ route('advertiser.rental.requests') }}">{{ __('Alles') }}</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request('status') == 'pending' ? 'active' : '' }}" href="{{ route('advertiser.rental.requests', ['status' => 'pending']) }}">{{ __('In afwachting') }}</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request('status') == 'active' ? 'active' : '' }}" href="{{ route('advertiser.rental.requests', ['status' => 'active']) }}">{{ __('Actief') }}</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request('status') == 'completed' ? 'active' : '' }}" href="{{ route('advertiser.rental.requests', ['status' => 'completed']) }}">{{ __('Voltooid') }}</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request('status') == 'cancelled' ? 'active' : '' }}" href="{{ route('advertiser.rental.requests', ['status' => 'cancelled']) }}">{{ __('Geannuleerd') }}</a>
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
                                    <span class="badge bg-warning text-dark">{{ __('In afwachting') }}</span>
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
                        <span class="text-muted">{{ __('Aanvraag') }} #{{ $rental->id }}</span>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">{{ $rental->advertisement->title }}</h5>
                        
                        <div class="mb-3">
                            <strong>{{ __('Aangevraagd door') }}:</strong> {{ $rental->renter->name }}
                            <br>
                            <strong>{{ __('Verhuurperiode') }}:</strong> {{ $rental->start_date->format('d-m-Y') }} t/m {{ $rental->end_date->format('d-m-Y') }}
                            <br>
                            <strong>{{ __('Totale dagen') }}:</strong> {{ $rental->start_date->diffInDays($rental->end_date) + 1 }}
                            <br>
                            <strong>{{ __('Totale prijs') }}:</strong> €{{ number_format(($rental->advertisement->price * ($rental->start_date->diffInDays($rental->end_date) + 1)), 2) }}
                        </div>
                        
                        @if($rental->status == 'pending')
                            <div class="d-flex mt-3">
                                <form action="{{ route('advertiser.rental.update', $rental) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="status" value="active">
                                    <button type="submit" class="btn btn-success me-2">{{ __('Accepteren') }}</button>
                                </form>
                                
                                <form action="{{ route('advertiser.rental.update', $rental) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="status" value="cancelled">
                                    <button type="submit" class="btn btn-danger">{{ __('Afwijzen') }}</button>
                                </form>
                            </div>
                        @elseif($rental->status == 'active')
                            <form action="{{ route('advertiser.rental.update', $rental) }}" method="POST" class="mt-3">
                                @csrf
                                <input type="hidden" name="status" value="completed">
                                <button type="submit" class="btn btn-primary">{{ __('Markeren als voltooid') }}</button>
                            </form>
                        @endif
                    </div>
                    <div class="card-footer text-muted">
                        <small>{{ __('Aangevraagd op') }}: {{ $rental->created_at->format('d-m-Y H:i') }}</small>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    @if(request('status') == 'pending')
                        {{ __('Je hebt momenteel geen openstaande verhuurverzoeken.') }}
                    @elseif(request('status') == 'active')
                        {{ __('Je hebt momenteel geen actieve verhuren.') }}
                    @elseif(request('status') == 'completed')
                        {{ __('Je hebt nog geen voltooide verhuren.') }}
                    @elseif(request('status') == 'cancelled')
                        {{ __('Je hebt nog geen geannuleerde verhuurverzoeken.') }}
                    @else
                        {{ __('Je hebt nog geen verhuurverzoeken ontvangen.') }}
                    @endif
                </div>
            </div>
        @endforelse
    </div>
    
    <div class="mt-4">
        {{ $rentals->links() }}
    </div>
</div>
@endsection