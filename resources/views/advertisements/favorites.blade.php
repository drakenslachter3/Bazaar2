@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ __('My Favorites') }}</h1>
    
    <div class="row mt-4">
        @forelse($favorites as $advertisement)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
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
                            
                            <form action="{{ route('advertisements.favorite', $advertisement) }}" method="GET">
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-heart"></i> {{ __('Remove') }}
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="card-footer text-muted">
                        <small>{{ __('Added to favorites') }}: {{ $advertisement->pivot->created_at->diffForHumans() }}</small>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    {{ __('You have no favorite advertisements yet.') }}
                </div>
                <p>{{ __('Browse our advertisements and click the heart icon to add them to your favorites.') }}</p>
                <a href="{{ route('advertisements.index') }}" class="btn btn-primary">
                    <i class="fas fa-search"></i> {{ __('Browse Advertisements') }}
                </a>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $favorites->links() }}
    </div>
</div>
@endsection