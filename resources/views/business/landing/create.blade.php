@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ __('Add Landing Page Component') }}</h1>
    
    <div class="card">
        <div class="card-header">{{ __('Create Component') }}</div>
        
        <div class="card-body">
            <form action="{{ route('business.landing.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="form-group">
                    <label for="type">{{ __('Component Type') }}</label>
                    <select name="type" id="type" class="form-control @error('type') is-invalid @enderror" required>
                        <option value="">{{ __('Select Type') }}</option>
                        <option value="featured_ads">{{ __('Featured Advertisements') }}</option>
                        <option value="text">{{ __('Text Block') }}</option>
                        <option value="image">{{ __('Image') }}</option>
                        <option value="hero">{{ __('Hero Banner') }}</option>
                        <option value="cta">{{ __('Call to Action') }}</option>
                    </select>
                    @error('type')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="position">{{ __('Position') }}</label>
                    <input type="number" name="position" id="position" class="form-control @error('position') is-invalid @enderror" value="{{ old('position', 0) }}" required min="0">
                    @error('position')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                
                <!-- Text Block Content -->
                <div class="component-settings" id="text-settings" style="display: none;">
                    <div class="form-group">
                        <label for="settings-title">{{ __('Title') }}</label>
                        <input type="text" name="settings[title]" id="settings-title" class="form-control" value="{{ old('settings.title') }}">
                    </div>
                    
                    <div class="form-group">
                        <label for="content">{{ __('Content') }}</label>
                        <textarea name="content" id="content" class="form-control @error('content') is-invalid @enderror" rows="5">{{ old('content') }}</textarea>
                        @error('content')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="settings-align">{{ __('Alignment') }}</label>
                        <select name="settings[align]" id="settings-align" class="form-control">
                            <option value="left">{{ __('Left') }}</option>
                            <option value="center">{{ __('Center') }}</option>
                            <option value="right">{{ __('Right') }}</option>
                        </select>
                    </div>
                </div>
                
                <!-- Featured Ads Settings -->
                <div class="component-settings" id="featured_ads-settings" style="display: none;">
                    <div class="form-group">
                        <label for="settings-count">{{ __('Number of Advertisements') }}</label>
                        <input type="number" name="settings[count]" id="settings-count" class="form-control" value="{{ old('settings.count', 3) }}" min="1" max="12">
                    </div>
                    
                    <div class="form-group">
                        <label for="settings-order-by">{{ __('Order By') }}</label>
                        <select name="settings[order_by]" id="settings-order-by" class="form-control">
                            <option value="created_at">{{ __('Date Created') }}</option>
                            <option value="price">{{ __('Price') }}</option>
                            <option value="title">{{ __('Title') }}</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="settings-order">{{ __('Order') }}</label>
                        <select name="settings[order]" id="settings-order" class="form-control">
                            <option value="desc">{{ __('Descending') }}</option>
                            <option value="asc">{{ __('Ascending') }}</option>
                        </select>
                    </div>
                </div>
                
                <!-- Image and Hero Settings -->
                <div class="component-settings" id="image-settings" style="display: none;">
                    <div class="form-group">
                        <label for="image">{{ __('Image') }}</label>
                        <input type="file" name="image" id="image" class="form-control-file @error('image') is-invalid @enderror" accept="image/*">
                        @error('image')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="settings-alt-text">{{ __('Alt Text') }}</label>
                        <input type="text" name="settings[alt_text]" id="settings-alt-text" class="form-control" value="{{ old('settings.alt_text') }}">
                    </div>
                </div>
                
                <!-- CTA Settings -->
                <div class="component-settings" id="cta-settings" style="display: none;">
                    <div class="form-group">
                        <label for="content">{{ __('CTA Text') }}</label>
                        <textarea name="content" id="cta-content" class="form-control" rows="3">{{ old('content') }}</textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="settings-button-text">{{ __('Button Text') }}</label>
                        <input type="text" name="settings[button_text]" id="settings-button-text" class="form-control" value="{{ old('settings.button_text', 'Meer Info') }}">
                    </div>
                    
                    <div class="form-group">
                        <label for="settings-button-url">{{ __('Button URL') }}</label>
                        <input type="text" name="settings[button_url]" id="settings-button-url" class="form-control" value="{{ old('settings.button_url', '#') }}">
                    </div>
                    
                    <div class="form-group">
                        <label for="settings-button-color">{{ __('Button Color') }}</label>
                        <select name="settings[button_color]" id="settings-button-color" class="form-control">
                            <option value="primary">{{ __('Primary') }}</option>
                            <option value="secondary">{{ __('Secondary') }}</option>
                            <option value="success">{{ __('Success') }}</option>
                            <option value="danger">{{ __('Danger') }}</option>
                            <option value="warning">{{ __('Warning') }}</option>
                            <option value="info">{{ __('Info') }}</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">{{ __('Create Component') }}</button>
                    <a href="{{ route('business.landing.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const typeSelect = document.getElementById('type');
        const componentSettings = document.querySelectorAll('.component-settings');
        
        typeSelect.addEventListener('change', function() {
            // Verberg alle settings
            componentSettings.forEach(settings => {
                settings.style.display = 'none';
            });
            
            // Toon de juiste settings op basis van het geselecteerde type
            const selectedType = this.value;
            
            if (selectedType === 'text') {
                document.getElementById('text-settings').style.display = 'block';
            } else if (selectedType === 'featured_ads') {
                document.getElementById('featured_ads-settings').style.display = 'block';
            } else if (selectedType === 'image' || selectedType === 'hero') {
                document.getElementById('image-settings').style.display = 'block';
            } else if (selectedType === 'cta') {
                document.getElementById('cta-settings').style.display = 'block';
            }
        });
        
        // Trigger change event om de juiste velden te tonen bij pagina laden
        typeSelect.dispatchEvent(new Event('change'));
    });
</script>
@endpush