@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ __('Mijn Huurkalender') }}</h1>
    <p class="lead">{{ __('Een overzicht van je gehuurde producten en wanneer je ze moet ophalen en terugbrengen.') }}</p>
    
    <div class="card">
        <div class="card-body">
            <div id="rental-calendar"></div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Legenda') }}</div>
                <div class="card-body">
                    <div class="d-flex flex-wrap">
                        <div class="me-4 mb-2">
                            <span class="badge bg-success">&nbsp;</span> {{ __('Ophalen product') }}
                        </div>
                        <div class="me-4 mb-2">
                            <span class="badge bg-danger">&nbsp;</span> {{ __('Terugbrengen product') }}
                        </div>
                        <div class="me-4 mb-2">
                            <span class="badge bg-primary">&nbsp;</span> {{ __('Huurperiode') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="mt-4">
        <a href="{{ route('my.rentals') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> {{ __('Terug naar Huuroverzicht') }}
        </a>
    </div>
</div>
@endsection

@push('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css' rel='stylesheet' />
<style>
    #rental-calendar {
        height: 600px;
    }
    .fc-event {
        cursor: pointer;
    }
    .fc-today {
        background-color: rgba(0, 123, 255, 0.1) !important;
    }
</style>
@endpush

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/locales/nl.js'></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // De events data komt uit de controller
        const events = @json($events);
        
        // Initialiseer de kalender
        const calendarEl = document.getElementById('rental-calendar');
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: '{{ app()->getLocale() }}',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,listMonth'
            },
            events: events,
            eventTimeFormat: {
                hour: '2-digit',
                minute: '2-digit',
                meridiem: false
            },
            firstDay: 1, // Maandag als eerste dag van de week
            buttonText: {
                today: '{{ __("Vandaag") }}',
                month: '{{ __("Maand") }}',
                week: '{{ __("Week") }}',
                list: '{{ __("Lijst") }}'
            },
            eventClick: function(info) {
                if (info.event.url) {
                    info.jsEvent.preventDefault();
                    window.location.href = info.event.url;
                }
            },
            eventDidMount: function(info) {
                // Voeg tooltips toe aan de events voor extra informatie
                const eventTitle = info.event.title;
                const eventEl = info.el;
                
                if (eventEl) {
                    eventEl.setAttribute('title', eventTitle);
                    
                    // We zouden hier Bootstrap tooltips kunnen initialiseren
                    // maar dat is optioneel
                }
            }
        });
        
        calendar.render();
        
        // Responsive aanpassingen
        window.addEventListener('resize', function() {
            if (window.innerWidth < 768) {
                calendar.changeView('listMonth');
            } else {
                calendar.changeView('dayGridMonth');
            }
        });
        
        // Check initiële schermgrootte
        if (window.innerWidth < 768) {
            calendar.changeView('listMonth');
        }
    });
</script>
@endpush