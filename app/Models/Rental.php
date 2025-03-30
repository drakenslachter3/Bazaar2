<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rental extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'advertisement_id',
        'renter_id',
        'start_date',
        'end_date',
        'status', // 'pending', 'active', 'completed', 'cancelled'
    ];
    
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];
    
    public function advertisement()
    {
        return $this->belongsTo(Advertisement::class);
    }
    
    public function renter()
    {
        return $this->belongsTo(User::class, 'renter_id');
    }
}