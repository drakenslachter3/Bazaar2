<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\LandingPageComponent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LandingPageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('business.owner');
    }
    
    public function index()
    {
        $user = Auth::user();
        $business = $user->business;
        $components = $business->landingPageComponents()->orderBy('position')->get();
        
        return view('business.landing.index', compact('business', 'components'));
    }
    
    public function create()
    {
        return view('business.landing.create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|string|in:featured_ads,text,image,hero,cta',
            'content' => 'nullable|string',
            'position' => 'required|integer|min:0',
            'image' => 'required_if:type,image|nullable|image|max:2048',
        ]);
        
        $user = Auth::user();
        $business = $user->business;
        
        $data = $request->only(['type', 'content', 'position']);
        
        // Verwerk instellingen
        $settings = [];
        
        if ($request->type === 'featured_ads') {
            $settings['count'] = $request->input('settings.count', 3);
            $settings['order_by'] = $request->input('settings.order_by', 'created_at');
            $settings['order'] = $request->input('settings.order', 'desc');
        } else if ($request->type === 'text') {
            $settings['title'] = $request->input('settings.title');
            $settings['align'] = $request->input('settings.align', 'left');
        } else if ($request->type === 'image' || $request->type === 'hero') {
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('landing', 'public');
                $settings['image_path'] = $path;
            }
            $settings['alt_text'] = $request->input('settings.alt_text', '');
        } else if ($request->type === 'cta') {
            $settings['button_text'] = $request->input('settings.button_text', 'Meer Info');
            $settings['button_url'] = $request->input('settings.button_url', '#');
            $settings['button_color'] = $request->input('settings.button_color', 'primary');
        }
        
        $data['settings'] = $settings;
        $data['business_id'] = $business->id;
        
        LandingPageComponent::create($data);
        
        return redirect()->route('business.landing.index')
            ->with('success', 'Component toegevoegd aan landingspagina.');
    }
    
    public function edit(LandingPageComponent $component)
    {
        $this->authorize('update', $component);
        
        return view('business.landing.edit', compact('component'));
    }
    
    public function update(Request $request, LandingPageComponent $component)
    {
        $this->authorize('update', $component);
        
        $request->validate([
            'content' => 'nullable|string',
            'position' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048',
        ]);
        
        $data = $request->only(['content', 'position']);
        
        // Verwerk instellingen
        $settings = $component->settings ?? [];
        
        if ($component->type === 'featured_ads') {
            $settings['count'] = $request->input('settings.count', 3);
            $settings['order_by'] = $request->input('settings.order_by', 'created_at');
            $settings['order'] = $request->input('settings.order', 'desc');
        } else if ($component->type === 'text') {
            $settings['title'] = $request->input('settings.title');
            $settings['align'] = $request->input('settings.align', 'left');
        } else if ($component->type === 'image' || $component->type === 'hero') {
            if ($request->hasFile('image')) {
                // Verwijder oude afbeelding
                if (isset($settings['image_path'])) {
                    Storage::delete('public/' . $settings['image_path']);
                }
                
                $path = $request->file('image')->store('landing', 'public');
                $settings['image_path'] = $path;
            }
            $settings['alt_text'] = $request->input('settings.alt_text', '');
        } else if ($component->type === 'cta') {
            $settings['button_text'] = $request->input('settings.button_text', 'Meer Info');
            $settings['button_url'] = $request->input('settings.button_url', '#');
            $settings['button_color'] = $request->input('settings.button_color', 'primary');
        }
        
        $data['settings'] = $settings;
        
        $component->update($data);
        
        return redirect()->route('business.landing.index')
            ->with('success', 'Component bijgewerkt.');
    }
    
    public function destroy(LandingPageComponent $component)
    {
        $this->authorize('delete', $component);
        
        // Verwijder eventuele afbeeldingen
        if ($component->type === 'image' || $component->type === 'hero') {
            $settings = $component->settings;
            if (isset($settings['image_path'])) {
                Storage::delete('public/' . $settings['image_path']);
            }
        }
        
        $component->delete();
        
        return redirect()->route('business.landing.index')
            ->with('success', 'Component verwijderd.');
    }
    
    public function reorder(Request $request)
    {
        $request->validate([
            'components' => 'required|array',
            'components.*' => 'required|integer|exists:landing_page_components,id',
        ]);
        
        $user = Auth::user();
        $business = $user->business;
        
        $positions = $request->components;
        
        foreach ($positions as $position => $componentId) {
            $component = LandingPageComponent::find($componentId);
            
            if ($component->business_id === $business->id) {
                $component->update(['position' => $position]);
            }
        }
        
        return response()->json(['success' => true]);
    }
}