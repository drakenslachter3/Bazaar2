<!-- resources/views/advertisements/index.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>{{ __('Advertenties') }}</h1>
        </div>
        @auth
            @if(auth()->user()->user_type !== 'regular')
            <div class="col-md-4 text-end">
                <a href="{{ route('advertisements.createad') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> {{ __('Maak Advertentie') }}
                </a>
            </div>
            @endif
        @endauth
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-filter"></i> {{ __('Filters') }}
            </div>
            <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse" aria-expanded="false" aria-controls="filterCollapse">
                <i class="fas fa-chevron-down"></i>
            </button>
        </div>
        <div class="collapse {{ request()->hasAny(['type', 'search', 'min_price', 'max_price', 'advertiser_id', 'order_by', 'created_after', 'created_before']) ? 'show' : '' }}" id="filterCollapse">
            <div class="card-body">
                <form action="{{ route('advertisements.index') }}" method="GET" class="row g-3">
                    <div class="col-md-6 col-lg-4">
                        <label for="search" class="form-label">{{ __('Zoekterm') }}</label>
                        <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="{{ __('Zoek op titel, beschrijving...') }}">
                    </div>
                    
                    <div class="col-md-6 col-lg-4">
                        <label for="type" class="form-label">{{ __('Type') }}</label>
                        <select name="type" id="type" class="form-select">
                            <option value="">{{ __('Alle Types') }}</option>
                            <option value="sale" {{ request('type') == 'sale' ? 'selected' : '' }}>{{ __('Te Koop') }}</option>
                            <option value="rent" {{ request('type') == 'rent' ? 'selected' : '' }}>{{ __('Te Huur') }}</option>
                        </select>
                    </div>
                    
                    <div class="col-md-6 col-lg-4">
                        <label for="advertiser_id" class="form-label">{{ __('Adverteerder') }}</label>
                        <select name="advertiser_id" id="advertiser_id" class="form-select">
                            <option value="">{{ __('Alle Adverteerders') }}</option>
                            @foreach($advertisers as $advertiser)
                                <option value="{{ $advertiser->id }}" {{ request('advertiser_id') == $advertiser->id ? 'selected' : '' }}>
                                    {{ $advertiser->business_name ?: $advertiser->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-6 col-lg-3">
                        <label for="min_price" class="form-label">{{ __('Minimale prijs') }}</label>
                        <div class="input-group">
                            <span class="input-group-text">€</span>
                            <input type="number" class="form-control" id="min_price" name="min_price" value="{{ request('min_price') }}" min="0" step="0.01">
                        </div>
                    </div>
                    
                    <div class="col-md-6 col-lg-3">
                        <label for="max_price" class="form-label">{{ __('Maximale prijs') }}</label>
                        <div class="input-group">
                            <span class="input-group-text">€</span>
                            <input type="number" class="form-control" id="max_price" name="max_price" value="{{ request('max_price') }}" min="0" step="0.01">
                        </div>
                    </div>
                    
                    <div class="col-md-6 col-lg-3">
                        <label for="created_after" class="form-label">{{ __('Geplaatst na') }}</label>
                        <input type="date" class="form-control" id="created_after" name="created_after" value="{{ request('created_after') }}">
                    </div>
                    
                    <div class="col-md-6 col-lg-3">
                        <label for="created_before" class="form-label">{{ __('Geplaatst voor') }}</label>
                        <input type="date" class="form-control" id="created_before" name="created_before" value="{{ request('created_before') }}">
                    </div>
                    
                    <div class="col-md-6 col-lg-3">
                        <label for="order_by" class="form-label">{{ __('Sorteer op') }}</label>
                        <select name="order_by" id="order_by" class="form-select">
                            <option value="created_at" {{ request('order_by') == 'created_at' ? 'selected' : '' }}>{{ __('Datum') }}</option>
                            <option value="price" {{ request('order_by') == 'price' ? 'selected' : '' }}>{{ __('Prijs') }}</option>
                            <option value="title" {{ request('order_by') == 'title' ? 'selected' : '' }}>{{ __('Titel') }}</option>
                            <option value="type" {{ request('order_by') == 'type' ? 'selected' : '' }}>{{ __('Type') }}</option>
                        </select>
                    </div>
                    
                    <div class="col-md-6 col-lg-3">
                        <label for="order" class="form-label">{{ __('Volgorde') }}</label>
                        <select name="order" id="order" class="form-select">
                            <option value="desc" {{ request('order') == 'desc' ? 'selected' : '' }}>{{ __('Aflopend') }}</option>
                            <option value="asc" {{ request('order') == 'asc' ? 'selected' : '' }}>{{ __('Oplopend') }}</option>
                        </select>
                    </div>
                    
                    <div class="col-md-6 col-lg-3">
                        <label for="per_page" class="form-label">{{ __('Items per pagina') }}</label>
                        <select name="per_page" id="per_page" class="form-select">
                            <option value="15" {{ request('per_page') == 15 ? 'selected' : '' }}>15</option>
                            <option value="30" {{ request('per_page') == 30 ? 'selected' : '' }}>30</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                        </select>
                    </div>
                    
                    <div class="col-12 d-flex">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search"></i> {{ __('Zoeken') }}
                        </button>
                        <a href="{{ route('advertisements.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-undo"></i> {{ __('Reset') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Resultaten weergave knoppen -->
    <div class="mb-3 d-flex justify-content-between">
        <div>
            <span class="text-muted">{{ __('Toont') }} {{ $advertisements->firstItem() ?? 0 }}-{{ $advertisements->lastItem() ?? 0 }} {{ __('van') }} {{ $advertisements->total() }} {{ __('resultaten') }}</span>
        </div>
        <div class="btn-group" role="group">
            <input type="radio" class="btn-check" name="viewMode" id="viewGrid" autocomplete="off" checked>
            <label class="btn btn-outline-secondary" for="viewGrid">
                <i class="fas fa-th-large"></i>
            </label>
            
            <input type="radio" class="btn-check" name="viewMode" id="viewList" autocomplete="off">
            <label class="btn btn-outline-secondary" for="viewList">
                <i class="fas fa-list"></i>
            </label>
        </div>
    </div>

    <!-- Grid weergave -->
    <div id="gridView" class="row">
        @forelse($advertisements as $advertisement)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">{{ $advertisement->title }}</h5>
                        <h6 class="card-subtitle mb-2 text-muted">
                            <strong>€{{ number_format($advertisement->price, 2) }}</strong>
                            <span class="badge {{ $advertisement->type == 'rent' ? 'bg-info' : 'bg-success' }} ms-1">
                                {{ $advertisement->type == 'rent' ? __('Te Huur') : __('Te Koop') }}
                            </span>
                        </h6>
                        <p class="card-text">{{ Str::limit($advertisement->description, 100) }}</p>
                        <div class="mt-auto d-flex justify-content-between align-items-center">
                            <a href="{{ route('advertisements.show', $advertisement) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-eye"></i> {{ __('Bekijken') }}
                            </a>
                            
                            @auth
                                <form action="{{ route('advertisements.favorite', $advertisement) }}" method="GET">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="{{ auth()->user()->favorites()->where('advertisement_id', $advertisement->id)->exists() ? 'fas' : 'far' }} fa-heart"></i>
                                    </button>
                                </form>
                            @endauth
                        </div>
                    </div>
                    <div class="card-footer text-muted">
                        <small>{{ __('Geplaatst') }} {{ $advertisement->created_at->diffForHumans() }}</small>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    {{ __('Geen advertenties gevonden die voldoen aan de criteria.') }}
                </div>
            </div>
        @endforelse
    </div>

    <!-- List weergave (standaard verborgen) -->
    <div id="listView" class="d-none">
        <div class="list-group">
            @forelse($advertisements as $advertisement)
                <div class="list-group-item list-group-item-action">
                    <div class="d-flex w-100 justify-content-between">
                        <h5 class="mb-1">{{ $advertisement->title }}</h5>
                        <small>{{ $advertisement->created_at->diffForHumans() }}</small>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1">{{ Str::limit($advertisement->description, 150) }}</p>
                            <small>
                                <strong>€{{ number_format($advertisement->price, 2) }}</strong>
                                <span class="badge {{ $advertisement->type == 'rent' ? 'bg-info' : 'bg-success' }} ms-1">
                                    {{ $advertisement->type == 'rent' ? __('Te Huur') : __('Te Koop') }}
                                </span>
                            </small>
                        </div>
                        <div>
                            <a href="{{ route('advertisements.show', $advertisement) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-eye"></i> {{ __('Bekijken') }}
                            </a>
                            
                            @auth
                                <form action="{{ route('advertisements.favorite', $advertisement) }}" method="GET" class="d-inline">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="{{ auth()->user()->favorites()->where('advertisement_id', $advertisement->id)->exists() ? 'fas' : 'far' }} fa-heart"></i>
                                    </button>
                                </form>
                            @endauth
                        </div>
                    </div>
                </div>
            @empty
                <div class="alert alert-info">
                    {{ __('Geen advertenties gevonden die voldoen aan de criteria.') }}
                </div>
            @endforelse
        </div>
    </div>

    <!-- Paginering -->
    <div class="mt-4 d-flex justify-content-center">
        {{ $advertisements->withQueryString()->links() }}
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const gridView = document.getElementById('gridView');
        const listView = document.getElementById('listView');
        const viewGridBtn = document.getElementById('viewGrid');
        const viewListBtn = document.getElementById('viewList');
        
        viewGridBtn.addEventListener('change', function() {
            gridView.classList.remove('d-none');
            listView.classList.add('d-none');
            localStorage.setItem('advertViewMode', 'grid');
        });
        
        viewListBtn.addEventListener('change', function() {
            gridView.classList.add('d-none');
            listView.classList.remove('d-none');
            localStorage.setItem('advertViewMode', 'list');
        });
        
        // Herstel opgeslagen weergavemodus
        const savedViewMode = localStorage.getItem('advertViewMode');
        if (savedViewMode === 'list') {
            viewListBtn.checked = true;
            viewListBtn.dispatchEvent(new Event('change'));
        } else {
            viewGridBtn.checked = true;
            viewGridBtn.dispatchEvent(new Event('change'));
        }
    });
</script>
@endpush