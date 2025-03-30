<?php
namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BusinessController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('business.owner')->except(['show']);
    }
    
    public function show($customUrl)
    {
        $business = Business::where('custom_url', $customUrl)->firstOrFail();
        $components = $business->landingPageComponents()->orderBy('position')->get();
        
        return view('business.landing', compact('business', 'components'));
    }
    
    public function edit()
    {
        $user = Auth::user();
        $business = $user->business;
        
        return view('business.edit', compact('business'));
    }
    
    public function update(Request $request)
    {
        $user = Auth::user();
        $business = $user->business;
        
        $request->validate([
            'name' => 'required|string|max:255',
            'custom_url' => 'nullable|string|alpha_dash|max:255|unique:businesses,custom_url,' . $business->id,
            'logo' => 'nullable|image|max:2048',
        ]);
        
        $data = $request->only(['name', 'custom_url']);
        
        if ($request->hasFile('logo')) {
            // Verwijder oude logo
            if ($business->logo_path) {
                Storage::delete('public/' . $business->logo_path);
            }
            
            $path = $request->file('logo')->store('logos', 'public');
            $data['logo_path'] = $path;
        }
        
        // Verwerk thema instellingen
        if ($request->has('theme')) {
            $data['theme_settings'] = $request->theme;
        }
        
        $business->update($data);
        
        return redirect()->route('business.edit')
            ->with('success', 'Bedrijfsgegevens bijgewerkt.');
    }
    
    public function uploadContract(Request $request)
    {
        $request->validate([
            'contract' => 'required|file|mimes:pdf|max:10240',
        ]);
        
        $user = Auth::user();
        $business = $user->business;
        
        if ($business->contract_path) {
            Storage::delete('public/' . $business->contract_path);
        }
        
        $path = $request->file('contract')->store('contracts', 'public');
        $business->update(['contract_path' => $path]);
        
        return redirect()->route('business.edit')
            ->with('success', 'Contract succesvol geüpload.');
    }
}