@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    @if($type == 'user')
                        {{ __('Review bewerken voor gebruiker: ') . $reviewedUser->name }}
                    @else
                        {{ __('Review bewerken voor: ') . $advertisement->title }}
                    @endif
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('reviews.update', $review) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="rating" class="form-label">{{ __('Beoordeling') }}</label>
                            <div class="rating-input">
                                <div class="d-flex">
                                    @for($i = 5; $i >= 1; $i--)
                                        <div class="form-check mx-2">
                                            <input class="form-check-input" type="radio" name="rating" id="rating{{ $i }}" value="{{ $i }}" {{ old('rating', $review->rating) == $i ? 'checked' : '' }} required>
                                            <label class="form-check-label" for="rating{{ $i }}">
                                                {{ $i }} <i class="fas fa-star text-warning"></i>
                                            </label>
                                        </div>
                                    @endfor
                                </div>
                            </div>
                            @error('rating')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="comment" class="form-label">{{ __('Jouw review') }}</label>
                            <textarea name="comment" id="comment" class="form-control @error('comment') is-invalid @enderror" rows="5" required>{{ old('comment', $review->comment) }}</textarea>
                            @error('comment')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            <small class="form-text text-muted">{{ __('Minimaal 10 karakters') }}</small>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                                {{ __('Annuleren') }}
                            </a>
                            <button type="submit" class="btn btn-primary">
                                {{ __('Review bijwerken') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection