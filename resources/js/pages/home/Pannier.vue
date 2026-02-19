<script setup>
import { ref, computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import HomeLayout from '@/layouts/home/HomeLayout.vue';

const props = defineProps({
    cartItems: { type: Array, default: () => [] },
    total:     { type: Number, default: 0 },
});

const processing = ref(false);

/* ── Taux de change ─────────────────────────────────────────── */
const EUR_RATE = 5179;
function formatMga(amount) {
    return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'MGA' }).format(amount);
}
function formatEur(mga) {
    return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(mga / EUR_RATE);
}
const formattedTotal    = computed(() => formatMga(props.total));
const formattedTotalEur = computed(() => formatEur(props.total));

/* ── Actions panier ─────────────────────────────────────────── */
function updateQuantity(cartItemId, newQuantity) {
    if (newQuantity < 1 || newQuantity > 10) return;
    processing.value = true;
    router.patch(route('home.pannier.update', cartItemId), { quantity: newQuantity }, {
        preserveScroll: true,
        onFinish: () => (processing.value = false),
    });
}

function removeItem(cartItemId) {
    if (!confirm('Voulez-vous vraiment retirer ce produit du panier ?')) return;
    processing.value = true;
    router.delete(route('home.pannier.remove', cartItemId), {
        preserveScroll: true,
        onFinish: () => (processing.value = false),
    });
}

function clearCart() {
    if (!confirm('Voulez-vous vraiment vider le panier ?')) return;
    processing.value = true;
    router.delete(route('home.pannier.clear'), {
        onFinish: () => (processing.value = false),
    });
}

/* ── CHECKOUT ────────────────────────────────────────────────
   POST /checkout → OrderController@checkout
   Laravel crée la commande, appelle Papi, et renvoie une
   redirection vers la page de paiement Papi.
   Inertia::location() fait une vraie redirection HTTP donc
   le navigateur quitte la SPA → pas de onSuccess ici.
   ──────────────────────────────────────────────────────────── */
function checkout() {
    processing.value = true;
    router.post(route('home.checkout'), {}, {
        // onFinish s'exécute si la requête échoue (ex: stock insuffisant)
        // En cas de succès, Inertia::location() redirige avant que onFinish soit appelé
        onFinish: () => (processing.value = false),
    });
}
</script>

<template>
    <HomeLayout page-title="Panier">
        <main class="main-content">

            <!-- En-tête -->
            <section class="section">
                <div class="section__header">
                    <div>
                        <h1 class="hero__title" style="font-size: 1.8rem; margin-bottom: 8px">
                            Mon <span class="text-gradient-cyber">Panier</span>
                        </h1>
                        <p class="font-ui text-sm text-[#8b85a6]">
                            {{ cartItems.length }} article{{ cartItems.length > 1 ? 's' : '' }} dans votre panier
                        </p>
                    </div>
                    <button v-if="cartItems.length > 0" @click="clearCart" :disabled="processing"
                        class="btn-cyber btn-secondary" style="padding: 8px 16px; font-size: 0.78rem">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                        </svg>
                        Vider le panier
                    </button>
                </div>
            </section>

            <!-- Panier vide -->
            <section v-if="cartItems.length === 0" class="section">
                <div class="empty-cart">
                    <div class="empty-cart__icon-wrap">
                        <svg class="h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                        </svg>
                    </div>
                    <h3 class="font-ui text-lg font-semibold text-[#e8e6f0]">Votre panier est vide</h3>
                    <p class="font-ui text-sm text-[#8b85a6]">Découvrez nos produits et ajoutez-les à votre panier</p>
                    <Link :href="route('home.product')" class="btn-cyber btn-primary" style="margin-top: 20px; padding: 10px 24px">
                        Voir les produits
                    </Link>
                </div>
            </section>

            <!-- Liste des articles -->
            <section v-else class="section">
                <div class="cart-layout">

                    <!-- Articles -->
                    <div class="cart-items">
                        <div v-for="item in cartItems" :key="item.id" class="cart-item">
                            <div class="cart-item__image">
                                <img :src="item.product.image" :alt="item.product.name" />
                            </div>

                            <div class="cart-item__info">
                                <p class="cart-item__brand">{{ item.product.brand }}</p>
                                <h3 class="cart-item__name">{{ item.product.name }}</h3>
                                <!-- price = version affichage (ex: "150 000 Ar") -->
                                <p class="cart-item__price">{{ item.product.price }}</p>
                            </div>

                            <div class="cart-item__actions">
                                <!-- Quantité -->
                                <div class="cart-item__qty">
                                    <button class="cart-item__qty-btn"
                                        @click="updateQuantity(item.id, item.quantity - 1)"
                                        :disabled="processing || item.quantity <= 1">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14" />
                                        </svg>
                                    </button>
                                    <span class="cart-item__qty-value">{{ item.quantity }}</span>
                                    <button class="cart-item__qty-btn"
                                        @click="updateQuantity(item.id, item.quantity + 1)"
                                        :disabled="processing || item.quantity >= 10">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                        </svg>
                                    </button>
                                </div>

                                <!-- Sous-total : subtotal vient du backend (quantity × price_numeric) -->
                                <div class="cart-item__subtotal">
                                    <div class="price-stack">
                                        <span class="price">{{ formatMga(item.subtotal) }}</span>
                                        <span class="eur-badge">≈ {{ formatEur(item.subtotal) }}</span>
                                    </div>
                                </div>

                                <!-- Supprimer -->
                                <button class="cart-item__remove" @click="removeItem(item.id)" :disabled="processing">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Résumé commande -->
                    <div class="cart-summary">
                        <h3 class="cart-summary__title">Résumé de la commande</h3>

                        <div class="cart-summary__details">
                            <div class="cart-summary__row">
                                <span>Sous-total</span>
                                <span>{{ formattedTotal }}</span>
                            </div>
                            <div class="cart-summary__row">
                                <span>Livraison</span>
                                <span class="text-[#39ff14]">Gratuite</span>
                            </div>
                            <div class="divider-glow" style="margin: 16px 0"></div>
                            <div class="cart-summary__row cart-summary__row--total">
                                <span>Total</span>
                                <div class="price-stack">
                                    <span class="price price--lg">{{ formattedTotal }}</span>
                                    <div class="eur-convert-badge">
                                        <svg class="h-3 w-3 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-11.25a.75.75 0 00-1.5 0v4.59L7.3 9.24a.75.75 0 00-1.1 1.02l3.25 3.5a.75.75 0 001.1 0l3.25-3.5a.75.75 0 10-1.1-1.02l-1.95 2.1V6.75z" clip-rule="evenodd" />
                                        </svg>
                                        <span>≈ {{ formattedTotalEur }}</span>
                                        <span class="eur-rate-hint">1€ = {{ EUR_RATE.toLocaleString('fr-FR') }} Ar</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <p class="rate-note">💱 Conversion indicative — taux moyen du marché (fév. 2026)</p>

                        <!-- ✅ BOUTON CHECKOUT BRANCHÉ SUR PAPI -->
                        <button
                            @click="checkout"
                            :disabled="processing"
                            class="btn-cyber btn-primary"
                            style="width: 100%; padding: 12px; font-size: 0.9rem; margin-top: 20px"
                        >
                            <svg v-if="!processing" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            <!-- Spinner pendant le traitement -->
                            <svg v-else class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            {{ processing ? 'Redirection vers le paiement...' : 'Passer la commande' }}
                        </button>

                        <Link :href="route('home.product')" class="cart-summary__continue">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                            </svg>
                            Continuer mes achats
                        </Link>
                    </div>
                </div>
            </section>

            <!-- Footer -->
            <footer class="page-footer">
                <div class="divider-glow" style="margin-bottom: 20px"></div>
                <div class="page-footer__row">
                    <div class="page-footer__brand">
                        <span class="page-footer__logo">Les Casainiers</span>
                        <span class="font-mono text-xs text-[#8b85a6]">&copy; 2026 — Antananarivo, Madagascar</span>
                    </div>
                    <div class="page-footer__links">
                        <Link :href="route('home.product')">Produits</Link>
                        <Link :href="route('home.offers')">Offres</Link>
                        <a href="#">Conditions</a>
                        <a href="#">Confidentialité</a>
                    </div>
                </div>
            </footer>

        </main>
    </HomeLayout>
</template>

<style scoped>
.empty-cart { display: flex; flex-direction: column; align-items: center; gap: 16px; padding: 80px 20px; text-align: center; }
.empty-cart__icon-wrap { display: flex; align-items: center; justify-content: center; width: 96px; height: 96px; border-radius: 50%; background: rgba(0, 240, 255, 0.04); border: 1px solid rgba(0, 240, 255, 0.1); color: #8b85a6; opacity: 0.6; }
.cart-layout { display: grid; grid-template-columns: 1fr; gap: 24px; }
@media (min-width: 1024px) { .cart-layout { grid-template-columns: 1fr 380px; } }
.cart-items { display: flex; flex-direction: column; gap: 16px; }
.cart-item { display: grid; grid-template-columns: 100px 1fr auto; gap: 16px; padding: 16px; background: var(--clr-surface-light, #1a1530); border: 1px solid var(--clr-border, rgba(255, 255, 255, 0.06)); border-radius: 12px; transition: all 200ms ease; }
.cart-item:hover { border-color: rgba(0, 240, 255, 0.2); box-shadow: 0 0 24px rgba(0, 240, 255, 0.06); transform: translateY(-1px); }
.cart-item__image { display: flex; align-items: center; justify-content: center; background: rgba(0, 240, 255, 0.03); border-radius: 8px; padding: 12px; }
.cart-item__image img { max-width: 100%; max-height: 80px; object-fit: contain; }
.cart-item__info { display: flex; flex-direction: column; gap: 4px; justify-content: center; }
.cart-item__brand { font-family: var(--font-mono, monospace); font-size: 0.65rem; color: #8b85a6; text-transform: uppercase; letter-spacing: 0.08em; }
.cart-item__name { font-family: var(--font-ui, sans-serif); font-size: 0.95rem; font-weight: 600; color: #e8e6f0; line-height: 1.4; }
.cart-item__price { font-family: var(--font-mono, monospace); font-size: 0.85rem; color: #00f0ff; margin-top: 4px; }
.cart-item__actions { display: flex; flex-direction: column; gap: 12px; align-items: flex-end; }
.cart-item__qty { display: flex; align-items: center; border: 1px solid var(--clr-border, rgba(255, 255, 255, 0.06)); border-radius: 8px; overflow: hidden; }
.cart-item__qty-btn { width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; background: rgba(255, 255, 255, 0.03); color: #8b85a6; border: none; cursor: pointer; transition: all 150ms ease; }
.cart-item__qty-btn:hover:not(:disabled) { background: rgba(0, 240, 255, 0.1); color: #00f0ff; }
.cart-item__qty-btn:disabled { opacity: 0.3; cursor: not-allowed; }
.cart-item__qty-value { width: 36px; text-align: center; font-family: var(--font-ui, sans-serif); font-size: 0.85rem; font-weight: 600; color: #e8e6f0; }
.cart-item__subtotal { flex: 1; display: flex; align-items: center; }
.cart-item__remove { width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; background: rgba(255, 45, 149, 0.08); border: 1px solid rgba(255, 45, 149, 0.15); border-radius: 8px; color: #ff2d95; cursor: pointer; transition: all 200ms ease; }
.cart-item__remove:hover:not(:disabled) { background: rgba(255, 45, 149, 0.15); border-color: rgba(255, 45, 149, 0.3); box-shadow: 0 0 12px rgba(255, 45, 149, 0.15); }
.cart-item__remove:disabled { opacity: 0.5; cursor: not-allowed; }
.price-stack { display: flex; flex-direction: column; align-items: flex-end; gap: 5px; }
.price--lg { font-size: 1.15rem !important; }
.eur-badge { font-family: var(--font-mono, monospace); font-size: 0.68rem; color: #8b85a6; background: rgba(139, 133, 166, 0.07); border: 1px solid rgba(139, 133, 166, 0.12); border-radius: 5px; padding: 2px 7px; letter-spacing: 0.02em; }
.eur-convert-badge { display: inline-flex; align-items: center; gap: 5px; font-family: var(--font-mono, monospace); font-size: 0.78rem; color: #a89ec0; background: rgba(139, 133, 166, 0.07); border: 1px solid rgba(139, 133, 166, 0.14); border-radius: 8px; padding: 4px 10px; }
.eur-rate-hint { font-size: 0.62rem; color: #6b6480; padding-left: 6px; border-left: 1px solid rgba(139, 133, 166, 0.2); margin-left: 2px; }
.rate-note { margin-top: 12px; font-family: var(--font-mono, monospace); font-size: 0.65rem; color: #6b6480; text-align: center; letter-spacing: 0.01em; }
.cart-summary { background: var(--clr-surface-light, #1a1530); border: 1px solid var(--clr-border, rgba(255, 255, 255, 0.06)); border-radius: 12px; padding: 24px; height: fit-content; position: sticky; top: 100px; }
.cart-summary__title { font-family: var(--font-ui, sans-serif); font-size: 1.1rem; font-weight: 700; color: #e8e6f0; margin-bottom: 20px; }
.cart-summary__details { display: flex; flex-direction: column; gap: 12px; }
.cart-summary__row { display: flex; justify-content: space-between; align-items: center; font-family: var(--font-ui, sans-serif); font-size: 0.85rem; color: #8b85a6; }
.cart-summary__row--total { font-size: 1rem; font-weight: 700; color: #e8e6f0; align-items: flex-start; }
.cart-summary__continue { display: flex; align-items: center; justify-content: center; gap: 8px; margin-top: 12px; padding: 10px; font-family: var(--font-ui, sans-serif); font-size: 0.85rem; color: #8b85a6; text-decoration: none; transition: color 200ms ease; }
.cart-summary__continue:hover { color: #00f0ff; }
@media (max-width: 639px) {
    .cart-item { grid-template-columns: 80px 1fr; }
    .cart-item__actions { grid-column: 1 / -1; flex-direction: row; justify-content: space-between; align-items: center; }
    .cart-summary { position: static; }
    .eur-rate-hint { display: none; }
}
</style>