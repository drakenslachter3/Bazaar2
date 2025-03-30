<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BusinessOwner
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect('login');
        }
        
        $user = Auth::user();
        
        if ($user->user_type !== 'business_advertiser' || !$user->business) {
            return redirect('/')->with('error', 'Toegang geweigerd. Alleen zakelijke gebruikers hebben toegang tot deze functie.');
        }
        
        return $next($request);
    }
}