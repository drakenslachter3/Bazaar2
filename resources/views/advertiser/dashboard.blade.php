@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ __('Mijn Advertenties Beheren') }}</h1>
    
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h3>{{ $activeCount }}</h3>
                    <p class="mb-0">{{ __('Actieve Advertenties') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-secondary text-white">
                <div class="card-body text-center">
                    <h3>{{ $inactiveCount }}</h3>
                    <p class="mb-0">{{ __('Inactieve Advertenties') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h3>{{ $saleCount }}</h3>
                    <p class="mb-0">{{ __('Te Koop') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h3>{{ $rentalCount }}</h3>
                    <p class="mb-0">{{ __('Te Huur') }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-8">
            <h2>{{ __('Mijn Advertenties') }}</h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('advertisements.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> {{ __('Nieuwe Advertentie') }}
            </a>
            @if(Auth::user()->user_type == 'business_advertiser')
                <a href="{{ route('advertisements.import.form') }}" class="btn btn-secondary ms-2">
                    <i class="fas fa-file-import"></i> {{ __('Importeren') }}
                </a>
            @endif
        </div>
    </div>
    
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>{{ __('Titel') }}</th>
                            <th>{{ __('Type') }}</th>
                            <th>{{ __('Prijs') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Aangemaakt') }}</th>
                            <th>{{ __('Verloopt') }}</th>
                            <th>{{ __('Acties') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($advertisements as $advertisement)
                            <tr>
                                <td>{{ $advertisement->title }}</td>
                                <td>
                                    @if($advertisement->type == 'rent')
                                        <span class="badge bg-info">{{ __('Te Huur') }}</span>
                                    @else
                                        <span class="badge bg-success">{{ __('Te Koop') }}</span>
                                    @endif
                                </td>
                                <td>€{{ number_format($advertisement->price, 2) }}</td>
                                <td>
                                    @if($advertisement->active)
                                        <span class="badge bg-success">{{ __('Actief') }}</span>
                                    @else
                                        <span class="badge bg-danger">{{ __('Inactief') }}</span>
                                    @endif
                                </td>
                                <td>{{ $advertisement->created_at->format('d-m-Y') }}</td>
                                <td>
                                    @if($advertisement->expiry_date)
                                        {{ $advertisement->expiry_date->format('d-m-Y') }}
                                        @if($advertisement->expiry_date->isPast())
                                            <span class="badge bg-danger">{{ __('Verlopen') }}</span>
                                        @elseif($advertisement->expiry_date->diffInDays(now()) < 7)
                                            <span class="badge bg-warning text-dark">{{ __('Bijna verlopen') }}</span>
                                        @endif
                                    @else
                                        <span class="text-muted">{{ __('Nooit') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('advertisements.show', $advertisement) }}" class="btn btn-sm btn-info" title="{{ __('Bekijken') }}">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('advertisements.edit', $advertisement) }}" class="btn btn-sm btn-primary" title="{{ __('Bewerken') }}">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $advertisement->id }}" title="{{ __('Verwijderen') }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    
                                    <!-- Delete Modal -->
                                    <div class="modal fade" id="deleteModal{{ $advertisement->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $advertisement->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteModalLabel{{ $advertisement->id }}">{{ __('Advertentie Verwijderen') }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    {{ __('Weet je zeker dat je deze advertentie wilt verwijderen?') }}<br>
                                                    <strong>{{ $advertisement->title }}</strong>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Annuleren') }}</button>
                                                    <form action="{{ route('advertisements.destroy', $advertisement) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">{{ __('Verwijderen') }}</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <p class="mb-0">{{ __('Je hebt nog geen advertenties geplaatst.') }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $advertisements->links() }}
            </div>
        </div>
    </div>
</div>
@endsection