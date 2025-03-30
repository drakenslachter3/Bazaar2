<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\User;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }
    
    public function index()
    {
        $pendingContracts = User::where('user_type', 'business_advertiser')
            ->where('contract_approved', false)
            ->get();
        
        return view('admin.contracts.index', compact('pendingContracts'));
    }
    
    public function show($id)
    {
        $user = User::findOrFail($id);
        $business = $user->business;
        
        return view('admin.contracts.show', compact('user', 'business'));
    }
    
    public function approve($id)
    {
        $user = User::findOrFail($id);
        $user->update(['contract_approved' => true]);
        
        return redirect()->route('admin.contracts.index')
            ->with('success', 'Contract goedgekeurd!');
    }
    
    public function reject($id)
    {
        $user = User::findOrFail($id);
        $user->update(['contract_approved' => false]);
        
        return redirect()->route('admin.contracts.index')
            ->with('success', 'Contract afgekeurd.');
    }
    
    public function upload(Request $request, $id)
    {
        $request->validate([
            'contract' => 'required|file|mimes:pdf|max:10240',
        ]);
        
        $user = User::findOrFail($id);
        $business = $user->business;
        
        $file = $request->file('contract');
        $path = $file->store('contracts', 'public');
        
        $business->update(['contract_path' => $path]);
        
        return redirect()->back()->with('success', 'Contract succesvol geüpload.');
    }
}