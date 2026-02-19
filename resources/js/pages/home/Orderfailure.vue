<script setup>
import { Link, router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { computed } from 'vue';
import HomeLayout from '@/layouts/home/HomeLayout.vue';

const props = defineProps({
    order: { type: Object, default: null },
});

const EUR_RATE = 5179;
function formatMga(amount) {
    return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'MGA' }).format(amount);
}
function formatEur(mga) {
    return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(mga / EUR_RATE);
}
const formattedTotal    = computed(() => props.order ? formatMga(props.order.amount) : '—');
const formattedTotalEur = computed(() => props.order ? formatEur(props.order.amount) : '—');

function retry() { router.get(route('home.pannier')); }
</script>

<template>
    <HomeLayout page-title="Paiement échoué">
        <main class="main-content">

            <!-- En-tête -->
            <section class="section">
                <div class="section__header">
                    <div>
                        <h1 class="hero__title" style="font-size:1.8rem; margin-bottom:8px;">
                            Paiement <span class="text-err">échoué</span>
                        </h1>
                        <p class="font-ui text-sm text-[#8b85a6]">Aucun montant n'a été débité de votre compte</p>
                    </div>
                    <!-- Croix inline dans le header -->
                    <div class="x-wrap">
                        <div class="x-glow"></div>
                        <svg viewBox="0 0 56 56" fill="none" width="56" height="56">
                            <circle class="x-c" cx="28" cy="28" r="26" stroke="#ff2d95" stroke-width="2" fill="rgba(255,45,149,.06)"/>
                            <path   class="x-l1" d="M18 18l20 20" stroke="#ff2d95" stroke-width="2.5" stroke-linecap="round"/>
                            <path   class="x-l2" d="M38 18L18 38" stroke="#ff2d95" stroke-width="2.5" stroke-linecap="round"/>
                        </svg>
                    </div>
                </div>
            </section>

            <!-- Contenu -->
            <section class="section">
                <div class="failure-layout">

                    <!-- Infos commande + raisons -->
                    <div class="failure-main">
                        <div class="card-block">
                            <p class="block-label" style="margin-bottom:16px;">Raisons possibles</p>
                            <ul class="reasons">
                                <li>Solde insuffisant sur le compte</li>
                                <li>Transaction annulée ou expirée</li>
                                <li>Problème de réseau pendant le paiement</li>
                                <li>Carte refusée par la banque</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Résumé -->
                    <div class="failure-summary">
                        <div class="card-block" v-if="order">
                            <p class="block-label" style="margin-bottom:16px;">Détails</p>

                            <div class="summary-rows">
                                <div class="summary-row">
                                    <span>Référence</span>
                                    <span class="ref-val">{{ order.reference }}</span>
                                </div>
                                <div class="summary-row">
                                    <span>Statut</span>
                                    <span class="badge badge--failed">
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                                        </svg>
                                        Échec
                                    </span>
                                </div>
                                <div class="divider-glow" style="margin:12px 0;"></div>
                                <div class="summary-row summary-row--total">
                                    <span>Montant</span>
                                    <div style="text-align:right;">
                                        <p class="price" style="font-size:1.1rem;">{{ formattedTotal }}</p>
                                        <p class="eur-hint">≈ {{ formattedTotalEur }}</p>
                                    </div>
                                </div>
                            </div>

                            <button @click="retry" class="btn-cyber btn-primary" style="width:100%; padding:11px; font-size:.88rem; margin-top:16px; display:flex; align-items:center; justify-content:center; gap:8px;">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/>
                                </svg>
                                Réessayer le paiement
                            </button>

                            <Link :href="route('home.product')" class="cart-summary__continue">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
                                </svg>
                                Continuer mes achats
                            </Link>
                        </div>
                    </div>

                </div>
            </section>

            <!-- Footer -->
            <footer class="page-footer">
                <div class="divider-glow" style="margin-bottom:20px;"></div>
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
/* ── Texte erreur ── */
.text-err {
    background: linear-gradient(135deg, #ff2d95, #ff6b35);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* ── Croix header ── */
.x-wrap { position: relative; width: 56px; height: 56px; flex-shrink: 0; }
.x-glow {
    position: absolute; inset: -8px; border-radius: 50%;
    background: radial-gradient(circle, rgba(255,45,149,.25) 0%, transparent 70%);
    animation: gp 2.5s ease-in-out infinite;
}
@keyframes gp { 0%,100%{transform:scale(1);opacity:.5} 50%{transform:scale(1.25);opacity:1} }
.x-c {
    stroke-dasharray: 165; stroke-dashoffset: 165;
    animation: dc .65s cubic-bezier(.65,0,.45,1) forwards;
}
.x-l1, .x-l2 {
    stroke-dasharray: 30; stroke-dashoffset: 30;
    animation: dl .3s cubic-bezier(.65,0,.45,1) .7s forwards;
}
.x-l2 { animation-delay: .88s; }
@keyframes dc { to { stroke-dashoffset: 0; } }
@keyframes dl { to { stroke-dashoffset: 0; } }

/* ── Layout ── */
.failure-layout {
    display: grid;
    grid-template-columns: 1fr 340px;
    gap: 20px;
    align-items: start;
}
@media (max-width: 900px) { .failure-layout { grid-template-columns: 1fr; } }
.failure-summary { position: sticky; top: 90px; }

/* ── Card block ── */
.card-block {
    background: var(--clr-surface-light, #1a1530);
    border: 1px solid var(--clr-border, rgba(255,255,255,.06));
    border-radius: 12px;
    padding: 20px;
}
.block-label {
    font-family: var(--font-mono, monospace);
    font-size: .65rem; color: #8b85a6;
    text-transform: uppercase; letter-spacing: .08em;
}

/* ── Raisons ── */
.reasons {
    list-style: none; padding: 0; margin: 0;
    display: flex; flex-direction: column; gap: 10px;
}
.reasons li {
    font-family: var(--font-ui, sans-serif);
    font-size: .83rem; color: #8b85a6; line-height: 1.4;
    padding: 10px 14px;
    background: rgba(255,45,149,.03);
    border: 1px solid rgba(255,45,149,.07);
    border-radius: 8px;
    padding-left: 32px;
    position: relative;
}
.reasons li::before {
    content: '—';
    position: absolute; left: 12px;
    color: #ff2d95; opacity: .5; font-size: .75rem;
}

/* ── Summary rows ── */
.summary-rows { display: flex; flex-direction: column; gap: 10px; }
.summary-row {
    display: flex; justify-content: space-between; align-items: center;
    font-family: var(--font-ui, sans-serif); font-size: .82rem; color: #8b85a6;
}
.summary-row--total { font-weight: 700; color: #e8e6f0; align-items: flex-start; }
.ref-val {
    font-family: var(--font-mono, monospace);
    font-size: .72rem; color: #ff2d95; font-weight: 600;
    letter-spacing: .04em; text-align: right;
}
.eur-hint {
    font-family: var(--font-mono, monospace);
    font-size: .62rem; color: #6b6480; margin-top: 3px;
}

/* ── Badge ── */
.badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 3px 10px; border-radius: 20px;
    font-family: var(--font-mono, monospace); font-size: .62rem; font-weight: 700;
}
.badge--failed {
    background: rgba(255,45,149,.08); color: #ff2d95;
    border: 1px solid rgba(255,45,149,.2);
}

/* ── Lien continuer (même que Pannier.vue) ── */
.cart-summary__continue {
    display: flex; align-items: center; justify-content: center; gap: 8px;
    margin-top: 12px; padding: 10px;
    font-family: var(--font-ui, sans-serif); font-size: .85rem;
    color: #8b85a6; text-decoration: none; transition: color 200ms ease;
}
.cart-summary__continue:hover { color: #00f0ff; }
</style>