@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ __('Expiring Advertisements') }}</h1>
    <p class="lead">{{ __('View advertisements that will expire in the next 30 days.') }}</p>
    
    <div class="row mt-4">
        @forelse($expiringAds as $advertisement)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-warning text-dark">
                        <strong>{{ __('Expires') }}: {{ $advertisement->expiry_date->format('d M Y') }}</strong>
                        <span class="ms-2 badge {{ $advertisement->expiry_date->isPast() ? 'bg-danger' : 'bg-warning text-dark' }}">
                            {{ $advertisement->expiry_date->isPast() ? __('Expired') : __('Expires in') . ' ' . $advertisement->expiry_date->diffForHumans() }}
                        </span>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">{{ $advertisement->title }}</h5>
                        <h6 class="card-subtitle mb-2 text-muted">
                            €{{ number_format($advertisement->price, 2) }}
                            @if($advertisement->type == 'rent')
                                <span class="badge bg-info">{{ __('For Rent') }}</span>
                            @else
                                <span class="badge bg-success">{{ __('For Sale') }}</span>
                            @endif
                        </h6>
                        <p class="card-text">{{ Str::limit($advertisement->description, 100) }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('advertisements.show', $advertisement) }}" class="btn btn-sm btn-primary">{{ __('View Details') }}</a>
                            <a href="{{ route('advertisements.edit', $advertisement) }}" class="btn btn-sm btn-outline-primary">{{ __('Edit') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-success">
                    {{ __('Great! You have no advertisements expiring in the next 30 days.') }}
                </div>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $expiringAds->links() }}
    </div>
</div>
@endsection