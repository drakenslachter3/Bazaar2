@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-body">
                        <h1 class="card-title">{{ $advertisement->title }}</h1>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="text-primary mb-0">€{{ number_format($advertisement->price, 2) }}</h5>
                            <span class="badge {{ $advertisement->type == 'rent' ? 'bg-info' : 'bg-success' }} fs-6">
                                {{ $advertisement->type == 'rent' ? __('For Rent') : __('For Sale') }}
                            </span>
                        </div>

                        <div class="mb-4">
                            <h5>{{ __('Description') }}</h5>
                            <p>{{ $advertisement->description }}</p>
                        </div>

                        <div class="d-flex justify-content-between">
                            <div>
                                @if ($advertisement->type == 'rent')
                                    <a href="{{ route('rentals.create.from.ad', $advertisement) }}" class="btn btn-success">
                                        <i class="fas fa-calendar-check"></i> {{ __('Rent Now') }}
                                    </a>
                                @else
                                    <button class="btn btn-success">
                                        <i class="fas fa-shopping-cart"></i> {{ __('Buy Now') }}
                                    </button>
                                @endif
                            </div>

                            <div>
                                @auth
                                    <form action="{{ route('advertisements.favorite', $advertisement) }}" method="GET"
                                        class="d-inline">
                                        <button type="submit"
                                            class="btn {{ auth()->user()->favorites()->where('advertisement_id', $advertisement->id)->exists() ? 'btn-danger' : 'btn-outline-danger' }}">
                                            <i
                                                class="{{ auth()->user()->favorites()->where('advertisement_id', $advertisement->id)->exists() ? 'fas' : 'far' }} fa-heart"></i>
                                            {{ auth()->user()->favorites()->where('advertisement_id', $advertisement->id)->exists() ? __('Remove from Favorites') : __('Add to Favorites') }}
                                        </button>
                                    </form>
                                @endauth

                                @if (auth()->id() == $advertisement->user_id)
                                    <a href="{{ route('advertisements.edit', $advertisement) }}" class="btn btn-primary">
                                        <i class="fas fa-edit"></i> {{ __('Edit') }}
                                    </a>
                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                        data-bs-target="#deleteModal">
                                        <i class="fas fa-trash"></i> {{ __('Delete') }}
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">{{ __('Reviews') }}</h3>
                        @auth
                            <a href="{{ route('reviews.product.create', $advertisement) }}" class="btn btn-outline-primary">
                                <i class="fas fa-star"></i> {{ __('Review schrijven') }}
                            </a>
                        @endauth
                    </div>
                    <div class="p-3 mb-3">
                        <h5>{{ __('Beoordeling') }}</h5>
                        @if($advertisement->reviews->count() > 0)
                            <div>
                                @php
                                    $avgRating = $advertisement->reviews->avg('rating');
                                @endphp
                                
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= round($avgRating))
                                        <i class="fas fa-star text-warning"></i>
                                    @else
                                        <i class="far fa-star text-warning"></i>
                                    @endif
                                @endfor
                                
                                <span class="ms-2">{{ number_format($avgRating, 1) }}/5 ({{ $advertisement->reviews->count() }} {{ __('reviews') }})</span>
                            </div>
                        @else
                            <p>{{ __('Nog geen beoordelingen') }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Advertiser Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        {{ __('Advertiser Information') }}
                    </div>
                    <div class="card-body">
                        <h5>{{ $advertisement->user->name }}</h5>
                        @if ($advertisement->user->user_type == 'business_advertiser')
                            <p><strong>{{ __('Business') }}:</strong> {{ $advertisement->user->business_name }}</p>
                        @endif

                        <div class="mt-3">
                            <a href="mailto:{{ $advertisement->user->email }}" class="btn btn-outline-primary w-100 mb-2">
                                <i class="fas fa-envelope"></i> {{ __('Contact Advertiser') }}
                            </a>
                            @auth
                                <a href="{{ route('reviews.user.create', $advertisement->user) }}"
                                    class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-star"></i> {{ __('Review Advertiser') }}
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>

                <!-- Rental Information (for rentable items) -->
                @if ($advertisement->type == 'rent')
                    <div class="card">
                        <div class="card-header">{{ __('Rental Information') }}</div>
                        <div class="card-body">
                            <p><strong>{{ __('Price') }}:</strong> €{{ number_format($advertisement->price, 2) }}
                                {{ __('per day') }}</p>

                            <div id="availability-calendar" class="my-3">
                                <!-- Calendar placeholder - will be populated with JavaScript -->
                            </div>

                            <a href="{{ route('rentals.create.from.ad', $advertisement) }}" class="btn btn-success w-100">
                                <i class="fas fa-calendar-check"></i> {{ __('Check Availability & Rent') }}
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">{{ __('Confirm Deletion') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{ __('Are you sure you want to delete this advertisement? This action cannot be undone.') }}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <form action="{{ route('advertisements.destroy', $advertisement) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">{{ __('Delete') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Hier kan JavaScript voor de beschikbaarheidskalender toegevoegd worden
        document.addEventListener('DOMContentLoaded', function() {
            // Initialisatie van kalender, indien gewenst
        });
    </script>
@endpush
