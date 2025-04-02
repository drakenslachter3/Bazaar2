<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function __construct()
    {
    }
    
    public function edit()
    {
        $user = Auth::user();
        
        return view('profile.edit', compact('user'));
    }
    
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'current_password' => 'nullable|required_with:password',
            'password' => 'nullable|min:8|confirmed',
        ]);
        
        // Controleer het huidige wachtwoord als een nieuw wachtwoord is opgegeven
        if ($request->filled('current_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Het huidige wachtwoord is onjuist.']);
            }
        }
        
        $user->name = $request->name;
        $user->email = $request->email;
        
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        
        // Taalinstelling bijwerken
        if ($request->has('locale')) {
            $user->locale = $request->locale;
            session(['locale' => $request->locale]);
        }
        
        $user->save();
        
        return redirect()->route('profile.edit')->with('success', 'Profiel bijgewerkt.');
    }
}