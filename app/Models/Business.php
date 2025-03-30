<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'user_id',
        'custom_url',
        'theme_settings',
        'logo_path',
        'contract_path',
    ];
    
    protected $casts = [
        'theme_settings' => 'array',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function advertisements()
    {
        return $this->hasMany(Advertisement::class);
    }
    
    public function landingPageComponents()
    {
        return $this->hasMany(LandingPageComponent::class);
    }
}