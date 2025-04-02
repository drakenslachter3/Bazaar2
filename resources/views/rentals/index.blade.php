@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ __('My Rentals') }}</h1>
    
    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link {{ !request('status') || request('status') == 'all' ? 'active' : '' }}" href="{{ route('rentals.index') }}">{{ __('All') }}</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request('status') == 'pending' ? 'active' : '' }}" href="{{ route('rentals.index', ['status' => 'pending']) }}">{{ __('Pending') }}</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request('status') == 'active' ? 'active' : '' }}" href="{{ route('rentals.index', ['status' => 'active']) }}">{{ __('Active') }}</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request('status') == 'completed' ? 'active' : '' }}" href="{{ route('rentals.index', ['status' => 'completed']) }}">{{ __('Completed') }}</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request('status') == 'cancelled' ? 'active' : '' }}" href="{{ route('rentals.index', ['status' => 'cancelled']) }}">{{ __('Cancelled') }}</a>
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
                                    <span class="badge bg-warning text-dark">{{ __('Pending') }}</span>
                                    @break
                                @case('active')
                                    <span class="badge bg-success">{{ __('Active') }}</span>
                                    @break
                                @case('completed')
                                    <span class="badge bg-info">{{ __('Completed') }}</span>
                                    @break
                                @case('cancelled')
                                    <span class="badge bg-danger">{{ __('Cancelled') }}</span>
                                    @break
                            @endswitch
                        </span>
                        <span class="text-muted">{{ __('Rental') }} #{{ $rental->id }}</span>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">{{ $rental->advertisement->title }}</h5>
                        <p class="card-text">{{ Str::limit($rental->advertisement->description, 100) }}</p>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>{{ __('Rental Period') }}:</strong><br>
                                {{ $rental->start_date->format('d M Y') }} - {{ $rental->end_date->format('d M Y') }}
                            </div>
                            <div class="col-md-6">
                                <strong>{{ __('Total Days') }}:</strong><br>
                                {{ $rental->start_date->diffInDays($rental->end_date) + 1 }}
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <strong>{{ __('Price per Day') }}:</strong> €{{ number_format($rental->advertisement->price, 2) }}<br>
                            <strong>{{ __('Total Price') }}:</strong> €{{ number_format(($rental->advertisement->price * ($rental->start_date->diffInDays($rental->end_date) + 1)), 2) }}
                        </div>
                        
                        <div class="d-flex">
                            <a href="{{ route('rentals.show', $rental) }}" class="btn btn-primary me-2">{{ __('View Details') }}</a>
                            
                            @if($rental->status == 'pending')
                                <form action="{{ route('rentals.destroy', $rental) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" onclick="return confirm('{{ __('Are you sure you want to cancel this rental?') }}')">
                                        {{ __('Cancel') }}
                                    </button>
                                </form>
                            @endif
                            
                            @if($rental->status == 'completed')
                                <a href="{{ route('reviews.product.create', $rental->advertisement) }}" class="btn btn-outline-success">
                                    {{ __('Leave Review') }}
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="card-footer text-muted">
                        <small>{{ __('Requested on') }}: {{ $rental->created_at->format('d M Y H:i') }}</small>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    {{ __('You have no rental requests yet.') }}
                </div>
                <p>{{ __('Browse our rental items and make a rental request to see it here.') }}</p>
                <a href="{{ route('advertisements.index', ['type' => 'rent']) }}" class="btn btn-primary">
                    {{ __('Browse Rental Items') }}
                </a>
            </div>
        @endforelse
    </div>
    
    <!-- Pagination -->
    <div class="mt-4">
        {{ $rentals->appends(request()->query())->links() }}
    </div>
</div>
@endsection