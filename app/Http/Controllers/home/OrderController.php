<?php

namespace App\Http\Controllers\home;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use App\Services\PapiPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function __construct(protected PapiPaymentService $papi) {}

    /* ================================================================
       CHECKOUT — POST /checkout
       Appelé quand le client clique "Passer la commande"
       ================================================================ */
    public function checkout(Request $request)
    {
        $user = auth()->user();

        // 1. Récupérer le panier avec les produits
        $cartItems = CartItem::with('product')
            ->where('user_id', $user->id)
            ->get();

        // 2. Panier vide ?
        if ($cartItems->isEmpty()) {
            return back()->with('error', 'Votre panier est vide.');
        }

        // 3. Vérifier le stock pour chaque article
        foreach ($cartItems as $item) {
            if ($item->product->stock < $item->quantity) {
                return back()->with(
                    'error',
                    "Stock insuffisant pour « {$item->product->name} ». " .
                        "Il ne reste que {$item->product->stock} exemplaire(s)."
                );
            }
        }

        /*
         * 4. CALCUL DU PRIX
         * ─────────────────
         * On utilise price_numeric (le champ numérique pur, ex: 150000)
         * et NON pas price (qui est formaté pour affichage, ex: "150 000 Ar")
         *
         * product.price         → "150 000 Ar"   ← pour affichage seulement
         * product.price_numeric → 150000          ← pour les calculs
         *
         * subtotal d'un item = quantity × price_numeric
         * total = somme de tous les subtotals
         */
        $total = $cartItems->sum(
            fn($item) => $item->quantity * $item->product->price
        );

        // 5. Générer une référence unique
        $reference = 'CASAINIERS-' . strtoupper(Str::random(8));

        // 6. Créer la commande en DB
        //    On fait un SNAPSHOT des produits → si le prix change demain,
        //    on garde quand même le prix au moment de la commande
        $order = Order::create([
            'user_id'   => $user->id,
            'reference' => $reference,
            'amount'    => (int) $total,   // entier, pas de décimales pour MGA
            'status'    => 'pending',
            // ← C'EST ICI, dans le create() de la commande
            'items' => $cartItems->map(fn($item) => [
                'product_id' => $item->product_id,
                'name'       => $item->product->name,
                'brand'      => $item->product->brand,
                'image'      => $item->product->image,
                'quantity'   => $item->quantity,
                'unit_price' => $item->product->price,
                'subtotal'   => $item->quantity * $item->product->price,
            ])->toArray(),
        ]);

        // 7. Appel API Papi pour générer le lien de paiement
        try {
            $papiResponse = $this->papi->createPaymentLink([
                'amount'      => $total,
                'client_name' => $user->name,
                'reference'   => $reference,
                'email'       => $user->email,
            ]);

            // Papi retourne : { data: { paymentLink: "https://...", notificationToken: "xxx", ... } }
            $paymentLink       = $papiResponse['data']['paymentLink']       ?? null;
            $notificationToken = $papiResponse['data']['notificationToken'] ?? null;

            if (!$paymentLink) {
                throw new \Exception('URL de paiement non reçue de Papi.');
            }

            // 8. Sauvegarder le lien et le token dans la commande
            $order->update([
                'papi_payment_url' => $paymentLink,
                'papi_notif_token' => $notificationToken,
            ]);

            // 9. Rediriger le client vers la page de paiement Papi
            //    Inertia::location() = vraie redirection HTTP (sort de la SPA)
            return Inertia::location($paymentLink);
        } catch (\Exception $e) {
            // En cas d'erreur, marquer la commande comme failed
            $order->update(['status' => 'failed']);
            Log::error('Papi checkout error: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de la création du paiement : ' . $e->getMessage());
        }
    }

    /* ================================================================
       SUCCESS — GET /commande/success?ref=CASAINIERS-XXXXXXXX
       Papi redirige le client ici après un paiement réussi
       ================================================================ */
    public function success(Request $request)
    {
        $order = Order::where('reference', $request->ref)
            ->where('user_id', auth()->id())
            ->first();

        return Inertia::render('home/OrderSuccess', [
            'order' => $order ? [
                'reference'       => $order->reference,
                'amount'          => $order->amount,
                'status'          => $order->status,
                'items'           => $order->items,
                'total_items'     => $order->total_items,
            ] : null,
        ]);
    }

    /* ================================================================
       FAILURE — GET /commande/failure?ref=CASAINIERS-XXXXXXXX
       Papi redirige le client ici après un paiement échoué
       ================================================================ */
    public function failure(Request $request)
    {
        $order = Order::where('reference', $request->ref)
            ->where('user_id', auth()->id())
            ->first();

        return Inertia::render('home/OrderFailure', [
            'order' => $order ? [
                'reference' => $order->reference,
                'amount'    => $order->amount,
                'status'    => $order->status,
            ] : null,
        ]);
    }

    /* ================================================================
       CALLBACK (WEBHOOK) — POST /papi/callback
       Appelé par les serveurs Papi (pas par le client !)
       C'est ici que le paiement est officiellement confirmé.
       ================================================================ */
    public function callback(Request $request)
    {
        $data = $request->all();
        Log::info('Papi callback reçu:', $data);

        $paymentReference  = $data['merchantPaymentReference'] ?? null;
        $notificationToken = $data['notificationToken'] ?? null;
        $paymentStatus     = $data['paymentStatus'] ?? null;

        if (!$paymentReference) {
            return response()->json(['error' => 'Missing merchantPaymentReference'], 400);
        }

        $order = Order::where('reference', $paymentReference)->first();

        if (!$order) {
            Log::warning('Commande introuvable pour ' . $paymentReference);
            return response()->json(['error' => 'Order not found'], 404);
        }

        if ($order->papi_notif_token !== $notificationToken) {
            Log::warning('Token invalide pour ' . $paymentReference);
            return response()->json(['error' => 'Invalid token'], 403);
        }

        match (strtoupper($paymentStatus)) {
            'SUCCESS' => $this->handleSuccess($order),
            'FAILED'  => $order->update(['status' => 'failed']),
            default   => null,
        };

        return response()->json(['received' => true], 200);
    }


    /* ================================================================
       HANDLE SUCCESS (méthode privée)
       ================================================================ */
    private function handleSuccess(Order $order): void
    {
        // Idempotence : si déjà payé, ne pas traiter deux fois
        if ($order->status === 'paid') {
            Log::info('Papi callback: commande déjà marquée paid → ' . $order->reference);
            return;
        }

        // 1. Marquer la commande comme payée
        $order->update(['status' => 'paid']);

        // 2. Décrémenter le stock pour chaque produit commandé
        //    On utilise les données du snapshot JSON (items)
        foreach ($order->items as $item) {
            Product::where('id', $item['product_id'])
                ->decrement('stock', $item['quantity']);
        }

        // 3. Vider le panier du client
        CartItem::where('user_id', $order->user_id)->delete();

        Log::info('Commande payée avec succès : ' . $order->reference);
    }
}
