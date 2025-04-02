// resources/js/rental-calendar.js
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('rental-calendar');
    
    if (!calendarEl) return;
    
    // Controleer of er events data beschikbaar is in de data-events attributen van de kalender element
    const eventsData = calendarEl.dataset.events ? JSON.parse(calendarEl.dataset.events) : [];
    
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listMonth'
        },
        events: eventsData,
        editable: false,
        dayMaxEvents: true,
        eventClick: function(info) {
            if (info.event.url) {
                info.jsEvent.preventDefault();
                window.location.href = info.event.url;
            }
        },
        eventTimeFormat: {
            hour: '2-digit',
            minute: '2-digit',
            meridiem: false
        },
        buttonText: {
            today: translations.today || 'Today',
            month: translations.month || 'Month',
            week: translations.week || 'Week',
            list: translations.list || 'List'
        }
    });
    
    calendar.render();
});

// JavaScript voor het aanmaken van huurperiodes
document.addEventListener('DOMContentLoaded', function() {
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    const priceElement = document.getElementById('rental-price');
    const totalElement = document.getElementById('rental-total');
    
    if (!startDateInput || !endDateInput || !priceElement || !totalElement) return;
    
    const pricePerDay = parseFloat(priceElement.dataset.price || 0);
    
    function calculateTotal() {
        const startDate = new Date(startDateInput.value);
        const endDate = new Date(endDateInput.value);
        
        if (isNaN(startDate.getTime()) || isNaN(endDate.getTime())) {
            totalElement.textContent = '0.00';
            return;
        }
        
        // Bereken aantal dagen
        const diffTime = Math.abs(endDate - startDate);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        
        // Bereken totaal
        const total = (diffDays * pricePerDay).toFixed(2);
        totalElement.textContent = total;
    }
    
    startDateInput.addEventListener('change', calculateTotal);
    endDateInput.addEventListener('change', calculateTotal);
    
    // Controleren op beschikbare datums (zorg ervoor dat gereserveerde datums niet kunnen worden geselecteerd)
    const unavailableDates = JSON.parse(document.getElementById('unavailable-dates')?.dataset.dates || '[]');
    
    function isDateUnavailable(date) {
        const dateString = date.toISOString().split('T')[0];
        return unavailableDates.includes(dateString);
    }
    
    // Als we flatpickr gebruiken kunnen we datums makkelijker filteren
    if (typeof flatpickr === 'function') {
        flatpickr(startDateInput, {
            minDate: 'today',
            disable: [isDateUnavailable],
            onChange: function(selectedDates, dateStr, instance) {
                // Update minimum datum van einddatum
                const endDatePicker = endDateInput._flatpickr;
                if (endDatePicker) {
                    endDatePicker.set('minDate', dateStr);
                }
                calculateTotal();
            }
        });
        
        flatpickr(endDateInput, {
            minDate: startDateInput.value || 'today',
            disable: [isDateUnavailable],
            onChange: calculateTotal
        });
    }
});