<!-- resources/views/reviews/show.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    {{ __('Review details') }}
                </div>

                <div class="card-body">
                    <div class="mb-4">
                        <h5>{{ __('Beoordeeld door') }}: {{ $review->reviewer->name }}</h5>
                        <div class="my-2">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fa{{ $i <= $review->rating ? 's' : 'r' }} fa-star text-warning"></i>
                            @endfor
                            <span class="ms-2">{{ $review->rating }}/5</span>
                        </div>
                        <div class="card bg-light">
                            <div class="card-body">
                                {{ $review->comment }}
                            </div>
                        </div>
                        <small class="text-muted">{{ __('Geplaatst op') }}: {{ $review->created_at->format('d M Y H:i') }}</small>
                    </div>

                    <div class="mt-4">
                        <h5>{{ __('Details') }}</h5>
                        <div class="card">
                            <div class="card-body">
                                @if($review->advertisement)
                                    <p><strong>{{ __('Beoordeeld product') }}:</strong> 
                                        <a href="{{ route('advertisements.show', $review->advertisement) }}">
                                            {{ $review->advertisement->title }}
                                        </a>
                                    </p>
                                @endif
                                
                                @if($review->reviewedUser)
                                    <p><strong>{{ __('Beoordeelde gebruiker') }}:</strong> {{ $review->reviewedUser->name }}</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                            {{ __('Terug') }}
                        </a>
                        
                        @if(auth()->id() == $review->reviewer_id)
                            <div>
                                <a href="{{ route('reviews.edit', $review) }}" class="btn btn-primary">
                                    {{ __('Bewerken') }}
                                </a>
                                <form action="{{ route('reviews.destroy', $review) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('{{ __('Weet je zeker dat je deze review wilt verwijderen?') }}')">
                                        {{ __('Verwijderen') }}
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection