<?php 
namespace App\Http\Controllers;

use App\Models\Rental;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RentalCalendarController extends Controller
{
    public function __construct()
    {
    }
    
    public function renterCalendar()
    {
        $user = Auth::user();
        $rentals = $user->rentedProducts()
            ->with('advertisement')
            ->get();
        
        $events = [];
        
        foreach ($rentals as $rental) {
            // Voeg ophaalmoment toe (start van huur)
            $events[] = [
                'id' => 'pickup-' . $rental->id,
                'title' => 'Ophalen: ' . $rental->advertisement->title,
                'start' => $rental->start_date->format('Y-m-d'),
                'url' => route('rentals.show', $rental->id),
                'backgroundColor' => '#28a745', // groen
                'borderColor' => '#28a745',
                'allDay' => true
            ];
            
            // Voeg terugbrengmoment toe (einde van huur)
            $events[] = [
                'id' => 'return-' . $rental->id,
                'title' => 'Terugbrengen: ' . $rental->advertisement->title,
                'start' => $rental->end_date->format('Y-m-d'),
                'url' => route('rentals.show', $rental->id),
                'backgroundColor' => '#dc3545', // rood
                'borderColor' => '#dc3545',
                'allDay' => true
            ];
            
            // Voeg de volledige huurperiode toe als aparte event (optioneel)
            if ($rental->start_date->format('Y-m-d') != $rental->end_date->format('Y-m-d')) {
                $events[] = [
                    'id' => 'period-' . $rental->id,
                    'title' => 'Gehuurd: ' . $rental->advertisement->title,
                    'start' => $rental->start_date->format('Y-m-d'),
                    'end' => $rental->end_date->addDay()->format('Y-m-d'), // +1 dag zodat het inclusief de eind-dag is
                    'url' => route('rentals.show', $rental->id),
                    'backgroundColor' => '#007bff', // blauw
                    'borderColor' => '#007bff',
                    'textColor' => '#ffffff',
                    'rendering' => 'background',
                ];
            }
        }
        
        return view('calendar.renter', compact('events'));
    }
}