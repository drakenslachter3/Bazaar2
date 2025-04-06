<!-- resources/views/purchases/index.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ __('Aankoop Geschiedenis') }}</h1>
    
    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-filter"></i> {{ __('Filters') }}
        </div>
        <div class="card-body">
            <form action="{{ route('purchases.index') }}" method="GET" class="row g-3">
                <div class="col-md-6 col-lg-3">
                    <label for="start_date" class="form-label">{{ __('Vanaf datum') }}</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-6 col-lg-3">
                    <label for="end_date" class="form-label">{{ __('Tot datum') }}</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-6 col-lg-3">
                    <label for="min_price" class="form-label">{{ __('Minimale prijs') }}</label>
                    <input type="number" class="form-control" id="min_price" name="min_price" value="{{ request('min_price') }}" min="0" step="0.01">
                </div>
                <div class="col-md-6 col-lg-3">
                    <label for="max_price" class="form-label">{{ __('Maximale prijs') }}</label>
                    <input type="number" class="form-control" id="max_price" name="max_price" value="{{ request('max_price') }}" min="0" step="0.01">
                </div>
                <div class="col-md-6 col-lg-3">
                    <label for="order_by" class="form-label">{{ __('Sorteer op') }}</label>
                    <select class="form-select" id="order_by" name="order_by">
                        <option value="purchase_date" {{ request('order_by') == 'purchase_date' ? 'selected' : '' }}>{{ __('Aankoopdatum') }}</option>
                        <option value="amount" {{ request('order_by') == 'amount' ? 'selected' : '' }}>{{ __('Prijs') }}</option>
                        <option value="status" {{ request('order_by') == 'status' ? 'selected' : '' }}>{{ __('Status') }}</option>
                    </select>
                </div>
                <div class="col-md-6 col-lg-3">
                    <label for="order" class="form-label">{{ __('Volgorde') }}</label>
                    <select class="form-select" id="order" name="order">
                        <option value="desc" {{ request('order') == 'desc' ? 'selected' : '' }}>{{ __('Aflopend') }}</option>
                        <option value="asc" {{ request('order') == 'asc' ? 'selected' : '' }}>{{ __('Oplopend') }}</option>
                    </select>
                </div>
                <div class="col-md-6 col-lg-3">
                    <label for="per_page" class="form-label">{{ __('Per pagina') }}</label>
                    <select class="form-select" id="per_page" name="per_page">
                        <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                    </select>
                </div>
                <div class="col-md-6 col-lg-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">{{ __('Toepassen') }}</button>
                    <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary">{{ __('Reset') }}</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Resultaten -->
    <div class="card">
        <div class="card-header">
            {{ __('Aankoopgeschiedenis') }} <span class="badge bg-primary">{{ $purchases->total() }} {{ __('resultaten') }}</span>
        </div>
        <div class="card-body">
            @if(count($purchases) > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>{{ __('Datum') }}</th>
                                <th>{{ __('Product') }}</th>
                                <th>{{ __('Bedrag') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Acties') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchases as $purchase)
                                <tr>
                                    <td>{{ $purchase->purchase_date->format('d-m-Y') }}</td>
                                    <td>
                                        @if($purchase->advertisement)
                                            <a href="{{ route('advertisements.show', $purchase->advertisement) }}">
                                                {{ $purchase->advertisement->title }}
                                            </a>
                                        @else
                                            <span class="text-muted">{{ __('Verwijderd product') }}</span>
                                        @endif
                                    </td>
                                    <td>€{{ number_format($purchase->amount, 2) }}</td>
                                    <td>
                                        @switch($purchase->status)
                                            @case('completed')
                                                <span class="badge bg-success">{{ __('Voltooid') }}</span>
                                                @break
                                            @case('pending')
                                                <span class="badge bg-warning text-dark">{{ __('In behandeling') }}</span>
                                                @break
                                            @case('cancelled')
                                                <span class="badge bg-danger">{{ __('Geannuleerd') }}</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">{{ $purchase->status }}</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            @if($purchase->advertisement)
                                                <a href="{{ route('advertisements.show', $purchase->advertisement) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i> {{ __('Bekijk') }}
                                                </a>
                                            @endif
                                            @if($purchase->advertisement && !$purchase->advertisement->reviews()->where('reviewer_id', auth()->id())->exists())
                                                <a href="{{ route('reviews.product.create', $purchase->advertisement) }}" class="btn btn-sm btn-outline-success">
                                                    <i class="fas fa-star"></i> {{ __('Beoordeel') }}
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Paginering -->
                <div class="mt-4">
                    {{ $purchases->withQueryString()->links() }}
                </div>
            @else
                <div class="alert alert-info">
                    {{ __('Je hebt nog geen aankopen gedaan.') }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection