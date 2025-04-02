<?php

namespace App\Http\Controllers;

use App\Models\Advertisement;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function __construct()
    {
    }
    
    public function index(Request $request)
    {
        $user = Auth::user();
        
        if ($request->type == 'received') {
            // Reviews die de gebruiker heeft ontvangen
            $reviews = Review::where('reviewed_user_id', $user->id)
                ->orWhereHas('advertisement', function($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->with(['reviewer', 'reviewedUser', 'advertisement'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        } else {
            // Reviews die de gebruiker heeft geschreven
            $reviews = Review::where('reviewer_id', $user->id)
                ->with(['reviewer', 'reviewedUser', 'advertisement'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        }
        
        return view('reviews.index', compact('reviews'));
    }
    
    public function createProductReview(Advertisement $advertisement)
    {
        // Controleer of de gebruiker het product heeft gehuurd of gekocht
        $user = Auth::user();

        $hasReviewed = Review::where('reviewer_id', $user->id)
            ->where('advertisement_id', $advertisement->id)
            ->exists();
            
        if ($hasReviewed) {
            return redirect()->back()->with('error', 'Je hebt al een review achtergelaten voor dit product.');
        }
        
        return view('reviews.create', [
            'advertisement' => $advertisement,
            'type' => 'product',
        ]);
    }
    
    public function createUserReview(User $user)
    {
        // Controleer of de ingelogde gebruiker producten heeft gehuurd van deze gebruiker
        $currentUser = Auth::user();
        $hasInteracted = $currentUser->rentedProducts()
            ->whereHas('advertisement', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->where('status', 'completed')
            ->exists();
            
        if (!$hasInteracted) {
            return redirect()->back()->with('error', 'Je kunt alleen gebruikers beoordelen waarmee je zaken hebt gedaan.');
        }
        
        // Controleer of de gebruiker al een review heeft achtergelaten
        $hasReviewed = Review::where('reviewer_id', $currentUser->id)
            ->where('reviewed_user_id', $user->id)
            ->exists();
            
        if ($hasReviewed) {
            return redirect()->back()->with('error', 'Je hebt al een review achtergelaten voor deze gebruiker.');
        }
        
        return view('reviews.create', [
            'reviewedUser' => $user,
            'type' => 'user',
        ]);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'rating' => 'required|integer|between:1,5',
            'comment' => 'required|string|min:10',
            'type' => 'required|in:product,user',
            'advertisement_id' => 'required_if:type,product|exists:advertisements,id',
            'user_id' => 'required_if:type,user|exists:users,id',
        ]);
        
        $review = new Review();
        $review->reviewer_id = Auth::id();
        $review->rating = $request->rating;
        $review->comment = $request->comment;
        
        if ($request->type === 'product') {
            $review->advertisement_id = $request->advertisement_id;
        } else {
            $review->reviewed_user_id = $request->user_id;
        }
        
        $review->save();
        
        return redirect()->route('reviews.index')->with('success', 'Review succesvol geplaatst.');
    }
    
    public function show(Review $review)
    {
        return view('reviews.show', compact('review'));
    }
    
    public function edit(Review $review)
    {
        $this->authorize('update', $review);
        
        $type = $review->advertisement_id ? 'product' : 'user';
        
        return view('reviews.edit', [
            'review' => $review,
            'type' => $type,
            'advertisement' => $review->advertisement,
            'reviewedUser' => $review->reviewedUser,
        ]);
    }
    
    public function update(Request $request, Review $review)
    {
        $this->authorize('update', $review);
        
        $request->validate([
            'rating' => 'required|integer|between:1,5',
            'comment' => 'required|string|min:10',
        ]);
        
        $review->update([
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);
        
        return redirect()->route('reviews.index')->with('success', 'Review bijgewerkt.');
    }
    
    public function destroy(Review $review)
    {
        $this->authorize('delete', $review);
        
        $review->delete();
        
        return redirect()->route('reviews.index')->with('success', 'Review verwijderd.');
    }
}