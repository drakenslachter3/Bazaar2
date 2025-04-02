@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Create Advertisement') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('advertisements.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3 row">
                            <label for="title" class="col-md-4 col-form-label text-md-end">{{ __('Title') }} <span class="text-danger">*</span></label>
                            <div class="col-md-6">
                                <input id="title" type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title') }}" required autofocus>
                                @error('title')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="description" class="col-md-4 col-form-label text-md-end">{{ __('Description') }} <span class="text-danger">*</span></label>
                            <div class="col-md-6">
                                <textarea id="description" class="form-control @error('description') is-invalid @enderror" name="description" rows="5" required>{{ old('description') }}</textarea>
                                @error('description')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="price" class="col-md-4 col-form-label text-md-end">{{ __('Price') }} (€) <span class="text-danger">*</span></label>
                            <div class="col-md-6">
                                <input id="price" type="number" step="0.01" min="0" class="form-control @error('price') is-invalid @enderror" name="price" value="{{ old('price') }}" required>
                                @error('price')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="type" class="col-md-4 col-form-label text-md-end">{{ __('Advertisement Type') }} <span class="text-danger">*</span></label>
                            <div class="col-md-6">
                                <select id="type" class="form-control @error('type') is-invalid @enderror" name="type" required>
                                    <option value="">{{ __('Select Type') }}</option>
                                    <option value="sale" {{ old('type') == 'sale' ? 'selected' : '' }}>{{ __('For Sale') }}</option>
                                    <option value="rent" {{ old('type') == 'rent' ? 'selected' : '' }}>{{ __('For Rent') }}</option>
                                </select>
                                @error('type')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="expiry_date" class="col-md-4 col-form-label text-md-end">{{ __('Expiry Date') }}</label>
                            <div class="col-md-6">
                                <input id="expiry_date" type="date" class="form-control @error('expiry_date') is-invalid @enderror" name="expiry_date" value="{{ old('expiry_date') }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                                @error('expiry_date')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                <small class="form-text text-muted">{{ __('Leave empty for no expiry date') }}</small>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Create Advertisement') }}
                                </button>
                                <a href="{{ route('advertisements.index') }}" class="btn btn-outline-secondary ms-2">
                                    {{ __('Cancel') }}
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">{{ __('Bulk Import Advertisements') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('advertisements.import') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3 row">
                            <label for="csv_file" class="col-md-4 col-form-label text-md-end">{{ __('CSV File') }}</label>
                            <div class="col-md-6">
                                <input id="csv_file" type="file" class="form-control @error('csv_file') is-invalid @enderror" name="csv_file" accept=".csv" required>
                                @error('csv_file')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                <small class="form-text text-muted">
                                    {{ __('CSV must contain columns: title, description, price, type (sale/rent), expiry_date (optional)') }}
                                </small>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Import Advertisements') }}
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Sample CSV structure -->
                    <div class="mt-3">
                        <h6>{{ __('Sample CSV Structure:') }}</h6>
                        <pre class="bg-light p-2">title,description,price,type,expiry_date
Product 1,This is a description,19.99,sale,2023-12-31
Product 2,For rent product,5.99,rent,2023-10-15</pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection