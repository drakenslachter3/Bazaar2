<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Advertisement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'price',
        'type', // 'sale', 'rent'
        'user_id',
        'business_id',
        'active',
        'expiry_date',
        'qr_code_path',
    ];
    
    protected $casts = [
        'expiry_date' => 'datetime',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function business()
    {
        return $this->belongsTo(Business::class);
    }
    
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
    
    public function rentalPeriods()
    {
        return $this->hasMany(RentalPeriod::class);
    }
    
    public function favorites()
    {
        return $this->belongsToMany(User::class, 'favorites');
    }
}