<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            // ── Qui a commandé ──────────────────────────────────
            $table->foreignId('user_id')
                  ->constrained()
                  ->cascadeOnDelete();

            // ── Référence unique (ex: CASAINIERS-AB12CD34) ──────
            $table->string('reference')->unique();

            // ── Montant BRUT en Ariary (entier, pas de décimales)
            // Exemple : 150000 = 150 000 Ar
            $table->unsignedBigInteger('amount');

            // ── Statut de la commande ────────────────────────────
            // pending  → en attente du paiement
            // paid     → paiement confirmé par Papi
            // failed   → paiement échoué
            // cancelled→ annulé manuellement
            $table->enum('status', ['pending', 'paid', 'failed', 'cancelled'])
                  ->default('pending');

            // ── Snapshot du panier au moment de la commande ─────
            // On stocke les produits commandés en JSON pour garder
            // une trace même si les produits changent de prix plus tard.
            // Structure : [
            //   { product_id, name, brand, image, quantity, unit_price, subtotal }
            // ]
            $table->json('items');

            // ── Papi.mg ──────────────────────────────────────────
            $table->string('papi_payment_url')->nullable();   // lien vers la page de paiement Papi
            $table->string('papi_notif_token')->nullable();   // token pour vérifier le webhook

            // ── Infos livraison (optionnel pour plus tard) ───────
            $table->string('shipping_address')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};