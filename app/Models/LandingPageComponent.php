<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingPageComponent extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'business_id',
        'type', // 'featured_ads', 'text', 'image', etc.
        'content',
        'position',
        'settings',
    ];
    
    protected $casts = [
        'settings' => 'array',
    ];
    
    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}