<?php

namespace App\Http\Controllers;

use App\Models\Advertisement;
use App\Models\Rental;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RentalController extends Controller
{
    public function __construct()
    {
    }
    
    public function index()
    {
        $user = Auth::user();
        $rentals = $user->rentedProducts()->paginate(15);
        
        return view('rentals.index', compact('rentals'));
    }
    
    public function create(Advertisement $advertisement)
    {
        if ($advertisement->type !== 'rent') {
            return redirect()->back()->with('error', 'Dit product is niet beschikbaar voor verhuur.');
        }
        
        return view('rentals.create', compact('advertisement'));
    }
    
    public function store(Request $request, Advertisement $advertisement)
    {
        $request->validate([
            'start_date' => 'required|date|after:today',
            'end_date' => 'required|date|after:start_date',
        ]);
        
        // Controleer beschikbaarheid
        $conflictingRentals = Rental::where('advertisement_id', $advertisement->id)
            ->where(function($query) use ($request) {
                $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                    ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                    ->orWhere(function($q) use ($request) {
                        $q->where('start_date', '<=', $request->start_date)
                            ->where('end_date', '>=', $request->end_date);
                    });
            })
            ->where('status', '!=', 'cancelled')
            ->exists();
        
        if ($conflictingRentals) {
            return redirect()->back()->with('error', 'Het product is niet beschikbaar in deze periode.');
        }
        
        $rental = Rental::create([
            'advertisement_id' => $advertisement->id,
            'renter_id' => Auth::id(),
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => 'pending',
        ]);
        
        return redirect()->route('rentals.show', $rental)
            ->with('success', 'Huurverzoek is ingediend.');
    }
    
    public function show(Rental $rental)
    {
        $this->authorize('view', $rental);
        
        return view('rentals.show', compact('rental'));
    }
    
    public function update(Request $request, Rental $rental)
    {
        $this->authorize('update', $rental);
        
        $request->validate([
            'status' => 'required|in:pending,active,completed,cancelled',
        ]);
        
        $rental->update(['status' => $request->status]);
        
        return redirect()->route('rentals.show', $rental)
            ->with('success', 'Huuraanvraag status bijgewerkt.');
    }
    
    public function destroy(Rental $rental)
    {
        $this->authorize('delete', $rental);
        
        // Optioneel: in plaats van verwijderen, markeer als geannuleerd
        $rental->update(['status' => 'cancelled']);
        
        return redirect()->route('rentals.index')
            ->with('success', 'Huuraanvraag geannuleerd.');
    }
}