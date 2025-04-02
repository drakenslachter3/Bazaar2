<?php

namespace App\Http\Controllers;

use App\Models\Advertisement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdvertiserController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
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
}