<?php
namespace App\Http\Controllers;

use App\Models\Advertisement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Rental;

class AdvertiserController extends Controller
{
    public function dashboard()
    {
        $user           = Auth::user();
        $advertisements = Advertisement::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $activeCount = Advertisement::where('user_id', $user->id)
            ->where('active', true)
            ->count();

        $inactiveCount = Advertisement::where('user_id', $user->id)
            ->where('active', false)
            ->count();

        $rentalCount = Advertisement::where('user_id', $user->id)
            ->where('type', 'rent')
            ->count();

        $saleCount = Advertisement::where('user_id', $user->id)
            ->where('type', 'sale')
            ->count();

        return view('advertiser.dashboard', compact(
            'advertisements',
            'activeCount',
            'inactiveCount',
            'rentalCount',
            'saleCount'
        ));
    }

    public function importForm()
    {
        return view('advertiser.import');
    }

    public function rentalRequests(Request $request)
    {
        $user = Auth::user();

        // Haal alle verhuurverzoeken op voor advertenties van deze gebruiker
        $query = Rental::whereHas('advertisement', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->with(['advertisement', 'renter']);

        // Filter op status indien opgegeven
        if ($request->has('status') && in_array($request->status, ['pending', 'active', 'completed', 'cancelled'])) {
            $query->where('status', $request->status);
        }

        $rentals = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('advertiser.rental_requests', compact('rentals'));
    }

    /**
     * Bijwerken van een verhuurverzoek (accepteren of afwijzen)
     */
    public function updateRentalRequest(Request $request, Rental $rental)
    {
        $user = Auth::user();

        // Controleer of de gebruiker de eigenaar is van de advertentie
        if ($rental->advertisement->user_id !== $user->id) {
            return redirect()->back()->with('error', 'Je hebt geen toestemming om dit verhuurverzoek te beheren.');
        }

        $request->validate([
            'status' => 'required|in:active,completed,cancelled',
        ]);

        $rental->status = $request->status;
        $rental->save();

        $statusLabels = [
            'active'    => 'geaccepteerd',
            'completed' => 'voltooid',
            'cancelled' => 'geannuleerd',
        ];

        return redirect()->back()->with('success', 'Verhuurverzoek is ' . $statusLabels[$request->status] . '.');
    }
}
