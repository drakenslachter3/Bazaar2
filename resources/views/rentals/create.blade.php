@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Rent Item') }}: {{ $advertisement->title }}</div>

                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <h5>{{ $advertisement->title }}</h5>
                                <p>{{ Str::limit($advertisement->description, 150) }}</p>
                                <p class="text-primary fw-bold">{{ __('Price') }}:
                                    €{{ number_format($advertisement->price, 2) }} {{ __('per day') }}</p>
                                <p><strong>{{ __('Owner') }}:</strong> {{ $advertisement->user->name }}</p>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('rentals.store', $advertisement) }}">
                            @csrf
                            <input type="hidden" name="advertisement_id" value="{{ $advertisement->id }}">
                            <div class="mb-3 row">
                                <label for="start_date" class="col-md-4 col-form-label text-md-end">{{ __('Start Date') }}
                                    <span class="text-danger">*</span></label>
                                <div class="col-md-6">
                                    <input id="start_date" type="date"
                                        class="form-control @error('start_date') is-invalid @enderror" name="start_date"
                                        required min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                        value="{{ old('start_date') }}">
                                    @error('start_date')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label for="end_date" class="col-md-4 col-form-label text-md-end">{{ __('End Date') }}
                                    <span class="text-danger">*</span></label>
                                <div class="col-md-6">
                                    <input id="end_date" type="date"
                                        class="form-control @error('end_date') is-invalid @enderror" name="end_date"
                                        required min="{{ date('Y-m-d', strtotime('+2 days')) }}"
                                        value="{{ old('end_date') }}">
                                    @error('end_date')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label class="col-md-4 col-form-label text-md-end">{{ __('Total Price') }}</label>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <span class="input-group-text">€</span>
                                        <input type="text" class="form-control" id="total-price" readonly value="0.00">
                                    </div>
                                    <small class="form-text text-muted" id="total-days">{{ __('0 days') }}</small>
                                </div>
                            </div>
                            @if (session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    {{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif
                            <div class="mb-3 row">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Submit Rental Request') }}
                                    </button>
                                    <a href="{{ route('advertisements.show', $advertisement) }}"
                                        class="btn btn-outline-secondary ms-2">
                                        {{ __('Cancel') }}
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden availability data -->
    <div id="unavailable-dates" data-dates="{{ json_encode($unavailableDates ?? []) }}"></div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');
            const totalPriceInput = document.getElementById('total-price');
            const totalDaysElement = document.getElementById('total-days');
            const pricePerDay = {{ $advertisement->price }};

            // Functie om het aantal dagen te berekenen
            function calculateTotalPrice() {
                const startDate = new Date(startDateInput.value);
                const endDate = new Date(endDateInput.value);

                if (isNaN(startDate.getTime()) || isNaN(endDate.getTime())) {
                    totalPriceInput.value = '0.00';
                    totalDaysElement.textContent = '0 dagen';
                    return;
                }

                // Bereken het aantal dagen
                const diffTime = Math.abs(endDate - startDate);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

                // Bereken de totale prijs
                const totalPrice = (diffDays * pricePerDay).toFixed(2);

                totalPriceInput.value = totalPrice;
                totalDaysElement.textContent = diffDays + (diffDays === 1 ? ' dag' : ' dagen');
            }

            // Luister naar wijzigingen in de datumvelden
            startDateInput.addEventListener('change', function() {
                // Zorg ervoor dat de einddatum niet vóór de startdatum kan zijn
                const nextDay = new Date(startDateInput.value);
                nextDay.setDate(nextDay.getDate() + 1);

                const nextDayStr = nextDay.toISOString().split('T')[0];
                endDateInput.min = nextDayStr;

                if (endDateInput.value < nextDayStr) {
                    endDateInput.value = nextDayStr;
                }

                calculateTotalPrice();
            });

            endDateInput.addEventListener('change', calculateTotalPrice);

            // Initieel berekenen als er al waarden ingevuld zijn
            calculateTotalPrice();
        });
    </script>
@endpush
