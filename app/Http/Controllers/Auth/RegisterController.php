<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Business;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    // In app/Http/Controllers/Auth/RegisterController.php

protected function create(array $data)
{
    // Controleer of userType expliciet is meegegeven in het form
    $userType = $data['user_type'] ?? 'regular';
    
    // Als dit niet een geldige waarde is, gebruik dan 'regular'
    if (!in_array($userType, ['regular', 'private_advertiser', 'business_advertiser'])) {
        $userType = 'regular';
    }
    
    // Log de waarde om te controleren
    \Log::info('Registering user with type: ' . $userType);
    
    $user = User::create([
        'name' => $data['name'],
        'email' => $data['email'],
        'password' => Hash::make($data['password']),
        'user_type' => $userType,
        'business_name' => $data['business_name'] ?? null,
        'business_details' => $data['business_details'] ?? null,
    ]);
    
    if ($userType === 'business_advertiser') {
        // Maak business record aan indien nodig
        if (!empty($data['business_name'])) {
            Business::create([
                'user_id' => $user->id,
                'name' => $data['business_name'],
            ]);
        }
    }
    
    return $user;
}

    /**
 * Show the application registration form for private advertisers.
 *
 * @return \Illuminate\View\View
 */
public function showPrivateAdvertiserRegisterForm()
{
    return view('auth.register', ['userType' => 'private_advertiser']);
}

/**
 * Show the application registration form for business advertisers.
 *
 * @return \Illuminate\View\View
 */
public function showBusinessAdvertiserRegisterForm()
{
    return view('auth.register', ['userType' => 'business_advertiser']);
}
}
