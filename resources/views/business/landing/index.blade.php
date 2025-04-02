@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ __('Landing Page Manager') }}</h1>
    
    <div class="row mb-4">
        <div class="col-md-6">
            <p>{{ __('Manage the components on your landing page.') }}</p>
        </div>
        <div class="col-md-6 text-right">
            <a href="{{ route('business.landing.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> {{ __('Add Component') }}
            </a>
            <a href="{{ route('business.landing', $business->custom_url) }}" class="btn btn-outline-primary ml-2" target="_blank">
                <i class="fas fa-eye"></i> {{ __('View Page') }}
            </a>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">{{ __('Components') }}</div>
        
        <div class="card-body">
            @if ($components->isEmpty())
                <div class="alert alert-info">
                    {{ __('No components yet. Click "Add Component" to create your landing page.') }}
                </div>
            @else
                <ul class="list-group component-list" id="sortable-components">
                    @foreach ($components as $component)
                        <li class="list-group-item d-flex justify-content-between align-items-center" data-id="{{ $component->id }}">
                            <div>
                                <span class="drag-handle mr-2"><i class="fas fa-grip-lines"></i></span>
                                <strong>{{ __('Type') }}:</strong> 
                                @switch($component->type)
                                    @case('featured_ads')
                                        {{ __('Featured Advertisements') }}
                                        @break
                                    @case('text')
                                        {{ __('Text Block') }}
                                        @break
                                    @case('image')
                                        {{ __('Image') }}
                                        @break
                                    @case('hero')
                                        {{ __('Hero Banner') }}
                                        @break
                                    @case('cta')
                                        {{ __('Call to Action') }}
                                        @break
                                    @default
                                        {{ $component->type }}
                                @endswitch
                                
                                <span class="ml-3 text-muted small">{{ __('Position') }}: {{ $component->position }}</span>
                            </div>
                            
                            <div>
                                <a href="{{ route('business.landing.edit', $component) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit"></i> {{ __('Edit') }}
                                </a>
                                <form action="{{ route('business.landing.destroy', $component) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('{{ __('Are you sure?') }}')">
                                        <i class="fas fa-trash"></i> {{ __('Delete') }}
                                    </button>
                                </form>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sortableList = document.getElementById('sortable-components');
        
        if (sortableList) {
            const sortable = new Sortable(sortableList, {
                handle: '.drag-handle',
                animation: 150,
                onEnd: function() {
                    const componentIds = Array.from(sortableList.children).map(item => item.dataset.id);
                    
                    // Stuur de nieuwe volgorde naar de server
                    fetch('{{ route('business.landing.reorder') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ components: componentIds })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Optioneel: Toon een succesbericht
                            console.log('Components reordered successfully');
                        }
                    })
                    .catch(error => {
                        console.error('Error reordering components:', error);
                    });
                }
            });
        }
    });
</script>
@endpush