<?php

namespace App\Http\Controllers\home;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function home()
    {
        // Catégories avec nombre de produits actifs
        $categories = Category::withCount(['products' => fn ($q) => $q->where('is_active', true)])
            ->get()
            ->map(fn ($cat) => [
                'id'             => $cat->id,
                'name'           => $cat->name,
                'slug'           => $cat->slug,
                'description'    => $cat->description,
                'products_count' => $cat->products_count,
            ]);

        // Produits tendance (les mieux notés, actifs, max 4)
        $trending = Product::where('is_active', true)
            ->with(['category'])
            ->withCount(['reviews' => fn ($q) => $q->where('is_approved', true)])
            ->withAvg(['reviews' => fn ($q) => $q->where('is_approved', true)], 'rating')
            ->orderByDesc('reviews_avg_rating')
            ->orderByDesc('reviews_count')
            ->take(4)
            ->get()
            ->map(fn ($p) => [
                'id'       => $p->id,
                'brand'    => $p->brand,
                'name'     => $p->name,
                'slug'     => $p->slug,
                'price'    => number_format($p->price, 0, ',', ' ') . ' Ar',
                'rating'   => (int) round($p->reviews_avg_rating ?? 0),
                'reviews'  => $p->reviews_count,
                'image'    => $p->image,
                'category' => $p->category->slug,
            ]);

        // Stats globales
        $stats = [
            'products'  => Product::where('is_active', true)->count(),
            'customers' => User::count(),
        ];

        return Inertia::render('home/Index', [
            'categories' => $categories,
            'trending'   => $trending,
            'stats'      => $stats,
        ]);
    }
}