<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'advertisement_id',
        'user_id',
        'purchase_date',
        'amount',
        'status',
    ];
    
    protected $casts = [
        'purchase_date' => 'datetime',
    ];
    
    public function advertisement()
    {
        return $this->belongsTo(Advertisement::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}