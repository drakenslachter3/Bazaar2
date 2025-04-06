<?php
namespace App\Http\Controllers;

use App\Models\Advertisement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AdvertisementController extends Controller
{
    public function __construct()
    {
    }

    public function index(Request $request)
    {
        $query = Advertisement::query()->where('active', true);

        // Filters toepassen
        if ($request->filled('type') && in_array($request->type, ['sale', 'rent'])) {
            $query->where('type', $request->type);
        }

        if ($request->filled('min_price') && is_numeric($request->min_price)) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price') && is_numeric($request->max_price)) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'LIKE', $searchTerm)
                    ->orWhere('description', 'LIKE', $searchTerm);
            });
        }

        if ($request->filled('advertiser_id') && is_numeric($request->advertiser_id)) {
            $query->where('user_id', $request->advertiser_id);
        }

        if ($request->filled('created_after')) {
            try {
                $date = new \DateTime($request->created_after);
                $query->where('created_at', '>=', $date->format('Y-m-d'));
            } catch (\Exception $e) {
                // Ongeldige datum-formaat - negeer filter
            }
        }

        if ($request->filled('created_before')) {
            try {
                $date = new \DateTime($request->created_before);
                $query->where('created_at', '<=', $date->format('Y-m-d 23:59:59'));
            } catch (\Exception $e) {
                // Ongeldige datum-formaat - negeer filter
            }
        }

        // Sortering
        $allowedOrderByFields = ['title', 'price', 'created_at', 'type'];
        $orderBy              = $request->input('order_by', 'created_at');
        $order                = in_array($request->input('order'), ['asc', 'desc']) ? $request->input('order') : 'desc';

        if (in_array($orderBy, $allowedOrderByFields)) {
            $query->orderBy($orderBy, $order);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // Paginering
        $perPage        = (int) $request->input('per_page', 15);
        $perPage        = in_array($perPage, [15, 30, 50, 100]) ? $perPage : 15;
        $advertisements = $query->paginate($perPage);

        // Haal unieke adverteerders op voor het filter dropdown
        $advertisers = User::whereIn('user_type', ['private_advertiser', 'business_advertiser'])
            ->whereHas('advertisements', function ($q) {
                $q->where('active', true);
            })
            ->get(['id', 'name', 'business_name']);

        return view('advertisements.index', compact('advertisements', 'advertisers'));
    }

    public function create()
    {
        return view('advertisements.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'price'       => 'required|numeric|min:0',
            'type'        => 'required|in:sale,rent',
            'expiry_date' => 'nullable|date|after:today',
        ]);

        $user = Auth::user();

        $advertisement = new Advertisement([
            'title'       => $request->title,
            'description' => $request->description,
            'price'       => $request->price,
            'type'        => $request->type,
            'user_id'     => $user->id,
            'business_id' => $user->business->id ?? null,
            'active'      => true,
            'expiry_date' => $request->expiry_date,
        ]);

        $advertisement->save();

        // Verwijder de QR-code generatie functie
        // $this->generateQrCode($advertisement);

        return redirect()->route('advertisements.show', $advertisement)
            ->with('success', 'Advertentie succesvol aangemaakt.');
    }

    protected function generateQrCode($advertisement)
    {
        $url    = route('advertisements.show', $advertisement->id);
        $qrCode = QrCode::format('png')->size(300)->generate($url);

        $filename = 'qrcodes/advert_' . $advertisement->id . '.png';
        Storage::put('public/' . $filename, $qrCode);

        $advertisement->update(['qr_code_path' => $filename]);
    }

    public function show(Advertisement $advertisement)
    {
        return view('advertisements.show', compact('advertisement'));
    }

    public function edit(Advertisement $advertisement)
    {
        // $this->authorize('update', $advertisement);

        return view('advertisements.edit', compact('advertisement'));
    }

    public function update(Request $request, Advertisement $advertisement)
    {
        // $this->authorize('update', $advertisement);

        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'price'       => 'required|numeric|min:0',
            'type'        => 'required|in:sale,rent',
            'expiry_date' => 'nullable|date|after:today',
            'active'      => 'boolean',
        ]);

        $advertisement->update($request->all());

        return redirect()->route('advertisements.show', $advertisement)
            ->with('success', 'Advertentie succesvol bijgewerkt.');
    }

    public function destroy(Advertisement $advertisement)
    {
        $this->authorize('delete', $advertisement);

        $advertisement->delete();

        return redirect()->route('advertisements.index')
            ->with('success', 'Advertentie verwijderd.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('csv_file');
        $path = $file->getRealPath();

        $csv    = array_map('str_getcsv', file($path));
        $header = array_shift($csv);

        $requiredColumns = ['title', 'description', 'price', 'type'];
        $missingColumns  = array_diff($requiredColumns, $header);

        if (! empty($missingColumns)) {
            return redirect()->back()->with('error', 'CSV ontbreekt verplichte kolommen: ' . implode(', ', $missingColumns));
        }

        $user        = Auth::user();
        $importCount = 0;

        foreach ($csv as $row) {
            $data = array_combine($header, $row);

            try {
                $advertisement = new Advertisement([
                    'title'       => $data['title'],
                    'description' => $data['description'],
                    'price'       => $data['price'],
                    'type'        => $data['type'],
                    'user_id'     => $user->id,
                    'business_id' => $user->business->id ?? null,
                    'active'      => true,
                    'expiry_date' => $data['expiry_date'] ?? null,
                ]);

                $advertisement->save();
                $this->generateQrCode($advertisement);
                $importCount++;
            } catch (\Exception $e) {
                Log::error('Fout bij importeren rij: ' . json_encode($data) . ', fout: ' . $e->getMessage());
                continue;
            }
        }

        return redirect()->route('advertisements.index')
            ->with('success', $importCount . ' advertenties succesvol geïmporteerd.');
    }

    public function upcoming()
    {
        $user        = Auth::user();
        $expiringAds = $user->advertisements()
            ->where('expiry_date', '>', now())
            ->where('expiry_date', '<', now()->addDays(30))
            ->orderBy('expiry_date')
            ->paginate(15);

        return view('advertisements.upcoming', compact('expiringAds'));
    }

    public function toggleFavorite(Advertisement $advertisement)
    {
        $user = Auth::user();

        if ($user->favorites()->where('advertisement_id', $advertisement->id)->exists()) {
            $user->favorites()->detach($advertisement->id);
            $message = 'Advertentie verwijderd uit favorieten.';
        } else {
            $user->favorites()->attach($advertisement->id);
            $message = 'Advertentie toegevoegd aan favorieten.';
        }

        return redirect()->back()->with('success', $message);
    }

    public function favorites()
    {
        $user      = Auth::user();
        $favorites = $user->favorites()->paginate(15);

        return view('advertisements.favorites', compact('favorites'));
    }
}
