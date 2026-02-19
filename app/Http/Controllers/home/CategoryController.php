<?php

namespace App\Http\Controllers\home;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function category()
    {
        $categories = Category::withCount(['products' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('name')
            ->get()
            ->map(fn ($c) => [
                'id'          => $c->id,
                'name'        => $c->name,
                'slug'        => $c->slug,
                'description' => $c->description,
                'image'       => $c->image,
                'count'       => $c->products_count,
            ]);

        return Inertia::render('home/Category', [
            'categories' => $categories,
        ]);
    }
}