<?php

namespace App\Http\Middleware;

use App\Models\CartItem;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $request->user()
                    ? $request->user()->only('id', 'name', 'email')
                    : null,
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            // Catégories pour le sidebar (toutes les pages)
            'categories' => Category::withCount(['products' => fn($q) => $q->where('is_active', true)])
                ->orderBy('name')
                ->get()
                ->map(fn($c) => [
                    'id'             => $c->id,
                    'name'           => $c->name,
                    'slug'           => $c->slug,
                    'products_count' => $c->products_count,
                ]),
            // Partager les données du panier
            'cart' => $request->user() ? [
                'count' => CartItem::where('user_id', $request->user()->id)->sum('quantity'),
                'items' => CartItem::with('product')
                    ->where('user_id', $request->user()->id)
                    ->get()
                    ->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'quantity' => $item->quantity,
                            'product' => [
                                'id' => $item->product->id,
                                'name' => $item->product->name,
                                'brand' => $item->product->brand,
                                'price' => $item->product->price,
                                'price_numeric' => $item->product->price_numeric,
                                'image' => $item->product->image,
                            ],
                        ];
                    }),
            ] : [
                'count' => 0,
                'items' => [],
            ],

            // Flash global
            'flash' => [
                'authRequired' => fn() => $request->session()->get('authRequired', false),
                'success'      => fn() => $request->session()->get('success'),
                'error'        => fn() => $request->session()->get('error'),
            ],
        ];
    }
}
