@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>{{ __('Advertenties Importeren') }}</h1>

        <div class="card">
            <div class="card-header">{{ __('CSV Bestand Uploaden') }}</div>
            <div class="card-body">
                <form method="POST" action="{{ route('advertisements.import') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label for="csv_file" class="form-label">{{ __('CSV Bestand') }}</label>
                        <input type="file" class="form-control @error('csv_file') is-invalid @enderror" id="csv_file"
                            name="csv_file" accept=".csv">
                        @error('csv_file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            {{ __('Het CSV bestand moet de volgende kolommen bevatten: title, description, price, type (sale/rent), expiry_date (optioneel)') }}
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">{{ __('Importeren') }}</button>
                    <a href="{{ route('advertiser.dashboard') }}"
                        class="btn btn-outline-secondary ms-2">{{ __('Annuleren') }}</a>
                </form>

                <div class="mt-4">
                    <h5>{{ __('Voorbeeld CSV formaat:') }}</h5>
                    <pre class="bg-light p-3">title,description,price,type,expiry_date
Fiets te huur,Een mooie stadsfiets voor dagelijks gebruik,5.00,rent,2023-12-31
Boekenkast te koop,Massief houten boekenkast in goede staat,75.50,sale,
Verhuur van tuingereedschap,Complete set tuingereedschap te huur,12.99,rent,2023-10-15</pre>
                </div>
            </div>
        </div>
    </div>
@endsection
