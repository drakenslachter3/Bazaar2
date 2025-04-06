<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PurchaseHistoryController extends Controller
{
    public function __construct()
    {
    }
    
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = $user->purchasedProducts();
        
        // Filters toepassen
        if ($request->has('start_date')) {
            $query->where('purchase_date', '>=', $request->start_date);
        }
        
        if ($request->has('end_date')) {
            $query->where('purchase_date', '<=', $request->end_date);
        }
        
        if ($request->has('min_price')) {
            $query->where('amount', '>=', $request->min_price);
        }
        
        if ($request->has('max_price')) {
            $query->where('amount', '<=', $request->max_price);
        }
        
        // Sortering
        $orderBy = $request->input('order_by', 'purchase_date');
        $order = $request->input('order', 'desc');
        
        if (in_array($orderBy, ['purchase_date', 'amount', 'status'])) {
            $query->orderBy($orderBy, $order);
        }
        
        // Paginering
        $perPage = $request->input('per_page', 10);
        $purchases = $query->with('advertisement')->paginate($perPage);
        
        return view('purchases.index', compact('purchases'));
    }
}