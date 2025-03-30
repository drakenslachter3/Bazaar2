<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'reviewer_id',
        'reviewed_user_id',
        'advertisement_id',
        'rating',
        'comment',
    ];
    
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
    
    public function reviewedUser()
    {
        return $this->belongsTo(User::class, 'reviewed_user_id');
    }
    
    public function advertisement()
    {
        return $this->belongsTo(Advertisement::class);
    }
}