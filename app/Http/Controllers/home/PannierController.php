<?php

namespace App\Http\Controllers\home;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PannierController extends Controller
{
    /**
     * Afficher le panier
     */
    public function pannier()
    {
        $cartItems = CartItem::with('product')
            ->where('user_id', auth()->id())
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
                        'image' => $item->product->image,
                        'stock' => $item->product->stock,
                    ],
                    'subtotal' => $item->quantity * $item->product->price,
                ];
            });

        $total = $cartItems->sum('subtotal');

        return Inertia::render('home/Pannier', [
            'cartItems' => $cartItems,
            'total' => $total,
        ]);
    }

    /**
     * Ajouter un produit au panier
     */
    public function add(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:10',
        ]);

        // Vérifier le stock
        if ($product->stock < $request->quantity) {
            return back()->with('error', 'Stock insuffisant');
        }

        $cartItem = CartItem::where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->first();

        if ($cartItem) {
            // Mettre à jour la quantité
            $newQuantity = $cartItem->quantity + $request->quantity;
            
            if ($newQuantity > $product->stock) {
                return back()->with('error', 'Stock insuffisant');
            }
            
            if ($newQuantity > 10) {
                return back()->with('error', 'Quantité maximale atteinte (10 par produit)');
            }

            $cartItem->update(['quantity' => $newQuantity]);
        } else {
            // Créer un nouvel item
            CartItem::create([
                'user_id' => auth()->id(),
                'product_id' => $product->id,
                'quantity' => $request->quantity,
            ]);
        }

        return back()->with('success', 'Produit ajouté au panier');
    }

    /**
     * Mettre à jour la quantité d'un item
     */
    public function updateQuantity(Request $request, CartItem $cartItem)
    {
        // Vérifier que l'item appartient à l'utilisateur
        if ($cartItem->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'quantity' => 'required|integer|min:1|max:10',
        ]);

        // Vérifier le stock
        if ($cartItem->product->stock < $request->quantity) {
            return back()->with('error', 'Stock insuffisant');
        }

        $cartItem->update(['quantity' => $request->quantity]);

        return back()->with('success', 'Quantité mise à jour');
    }

    /**
     * Supprimer un item du panier
     */
    public function remove(CartItem $cartItem)
    {
        // Vérifier que l'item appartient à l'utilisateur
        if ($cartItem->user_id !== auth()->id()) {
            abort(403);
        }

        $cartItem->delete();

        return back()->with('success', 'Produit retiré du panier');
    }

    /**
     * Vider le panier
     */
    public function clear()
    {
        CartItem::where('user_id', auth()->id())->delete();

        return back()->with('success', 'Panier vidé');
    }
}