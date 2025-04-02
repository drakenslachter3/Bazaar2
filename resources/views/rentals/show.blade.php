@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('Rental Request Details') }}</h5>
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
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h4>{{ $rental->advertisement->title }}</h4>
                            <p>{{ $rental->advertisement->description }}</p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>{{ __('Rental Details') }}</h5>
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th>{{ __('Start Date') }}</th>
                                        <td>{{ $rental->start_date->format('d M Y') }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('End Date') }}</th>
                                        <td>{{ $rental->end_date->format('d M Y') }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Duration') }}</th>
                                        <td>{{ $rental->start_date->diffInDays($rental->end_date) + 1 }} {{ __('days') }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Price per Day') }}</th>
                                        <td>€{{ number_format($rental->advertisement->price, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Total Price') }}</th>
                                        <td>€{{ number_format(($rental->advertisement->price * ($rental->start_date->diffInDays($rental->end_date) + 1)), 2) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>{{ __('Owner Information') }}</h5>
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th>{{ __('Name') }}</th>
                                        <td>{{ $rental->advertisement->user->name }}</td>
                                    </tr>
                                    @if($rental->advertisement->user->user_type == 'business_advertiser')
                                    <tr>
                                        <th>{{ __('Business') }}</th>
                                        <td>{{ $rental->advertisement->user->business_name }}</td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <th>{{ __('Contact') }}</th>
                                        <td>{{ $rental->advertisement->user->email }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h5>{{ __('Timeline') }}</h5>
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <i class="fas fa-paper-plane"></i> {{ __('Request Submitted') }}: {{ $rental->created_at->format('d M Y H:i') }}
                                </li>
                                @if($rental->status == 'active' || $rental->status == 'completed')
                                <li class="list-group-item">
                                    <i class="fas fa-check"></i> {{ __('Request Approved') }}: {{ $rental->updated_at->format('d M Y H:i') }}
                                </li>
                                @endif
                                @if($rental->status == 'completed')
                                <li class="list-group-item">
                                    <i class="fas fa-flag-checkered"></i> {{ __('Rental Completed') }}: {{ $rental->updated_at->format('d M Y H:i') }}
                                </li>
                                @endif
                                @if($rental->status == 'cancelled')
                                <li class="list-group-item">
                                    <i class="fas fa-times"></i> {{ __('Request Cancelled') }}: {{ $rental->updated_at->format('d M Y H:i') }}
                                </li>
                                @endif
                            </ul>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('rentals.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> {{ __('Back to Rentals') }}
                        </a>
                        
                        <div>
                            @if($rental->status == 'pending')
                                <form action="{{ route('rentals.destroy', $rental) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('{{ __('Are you sure you want to cancel this rental?') }}')">
                                        <i class="fas fa-times"></i> {{ __('Cancel Rental') }}
                                    </button>
                                </form>
                            @endif
                            
                            @if($rental->status == 'completed')
                                <a href="{{ route('reviews.product.create', $rental->advertisement) }}" class="btn btn-success">
                                    <i class="fas fa-star"></i> {{ __('Leave Review') }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection