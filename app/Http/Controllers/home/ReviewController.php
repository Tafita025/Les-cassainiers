<?php

namespace App\Http\Controllers\home;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ReviewController extends Controller
{
    /**
     * Afficher tous les produits avec leurs notes
     */
    public function review(Request $request)
    {
        $products = Product::where('is_active', true)
            ->withCount(['reviews' => fn ($q) => $q->where('is_approved', true)])
            ->withAvg(['reviews' => fn ($q) => $q->where('is_approved', true)], 'rating')
            ->having('reviews_count', '>', 0)
            ->orderByDesc('reviews_avg_rating')
            ->get()
            ->map(fn ($p) => [
                'id'      => $p->id,
                'name'    => $p->name,
                'slug'    => $p->slug,
                'brand'   => $p->brand,
                'image'   => $p->image,
                'price'   => number_format($p->price, 0, ',', ' ') . ' Ar',
                'rating'  => (int) round($p->reviews_avg_rating ?? 0),
                'reviews' => $p->reviews_count,
            ]);

        return Inertia::render('home/Review', [
            'products' => $products,
        ]);
    }

    /**
     * Charger les avis d'un produit (JSON)
     */
    public function show(Product $product)
    {
        $reviews = $product->reviews()
            ->where('is_approved', true)
            ->with('user:id,name')
            ->latest()
            ->get()
            ->map(fn ($r) => [
                'id'            => $r->id,
                'rating'        => $r->rating,
                'comment'       => $r->comment,
                'user_name'     => $r->user->name,
                'user_initials' => collect(explode(' ', $r->user->name))
                    ->filter()->map(fn ($w) => strtoupper($w[0]))->take(2)->join(''),
                'date'          => $r->created_at->diffForHumans(),
            ]);

        $distribution = [];
        for ($i = 5; $i >= 1; $i--) {
            $distribution[$i] = $product->reviews()
                ->where('is_approved', true)
                ->where('rating', $i)
                ->count();
        }

        $total = $product->reviews()->where('is_approved', true)->count();
        $avg = $total > 0
            ? round($product->reviews()->where('is_approved', true)->avg('rating'), 1)
            : 0;

        $userReview = null;
        if (Auth::check()) {
            $existing = $product->reviews()->where('user_id', Auth::id())->first();
            if ($existing) {
                $userReview = [
                    'rating'  => $existing->rating,
                    'comment' => $existing->comment,
                ];
            }
        }

        return response()->json([
            'reviews'      => $reviews,
            'distribution' => $distribution,
            'average'      => $avg,
            'total'        => $total,
            'userReview'   => $userReview,
        ]);
    }

    /**
     * Soumettre un avis (protégé par auth.modal middleware)
     */
    public function store(Request $request, Product $product)
    {
        $validated = $request->validate([
            'rating'  => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ], [
            'rating.required' => 'Veuillez sélectionner une note.',
            'rating.min'      => 'La note minimum est 1 étoile.',
            'rating.max'      => 'La note maximum est 5 étoiles.',
            'comment.max'     => 'Le commentaire ne peut pas dépasser 1000 caractères.',
        ]);

        Review::updateOrCreate(
            [
                'product_id' => $product->id,
                'user_id'    => Auth::id(),
            ],
            [
                'rating'      => $validated['rating'],
                'comment'     => $validated['comment'],
                'is_approved' => true,
            ]
        );

        return back()->with('success', 'Votre avis a été enregistré.');
    }
}