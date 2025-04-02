<?php

namespace App\Http\Controllers;

use App\Models\Advertisement;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $latestAds = Advertisement::where('active', true)
                                 ->orderBy('created_at', 'desc')
                                 ->take(8)
                                 ->get();
        
        $rentableAds = Advertisement::where('active', true)
                                   ->where('type', 'rent')
                                   ->orderBy('created_at', 'desc')
                                   ->take(4)
                                   ->get();
                                   
        $forSaleAds = Advertisement::where('active', true)
                                  ->where('type', 'sale')
                                  ->orderBy('created_at', 'desc')
                                  ->take(4)
                                  ->get();
        
        return view('home', compact('latestAds', 'rentableAds', 'forSaleAds'));
    }
}
