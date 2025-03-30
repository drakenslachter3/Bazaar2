<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use PDF;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest');
    }

    // Standaard gebruiker registratie
    public function showRegisterForm()
    {
        return view('auth.register', ['userType' => 'regular']);
    }
    
    // Particuliere adverteerder registratie
    public function showPrivateAdvertiserRegisterForm()
    {
        return view('auth.register', ['userType' => 'private_advertiser']);
    }
    
    // Zakelijke adverteerder registratie
    public function showBusinessAdvertiserRegisterForm()
    {
        return view('auth.register', ['userType' => 'business_advertiser']);
    }

    protected function validator(array $data)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'user_type' => ['required', 'in:regular,private_advertiser,business_advertiser'],
        ];
        
        if ($data['user_type'] === 'business_advertiser') {
            $rules['business_name'] = ['required', 'string', 'max:255'];
            $rules['business_details'] = ['required', 'string'];
        }
        
        return Validator::make($data, $rules);
    }

    protected function create(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'user_type' => $data['user_type'],
            'business_name' => $data['business_name'] ?? null,
            'business_details' => $data['business_details'] ?? null,
        ]);
        
        if ($data['user_type'] === 'business_advertiser') {
            Business::create([
                'user_id' => $user->id,
                'name' => $data['business_name'],
            ]);
            
            // Genereer contract PDF
            $this->generateBusinessContract($user);
        }
        
        return $user;
    }
    
    protected function generateBusinessContract($user)
    {
        $pdf = PDF::loadView('pdfs.business_contract', [
            'user' => $user,
            'date' => now(),
        ]);
        
        $filename = 'contract_' . $user->id . '.pdf';
        $path = 'contracts/' . $filename;
        
        // Sla het contract op
        \Storage::put('public/' . $path, $pdf->output());
        
        // Update het bedrijf met het contract pad
        $user->business->update(['contract_path' => $path]);
        
        return $path;
    }
    
    // Custom registratie voor verschillende gebruikerstypen
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        $user = $this->create($request->all());
        
        $this->guard()->login($user);
        
        return $this->registered($request, $user)
            ?: redirect($this->redirectPath());
    }
}