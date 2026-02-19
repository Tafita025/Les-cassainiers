<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
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
</script>

<template>
    <HomeLayout page-title="Commande confirmée">
        <main class="main-content">

            <!-- En-tête -->
            <section class="section">
                <div class="section__header">
                    <div>
                        <h1 class="hero__title" style="font-size:1.8rem; margin-bottom:8px;">
                            Commande <span class="text-gradient-cyber">confirmée</span>
                        </h1>
                        <p class="font-ui text-sm text-[#8b85a6]">Votre paiement a été traité avec succès</p>
                    </div>
                    <!-- Checkmark inline dans le header -->
                    <div class="check-wrap">
                        <div class="check-glow"></div>
                        <svg viewBox="0 0 56 56" fill="none" width="56" height="56">
                            <circle class="check-c" cx="28" cy="28" r="26" stroke="#39ff14" stroke-width="2" fill="rgba(57,255,20,.06)"/>
                            <path   class="check-t" d="M16 28.5l8 8 16-16" stroke="#39ff14" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </div>
            </section>

            <!-- Contenu -->
            <section v-if="order" class="section">
                <div class="success-layout">

                    <!-- Colonne articles -->
                    <div class="success-articles">
                        <div class="card-block">
                            <p class="block-label" style="margin-bottom:16px;">
                                {{ order.total_items }} article{{ order.total_items > 1 ? 's' : '' }} commandé{{ order.total_items > 1 ? 's' : '' }}
                            </p>

                            <div class="art-list">
                                <div class="art-row" v-for="(item, i) in order.items" :key="i">
                                    <div class="art-row__img">
                                        <img :src="item.image" :alt="item.name" />
                                    </div>
                                    <div class="art-row__info">
                                        <p class="art-row__brand">{{ item.brand }}</p>
                                        <p class="art-row__name">{{ item.name }}</p>
                                        <p class="art-row__qty">Qté : {{ item.quantity }}</p>
                                    </div>
                                    <div class="art-row__price">
                                        <span class="price" style="font-size:.88rem;">{{ formatMga(item.subtotal) }}</span>
                                        <span class="art-row__eur">≈ {{ formatEur(item.subtotal) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Colonne résumé -->
                    <div class="success-summary">
                        <div class="card-block">
                            <p class="block-label" style="margin-bottom:16px;">Résumé</p>

                            <div class="summary-rows">
                                <div class="summary-row">
                                    <span>Référence</span>
                                    <span class="ref-val">{{ order.reference }}</span>
                                </div>
                                <div class="summary-row">
                                    <span>Statut</span>
                                    <span class="badge badge--paid">
                                        <span class="badge__dot"></span>
                                        Payée
                                    </span>
                                </div>
                                <div class="divider-glow" style="margin:12px 0;"></div>
                                <div class="summary-row summary-row--total">
                                    <span>Total</span>
                                    <div style="text-align:right;">
                                        <p class="price" style="font-size:1.1rem;">{{ formattedTotal }}</p>
                                        <p class="art-row__eur">≈ {{ formattedTotalEur }}</p>
                                    </div>
                                </div>
                            </div>

                            <p class="summary-note">Votre commande est en cours de préparation</p>

                            <Link :href="route('home.product')" class="btn-cyber btn-primary" style="width:100%; padding:11px; font-size:.88rem; margin-top:16px; display:flex; align-items:center; justify-content:center; gap:8px;">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007Z"/>
                                </svg>
                                Continuer mes achats
                            </Link>
                        </div>
                    </div>

                </div>
            </section>

            <!-- Pas de commande -->
            <section v-else class="section">
                <div class="card-block" style="text-align:center; padding:40px;">
                    <p class="font-ui text-sm text-[#8b85a6]">Commande introuvable — mais votre paiement a bien été reçu.</p>
                    <Link :href="route('home.product')" class="btn-cyber btn-primary" style="margin-top:16px; padding:10px 24px; display:inline-flex; align-items:center; gap:8px;">
                        Retour aux produits
                    </Link>
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
/* ── Checkmark header ── */
.check-wrap { position: relative; width: 56px; height: 56px; flex-shrink: 0; }
.check-glow {
    position: absolute; inset: -8px; border-radius: 50%;
    background: radial-gradient(circle, rgba(57,255,20,.25) 0%, transparent 70%);
    animation: gp 2.5s ease-in-out infinite;
}
@keyframes gp { 0%,100%{transform:scale(1);opacity:.5} 50%{transform:scale(1.25);opacity:1} }
.check-c {
    stroke-dasharray: 165; stroke-dashoffset: 165;
    animation: dc .65s cubic-bezier(.65,0,.45,1) forwards;
}
.check-t {
    stroke-dasharray: 50; stroke-dashoffset: 50;
    animation: dt .35s cubic-bezier(.65,0,.45,1) .7s forwards;
}
@keyframes dc { to { stroke-dashoffset: 0; } }
@keyframes dt { to { stroke-dashoffset: 0; } }

/* ── Layout grille ── */
.success-layout {
    display: grid;
    grid-template-columns: 1fr 340px;
    gap: 20px;
    align-items: start;
}
@media (max-width: 900px) { .success-layout { grid-template-columns: 1fr; } }

.success-summary { position: sticky; top: 90px; }

/* ── Card block (même style que cart-summary) ── */
.card-block {
    background: var(--clr-surface-light, #1a1530);
    border: 1px solid var(--clr-border, rgba(255,255,255,.06));
    border-radius: 12px;
    padding: 20px;
}

.block-label {
    font-family: var(--font-mono, monospace);
    font-size: .65rem;
    color: #8b85a6;
    text-transform: uppercase;
    letter-spacing: .08em;
}

/* ── Articles ── */
.art-list { display: flex; flex-direction: column; gap: 10px; }

.art-row {
    display: grid;
    grid-template-columns: 72px 1fr auto;
    gap: 14px;
    align-items: center;
    padding: 12px;
    background: rgba(255,255,255,.02);
    border: 1px solid rgba(255,255,255,.05);
    border-radius: 10px;
    transition: border-color 180ms;
}
.art-row:hover { border-color: rgba(0,240,255,.1); }

.art-row__img {
    width: 72px; height: 72px;
    display: flex; align-items: center; justify-content: center;
    background: rgba(0,240,255,.03);
    border-radius: 8px;
    padding: 8px;
    flex-shrink: 0;
}
.art-row__img img { max-width: 100%; max-height: 52px; object-fit: contain; }

.art-row__brand {
    font-family: var(--font-mono, monospace);
    font-size: .58rem; color: #8b85a6;
    text-transform: uppercase; letter-spacing: .07em;
}
.art-row__name {
    font-family: var(--font-ui, sans-serif);
    font-size: .88rem; font-weight: 600; color: #e8e6f0;
    line-height: 1.3; margin: 3px 0 2px;
}
.art-row__qty {
    font-family: var(--font-mono, monospace);
    font-size: .62rem; color: #8b85a6;
}
.art-row__price {
    display: flex; flex-direction: column; align-items: flex-end; gap: 4px; flex-shrink: 0;
}
.art-row__eur {
    font-family: var(--font-mono, monospace);
    font-size: .62rem; color: #6b6480;
}

/* ── Résumé ── */
.summary-rows { display: flex; flex-direction: column; gap: 10px; }
.summary-row {
    display: flex; justify-content: space-between; align-items: center;
    font-family: var(--font-ui, sans-serif); font-size: .82rem; color: #8b85a6;
}
.summary-row--total { font-weight: 700; color: #e8e6f0; align-items: flex-start; }
.ref-val {
    font-family: var(--font-mono, monospace);
    font-size: .72rem; color: #00f0ff; font-weight: 600;
    letter-spacing: .04em; text-align: right;
}
.summary-note {
    margin-top: 14px;
    font-family: var(--font-mono, monospace);
    font-size: .62rem; color: #6b6480; text-align: center;
}

/* ── Badge ── */
.badge {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 3px 10px; border-radius: 20px;
    font-family: var(--font-mono, monospace); font-size: .62rem; font-weight: 700;
}
.badge--paid {
    background: rgba(57,255,20,.08); color: #39ff14;
    border: 1px solid rgba(57,255,20,.2);
}
.badge__dot {
    width: 6px; height: 6px; border-radius: 50%; background: #39ff14;
    animation: bl 2s infinite;
}
@keyframes bl { 0%,100%{opacity:1} 50%{opacity:.3} }

/* ── Responsive articles ── */
@media (max-width: 540px) {
    .art-row { grid-template-columns: 56px 1fr; }
    .art-row__price { grid-column: 1/-1; flex-direction: row; gap: 10px; align-items: center; }
}
</style>