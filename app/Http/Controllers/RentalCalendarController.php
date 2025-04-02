<?php 

namespace App\Http\Controllers;

use App\Models\Advertisement;
use App\Models\Rental;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RentalCalendarController extends Controller
{
    public function __construct()
    {
    }
    
    // Voor huurders: bekijk je gehuurde items
    public function renterCalendar()
    {
        $user = Auth::user();
        $rentals = $user->rentedProducts()->with('advertisement')->get();
        
        $events = [];
        
        foreach ($rentals as $rental) {
            $events[] = [
                'id' => $rental->id,
                'title' => 'Ophalen: ' . $rental->advertisement->title,
                'start' => $rental->start_date->format('Y-m-d'),
                'url' => route('rentals.show', $rental),
                'backgroundColor' => '#28a745',
                'borderColor' => '#28a745',
            ];
            
            $events[] = [
                'id' => $rental->id,
                'title' => 'Terugbrengen: ' . $rental->advertisement->title,
                'start' => $rental->end_date->format('Y-m-d'),
                'url' => route('rentals.show', $rental),
                'backgroundColor' => '#dc3545',
                'borderColor' => '#dc3545',
            ];
        }
        
        return view('calendar.renter', compact('events'));
    }
    
    // Voor verhuurders: bekijk je verhuurde items
    public function advertiserCalendar()
    {
        $user = Auth::user();
        $advertisements = $user->advertisements;
        
        $events = [];
        
        foreach ($advertisements as $advertisement) {
            $rentals = $advertisement->rentalPeriods;
            
            foreach ($rentals as $rental) {
                $events[] = [
                    'id' => $rental->id,
                    'title' => 'Uitgeleend: ' . $advertisement->title,
                    'start' => $rental->start_date->format('Y-m-d'),
                    'end' => $rental->end_date->format('Y-m-d'),
                    'url' => route('rentals.show', $rental),
                    'backgroundColor' => '#007bff',
                    'borderColor' => '#007bff',
                ];
            }
            
            // Voeg ook verlopen advertenties toe
            if ($advertisement->expiry_date) {
                $events[] = [
                    'id' => 'exp-' . $advertisement->id,
                    'title' => 'Advertentie verloopt: ' . $advertisement->title,
                    'start' => $advertisement->expiry_date->format('Y-m-d'),
                    'url' => route('advertisements.show', $advertisement),
                    'backgroundColor' => '#ffc107',
                    'borderColor' => '#ffc107',
                ];
            }
        }
        
        return view('calendar.advertiser', compact('events'));
    }
}