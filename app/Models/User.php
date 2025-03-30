<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'user_type', // 'regular', 'private_advertiser', 'business_advertiser'
        'business_name',
        'business_details',
        'contract_approved',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    
    public function advertisements()
    {
        return $this->hasMany(Advertisement::class);
    }
    
    public function favorites()
    {
        return $this->belongsToMany(Advertisement::class, 'favorites');
    }
    
    public function purchasedProducts()
    {
        return $this->hasMany(Purchase::class);
    }
    
    public function rentedProducts()
    {
        return $this->hasMany(Rental::class, 'renter_id');
    }
    
    public function reviews()
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }
    
    public function receivedReviews()
    {
        return $this->hasMany(Review::class, 'reviewed_user_id');
    }
}