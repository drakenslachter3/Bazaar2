@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="jumbotron">
                <h1>{{ __('messages.welcome') }}</h1>
                <p class="lead">{{ __('messages.welcome_under') }}</p>
                @guest
                    <p><a class="btn btn-primary btn-lg" href="{{ route('register') }}" role="button">{{ __('messages.join_now') }}</a></p>
                @endguest
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-12">
            <h2>{{ __('messages.advertisements') }}</h2>
            <div class="row">
                @foreach($latestAds as $advertisement)
                    <div class="col-md-3 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">{{ $advertisement->title }}</h5>
                                <p class="card-text">€{{ number_format($advertisement->price, 2) }}</p>
                                <a href="{{ route('advertisements.show', $advertisement) }}" class="btn btn-primary">{{ __('messages.view_details') }}</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <h2>{{ __('messages.items_for_rent') }}</h2>
            <div class="row">
                @foreach($rentableAds as $advertisement)
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">{{ $advertisement->title }}</h5>
                                <p class="card-text">€{{ number_format($advertisement->price, 2) }}</p>
                                <a href="{{ route('advertisements.show', $advertisement) }}" class="btn btn-primary">{{ __('messages.view_details') }}</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <a href="{{ route('advertisements.index', ['type' => 'rent']) }}" class="btn btn-outline-primary">{{ __('messages.view_all') }}</a>
        </div>
        
        <div class="col-md-6">
            <h2>{{ __('messages.items_for_sale') }}</h2>
            <div class="row">
                @foreach($forSaleAds as $advertisement)
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">{{ $advertisement->title }}</h5>
                                <p class="card-text">€{{ number_format($advertisement->price, 2) }}</p>
                                <a href="{{ route('advertisements.show', $advertisement) }}" class="btn btn-primary">{{ __('messages.view_details') }}</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <a href="{{ route('advertisements.index', ['type' => 'sale']) }}" class="btn btn-outline-primary">{{ __('messages.view_all') }}</a>
        </div>
    </div>
</div>
@endsection