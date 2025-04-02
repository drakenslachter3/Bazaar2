@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ __('Mijn Reviews') }}</h1>
    
    <div class="mb-4">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link {{ request('type') != 'received' ? 'active' : '' }}" href="{{ route('reviews.index') }}">
                    {{ __('Door mij geschreven') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('type') == 'received' ? 'active' : '' }}" href="{{ route('reviews.index', ['type' => 'received']) }}">
                    {{ __('Ontvangen reviews') }}
                </a>
            </li>
        </ul>
    </div>
    
    <div class="row">
        @forelse($reviews as $review)
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fa{{ $i <= $review->rating ? 's' : 'r' }} fa-star text-warning"></i>
                                @endfor
                                <span class="ms-2">{{ $review->rating }}/5</span>
                            </div>
                            <small class="text-muted">{{ $review->created_at->format('d M Y') }}</small>
                        </div>
                    </div>
                    <div class="card-body">
                        @if(request('type') == 'received')
                            <h5 class="card-title">{{ __('Geschreven door') }}: {{ $review->reviewer->name }}</h5>
                        @endif
                        
                        @if($review->advertisement)
                            <h5 class="card-title">{{ $review->advertisement->title }}</h5>
                            <p class="text-muted">{{ __('Product review') }}</p>
                        @elseif($review->reviewedUser)
                            <h5 class="card-title">{{ $review->reviewedUser->name }}</h5>
                            <p class="text-muted">{{ __('Gebruiker review') }}</p>
                        @endif
                        
                        <p class="card-text">{{ Str::limit($review->comment, 150) }}</p>
                        
                        <div class="mt-3">
                            <a href="{{ route('reviews.show', $review) }}" class="btn btn-primary btn-sm">
                                {{ __('Bekijk review') }}
                            </a>
                            
                            @if(auth()->id() == $review->reviewer_id && request('type') != 'received')
                                <a href="{{ route('reviews.edit', $review) }}" class="btn btn-outline-primary btn-sm">
                                    {{ __('Bewerken') }}
                                </a>
                                <form action="{{ route('reviews.destroy', $review) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('{{ __('Weet je zeker dat je deze review wilt verwijderen?') }}')">
                                        {{ __('Verwijderen') }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    @if(request('type') == 'received')
                        {{ __('Je hebt nog geen reviews ontvangen.') }}
                    @else
                        {{ __('Je hebt nog geen reviews geschreven.') }}
                        <p class="mt-2">{{ __('Schrijf reviews voor producten die je hebt gekocht of gehuurd, of voor gebruikers waar je zaken mee hebt gedaan.') }}</p>
                    @endif
                </div>
            </div>
        @endforelse
    </div>
    
    <div class="mt-4">
        {{ $reviews->links() }}
    </div>
</div>
@endsection