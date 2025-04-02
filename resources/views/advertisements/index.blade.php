@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>{{ __('Advertisements') }}</h1>
        </div>
        @auth
            @if(auth()->user()->user_type !== 'regular')
            <div class="col-md-4 text-end">
                <a href="{{ route('advertisements.createad') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> {{ __('Create Advertisement') }}
                </a>
            </div>
            @endif
        @endauth
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-filter"></i> {{ __('Filter') }}
        </div>
        <div class="card-body">
            <form action="{{ route('advertisements.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="type" class="form-label">{{ __('Type') }}</label>
                    <select name="type" id="type" class="form-control">
                        <option value="">{{ __('All Types') }}</option>
                        <option value="sale" {{ request('type') == 'sale' ? 'selected' : '' }}>{{ __('For Sale') }}</option>
                        <option value="rent" {{ request('type') == 'rent' ? 'selected' : '' }}>{{ __('For Rent') }}</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="min_price" class="form-label">{{ __('Min Price') }}</label>
                    <input type="number" name="min_price" id="min_price" class="form-control" value="{{ request('min_price') }}" min="0">
                </div>
                <div class="col-md-3">
                    <label for="max_price" class="form-label">{{ __('Max Price') }}</label>
                    <input type="number" name="max_price" id="max_price" class="form-control" value="{{ request('max_price') }}" min="0">
                </div>
                <div class="col-md-3">
                    <label for="order_by" class="form-label">{{ __('Sort By') }}</label>
                    <select name="order_by" id="order_by" class="form-control">
                        <option value="created_at" {{ request('order_by') == 'created_at' ? 'selected' : '' }}>{{ __('Date') }}</option>
                        <option value="price" {{ request('order_by') == 'price' ? 'selected' : '' }}>{{ __('Price') }}</option>
                        <option value="title" {{ request('order_by') == 'title' ? 'selected' : '' }}>{{ __('Title') }}</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="order" class="form-label">{{ __('Direction') }}</label>
                    <select name="order" id="order" class="form-control">
                        <option value="desc" {{ request('order') == 'desc' ? 'selected' : '' }}>{{ __('Descending') }}</option>
                        <option value="asc" {{ request('order') == 'asc' ? 'selected' : '' }}>{{ __('Ascending') }}</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="per_page" class="form-label">{{ __('Items per page') }}</label>
                    <select name="per_page" id="per_page" class="form-control">
                        <option value="15" {{ request('per_page') == 15 ? 'selected' : '' }}>15</option>
                        <option value="30" {{ request('per_page') == 30 ? 'selected' : '' }}>30</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                    </select>
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">{{ __('Apply Filters') }}</button>
                    <a href="{{ route('advertisements.index') }}" class="btn btn-outline-secondary">{{ __('Reset') }}</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Advertisement grid -->
    <div class="row">
        @forelse($advertisements as $advertisement)
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
                            
                            @auth
                                <form action="{{ route('advertisements.favorite', $advertisement) }}" method="GET">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        @if(auth()->user()->favorites()->where('advertisement_id', $advertisement->id)->exists())
                                            <i class="fas fa-heart"></i>
                                        @else
                                            <i class="far fa-heart"></i>
                                        @endif
                                    </button>
                                </form>
                            @endauth
                        </div>
                    </div>
                    <div class="card-footer text-muted">
                        <small>{{ __('Posted') }} {{ $advertisement->created_at->diffForHumans() }}</small>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    {{ __('No advertisements found matching your criteria.') }}
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $advertisements->appends(request()->query())->links() }}
    </div>
</div>
@endsection