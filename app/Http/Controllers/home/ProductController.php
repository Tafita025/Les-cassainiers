<?php

namespace App\Http\Controllers\home;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function product(Request $request)
    {
        $query = Product::query()
            ->where('is_active', true)
            ->with(['category'])
            ->withCount(['reviews' => fn ($q) => $q->where('is_approved', true)])
            ->withAvg(['reviews' => fn ($q) => $q->where('is_approved', true)], 'rating');

        // Filtre par catégorie (depuis ?categorie=slug)
        if ($request->filled('categorie')) {
            $query->whereHas('category', fn ($q) =>
                $q->where('slug', $request->categorie)
            );
        }

        $products = $query->latest()->get()->map(fn ($p) => [
            'id'          => $p->id,
            'brand'       => $p->brand,
            'name'        => $p->name,
            'slug'        => $p->slug,
            'description' => $p->description,
            'price'       => number_format($p->price, 0, ',', ' ') . ' Ar',
            'raw_price'   => $p->price,
            'rating'      => (int) round($p->reviews_avg_rating ?? 0),
            'reviews'     => $p->reviews_count,
            'image'       => $p->image,
            'category'    => $p->category->name,
            'stock'       => $p->stock,
        ]);

        return Inertia::render('home/Product', [
            'products'        => $products,
            'currentCategory' => $request->categorie,
        ]);
    }
}