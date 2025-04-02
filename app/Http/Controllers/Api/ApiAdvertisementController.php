<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApiAdvertisementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }
    
    public function index(Request $request)
    {
        $business = $request->user()->business;
        
        if (!$business) {
            return response()->json(['error' => 'Geen zakelijk account gevonden.'], 403);
        }
        
        $query = Advertisement::where('business_id', $business->id)
                              ->where('active', true);
        
        // Filters
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }
        
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        
        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }
        
        // Sortering
        $orderBy = $request->input('order_by', 'created_at');
        $order = $request->input('order', 'desc');
        $query->orderBy($orderBy, $order);
        
        // Paginering
        $perPage = $request->input('per_page', 15);
        $advertisements = $query->paginate($perPage);
        
        return response()->json($advertisements);
    }
    
    public function show($id, Request $request)
    {
        $business = $request->user()->business;
        
        if (!$business) {
            return response()->json(['error' => 'Geen zakelijk account gevonden.'], 403);
        }
        
        $advertisement = Advertisement::where('id', $id)
                                     ->where('business_id', $business->id)
                                     ->first();
        
        if (!$advertisement) {
            return response()->json(['error' => 'Advertentie niet gevonden.'], 404);
        }
        
        return response()->json($advertisement);
    }
    
    public function store(Request $request)
    {
        $business = $request->user()->business;
        
        if (!$business) {
            return response()->json(['error' => 'Geen zakelijk account gevonden.'], 403);
        }
        
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'type' => 'required|in:sale,rent',
            'expiry_date' => 'nullable|date|after:today',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $advertisement = Advertisement::create([
            'title' => $request->title,
            'description' => $request->description,
            'price' => $request->price,
            'type' => $request->type,
            'user_id' => $request->user()->id,
            'business_id' => $business->id,
            'expiry_date' => $request->expiry_date,
            'active' => true,
        ]);
        
        // QR code genereren (hergebruik functie uit AdvertisementController)
        $this->generateQrCode($advertisement);
        
        return response()->json($advertisement, 201);
    }
    
    protected function generateQrCode($advertisement)
    {
        $url = url('/advertisements/' . $advertisement->id);
        $qrCode = \QrCode::format('png')->size(300)->generate($url);
        
        $filename = 'qrcodes/advert_' . $advertisement->id . '.png';
        \Storage::put('public/' . $filename, $qrCode);
        
        $advertisement->update(['qr_code_path' => $filename]);
    }
    
    public function update(Request $request, $id)
    {
        $business = $request->user()->business;
        
        if (!$business) {
            return response()->json(['error' => 'Geen zakelijk account gevonden.'], 403);
        }
        
        $advertisement = Advertisement::where('id', $id)
                                     ->where('business_id', $business->id)
                                     ->first();
        
        if (!$advertisement) {
            return response()->json(['error' => 'Advertentie niet gevonden.'], 404);
        }
        
        $validator = Validator::make($request->all(), [
            'title' => 'string|max:255',
            'description' => 'string',
            'price' => 'numeric|min:0',
            'type' => 'in:sale,rent',
            'expiry_date' => 'nullable|date|after:today',
            'active' => 'boolean',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $advertisement->update($request->all());
        
        return response()->json($advertisement);
    }
    
    public function destroy(Request $request, $id)
    {
        $business = $request->user()->business;
        
        if (!$business) {
            return response()->json(['error' => 'Geen zakelijk account gevonden.'], 403);
        }
        
        $advertisement = Advertisement::where('id', $id)
                                     ->where('business_id', $business->id)
                                     ->first();
        
        if (!$advertisement) {
            return response()->json(['error' => 'Advertentie niet gevonden.'], 404);
        }
        
        $advertisement->delete();
        
        return response()->json(null, 204);
    }
}   