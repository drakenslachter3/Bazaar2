@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="jumbotron">
                <h1>{{ __('Welcome to the Marketplace') }}</h1>
                <p class="lead">{{ __('Buy, sell, and rent items in your local community') }}</p>
                @guest
                    <p><a class="btn btn-primary btn-lg" href="{{ route('register') }}" role="button">{{ __('Join Now') }}</a></p>
                @endguest
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-12">
            <h2>{{ __('Latest Advertisements') }}</h2>
            <div class="row">
                @foreach($latestAds as $advertisement)
                    <div class="col-md-3 mb-4">
                        <div class="card">
                            @if($advertisement->image_path)
                                <img src="{{ asset('storage/' . $advertisement->image_path) }}" class="card-img-top" alt="{{ $advertisement->title }}">
                            @else
                                <div class="card-img-top bg-light text-center p-5">{{ __('No Image') }}</div>
                            @endif
                            <div class="card-body">
                                <h5 class="card-title">{{ $advertisement->title }}</h5>
                                <p class="card-text">€{{ number_format($advertisement->price, 2) }}</p>
                                <a href="{{ route('advertisements.show', $advertisement) }}" class="btn btn-primary">{{ __('View Details') }}</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <h2>{{ __('Items for Rent') }}</h2>
            <div class="row">
                @foreach($rentableAds as $advertisement)
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">{{ $advertisement->title }}</h5>
                                <p class="card-text">€{{ number_format($advertisement->price, 2) }}</p>
                                <a href="{{ route('advertisements.show', $advertisement) }}" class="btn btn-primary">{{ __('View Details') }}</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <a href="{{ route('advertisements.index', ['type' => 'rent']) }}" class="btn btn-outline-primary">{{ __('View All Rentals') }}</a>
        </div>
        
        <div class="col-md-6">
            <h2>{{ __('Items for Sale') }}</h2>
            <div class="row">
                @foreach($forSaleAds as $advertisement)
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">{{ $advertisement->title }}</h5>
                                <p class="card-text">€{{ number_format($advertisement->price, 2) }}</p>
                                <a href="{{ route('advertisements.show', $advertisement) }}" class="btn btn-primary">{{ __('View Details') }}</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <a href="{{ route('advertisements.index', ['type' => 'sale']) }}" class="btn btn-outline-primary">{{ __('View All For Sale') }}</a>
        </div>
    </div>
</div>
@endsection