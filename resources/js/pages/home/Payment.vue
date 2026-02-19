<script setup>
import { ref, provide } from 'vue';
import { Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import HomeLayout from '@/layouts/home/HomeLayout.vue';
import AuthModal from '@/components/home/AuthModal.vue';

/* ================================================================
   ÉTATS PARTAGÉS
   ================================================================ */
const sidebarOpen = ref(false);
provide('sidebarOpen', sidebarOpen);

const showAuthModal = ref(false);
const authTab = ref('login');
provide('showAuthModal', showAuthModal);
provide('authTab', authTab);
</script>

<template>
    <HomeLayout page-title="Paiement">
        <!-- Modal Auth (composant séparé) -->
        <AuthModal />

        <!-- ============================================================
             CONTENU PAGE
             ============================================================ -->
        <main class="main-content">
            <!-- HERO — Paiement sécurisé -->
            <section class="pay-hero animate-fade-in-up">
                <div class="pay-hero__glow pay-hero__glow--cyan"></div>
                <div class="pay-hero__glow pay-hero__glow--purple"></div>
                <div class="pay-hero__inner">
                    <div class="pay-hero__icon">
                        <svg
                            class="h-10 w-10 text-[#00f0ff]"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="1.5"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z"
                            />
                        </svg>
                    </div>
                    <div class="pay-hero__text">
                        <h1 class="pay-hero__title">
                            Paiement
                            <span class="text-gradient-cyber">Sécurisé</span>
                        </h1>
                        <p class="pay-hero__subtitle">
                            Toutes les transactions sont sécurisées via Papi.mg.
                            Choisissez votre méthode de paiement préférée parmi
                            les options disponibles à Madagascar.
                        </p>
                    </div>
                    <div class="pay-hero__badge">
                        <img
                            src="https://docs.papi.mg/fr/assets/images/integrated-payment-e000e9ced736b96e0345cc33b4ffcddf.png"
                            alt="Papi.mg — Passerelle de paiement"
                            class="pay-hero__partner-logo"
                        />
                        <Link href="https://papi.mg/" class="pay-hero__partner-text"
                            >Propulsé par Papi.mg</Link
                        >
                    </div>
                </div>
            </section>

            <!-- ====================================================
                 SECTION : Méthodes Mobile Money
                 ==================================================== -->
            <section class="section">
                <div class="section__header">
                    <h2 class="section__title">Mobile Money</h2>
                    <span class="badge badge-green">
                        <span
                            style="
                                width: 6px;
                                height: 6px;
                                border-radius: 50%;
                                background: #39ff14;
                            "
                            class="animate-pulse"
                        ></span>
                        Instantané
                    </span>
                </div>

                <div class="pay-grid">
                    <!-- MVola -->
                    <div class="pay-card group">
                        <div
                            class="pay-card__logo-wrapper"
                            style="--pay-color: rgba(255, 204, 0, 0.1)"
                        >
                            <img
                                src="https://www.madagascar-internet.mg/wp-content/uploads/2022/03/MVOLA-300x140.png"
                                alt="MVola — Telma Mobile Money"
                                class="pay-card__logo"
                            />
                        </div>
                        <div class="pay-card__body">
                            <h3 class="pay-card__name">MVola</h3>
                            <p class="pay-card__operator">Yas Madagascar</p>
                            <p class="pay-card__desc">
                                Paiement instantané via votre compte MVola.
                                Tapez *111# ou utilisez l'app MVola.
                            </p>
                            <div class="pay-card__meta">
                                <span class="pay-card__tag pay-card__tag--green"
                                    >Disponible</span
                                >
                                <span class="pay-card__fee"> </span>
                            </div>
                        </div>
                    </div>

                    <!-- Orange Money -->
                    <div class="pay-card group">
                        <div
                            class="pay-card__logo-wrapper"
                            style="--pay-color: rgba(255, 121, 0, 0.1)"
                        >
                            <img
                                src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/c8/Orange_logo.svg/220px-Orange_logo.svg.png"
                                alt="Orange Money — Orange Madagascar"
                                class="pay-card__logo"
                            />
                        </div>
                        <div class="pay-card__body">
                            <h3 class="pay-card__name">Orange Money</h3>
                            <p class="pay-card__operator">Orange Madagascar</p>
                            <p class="pay-card__desc">
                                Payez directement depuis votre compte Orange
                                Money. Tapez #145# ou via l'app Orange Money.
                            </p>
                            <div class="pay-card__meta">
                                <span class="pay-card__tag pay-card__tag--green"
                                    >Disponible</span
                                >
                                <span class="pay-card__fee"> </span>
                            </div>
                        </div>
                    </div>

                    <!-- Airtel Money -->
                    <div class="pay-card group">
                        <div
                            class="pay-card__logo-wrapper"
                            style="--pay-color: rgba(255, 0, 0, 0.08)"
                        >
                            <img
                                src="https://download.logo.wine/logo/Airtel_Africa/Airtel_Africa-Logo.wine.png"
                                alt="Airtel Money — Airtel Madagascar"
                                class="pay-card__logo"
                            />
                        </div>
                        <div class="pay-card__body">
                            <h3 class="pay-card__name">Airtel Money</h3>
                            <p class="pay-card__operator">Airtel Madagascar</p>
                            <p class="pay-card__desc">
                                Effectuez vos paiements via Airtel Money.
                                Simple, rapide et accessible partout à
                                Madagascar.
                            </p>
                            <div class="pay-card__meta">
                                <span class="pay-card__tag pay-card__tag--green"
                                    >Disponible</span
                                >
                                <span class="pay-card__fee"> </span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ====================================================
                 SECTION : Cartes Bancaires
                 ==================================================== -->
            <section class="section">
                <div class="section__header">
                    <h2 class="section__title">Cartes Bancaires</h2>
                    <span class="badge badge-cyan">International</span>
                </div>

                <div class="pay-grid">
                    <!-- Visa -->
                    <div class="pay-card group">
                        <div
                            class="pay-card__logo-wrapper"
                            style="--pay-color: rgba(26, 31, 113, 0.12)"
                        >
                            <img
                                src="https://upload.wikimedia.org/wikipedia/commons/d/d3/Visa_Inc._logo_%282005%E2%80%932014%29.png"
                                alt="Visa — Carte bancaire internationale"
                                class="pay-card__logo"
                            />
                        </div>
                        <div class="pay-card__body">
                            <h3 class="pay-card__name">Visa</h3>
                            <p class="pay-card__operator">
                                Carte internationale
                            </p>
                            <p class="pay-card__desc">
                                Paiement sécurisé par carte Visa. Compatible
                                avec toutes les cartes Visa émises à Madagascar
                                et à l'international.
                            </p>
                            <div class="pay-card__meta">
                                <span class="pay-card__tag pay-card__tag--green"
                                    >Disponible</span
                                >
                                <span class="pay-card__fee"> </span>
                            </div>
                        </div>
                    </div>

                    <!-- Mastercard -->
                    <div class="pay-card group">
                        <div
                            class="pay-card__logo-wrapper"
                            style="--pay-color: rgba(235, 0, 27, 0.08)"
                        >
                            <img
                                src="https://upload.wikimedia.org/wikipedia/commons/thumb/b/b7/MasterCard_Logo.svg/220px-MasterCard_Logo.svg.png"
                                alt="Mastercard — Carte bancaire internationale"
                                class="pay-card__logo"
                            />
                        </div>
                        <div class="pay-card__body">
                            <h3 class="pay-card__name">Mastercard</h3>
                            <p class="pay-card__operator">
                                Carte internationale
                            </p>
                            <p class="pay-card__desc">
                                Acceptation de toutes les cartes Mastercard.
                                Paiement 3D Secure pour une sécurité renforcée.
                            </p>
                            <div class="pay-card__meta">
                                <span class="pay-card__tag pay-card__tag--green"
                                    >Disponible</span
                                >
                                <span class="pay-card__fee"> </span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- SECTION : Comment ça marche -->
            <section class="section">
                <h2 class="section__title" style="margin-bottom: 24px">
                    Comment ça marche ?
                </h2>
                <div class="pay-steps">
                    <div class="pay-step">
                        <div class="pay-step__number">1</div>
                        <div class="pay-step__content">
                            <h3 class="pay-step__title">Ajoutez au panier</h3>
                            <p class="pay-step__desc">
                                Parcourez nos produits et ajoutez ceux qui vous
                                intéressent dans votre panier.
                            </p>
                        </div>
                    </div>
                    <div class="pay-step__line"></div>
                    <div class="pay-step">
                        <div class="pay-step__number">2</div>
                        <div class="pay-step__content">
                            <h3 class="pay-step__title">
                                Choisissez votre méthode
                            </h3>
                            <p class="pay-step__desc">
                                Sélectionnez MVola, Orange Money, Airtel Money
                                ou carte bancaire lors du checkout.
                            </p>
                        </div>
                    </div>
                    <div class="pay-step__line"></div>
                    <div class="pay-step">
                        <div class="pay-step__number">3</div>
                        <div class="pay-step__content">
                            <h3 class="pay-step__title">
                                Confirmez le paiement
                            </h3>
                            <p class="pay-step__desc">
                                Validez la transaction depuis votre téléphone ou
                                entrez vos informations de carte.
                            </p>
                        </div>
                    </div>
                    <div class="pay-step__line"></div>
                    <div class="pay-step">
                        <div class="pay-step__number">
                            <svg
                                class="h-5 w-5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="2.5"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="m4.5 12.75 6 6 9-13.5"
                                />
                            </svg>
                        </div>
                        <div class="pay-step__content">
                            <h3 class="pay-step__title">
                                Commande confirmée !
                            </h3>
                            <p class="pay-step__desc">
                                Recevez votre confirmation par e-mail et suivez
                                votre livraison en temps réel.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- SECTION : Garanties -->
            <section class="section">
                <div class="pay-guarantees">
                    <div class="pay-guarantee">
                        <div class="pay-guarantee__icon">
                            <svg
                                class="h-6 w-6 text-[#00f0ff]"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.5"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z"
                                />
                            </svg>
                        </div>
                        <h3 class="pay-guarantee__title">Paiement sécurisé</h3>
                        <p class="pay-guarantee__desc">
                            Transactions chiffrées SSL via Papi.mg
                        </p>
                    </div>
                    <div class="pay-guarantee">
                        <div class="pay-guarantee__icon">
                            <svg
                                class="h-6 w-6 text-[#39ff14]"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.5"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"
                                />
                            </svg>
                        </div>
                        <h3 class="pay-guarantee__title">Livraison 48h</h3>
                        <p class="pay-guarantee__desc">
                            Livraison partout à Antananarivo en 48h
                        </p>
                    </div>
                    <div class="pay-guarantee">
                        <div class="pay-guarantee__icon">
                            <svg
                                class="h-6 w-6 text-[#b44aff]"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.5"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182"
                                />
                            </svg>
                        </div>
                        <h3 class="pay-guarantee__title">Retour 7 jours</h3>
                        <p class="pay-guarantee__desc">
                            Retour gratuit sous 7 jours si non satisfait
                        </p>
                    </div>
                    <div class="pay-guarantee">
                        <div class="pay-guarantee__icon">
                            <svg
                                class="h-6 w-6 text-[#ff2d95]"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.5"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 0 1-.825-.242m9.345-8.334a2.126 2.126 0 0 0-.476-.095 48.64 48.64 0 0 0-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0 0 11.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155"
                                />
                            </svg>
                        </div>
                        <h3 class="pay-guarantee__title">Support 24/7</h3>
                        <p class="pay-guarantee__desc">
                            Assistance par chat, e-mail ou téléphone
                        </p>
                    </div>
                </div>
            </section>

            <!-- FOOTER -->
            <footer class="page-footer">
                <div class="divider-glow" style="margin-bottom: 20px"></div>
                <div class="page-footer__row">
                    <div class="page-footer__brand">
                        <span class="page-footer__logo">Les Casainiers</span>
                        <span class="font-mono text-xs text-[#8b85a6]"
                            >&copy; 2026 — Antananarivo, Madagascar</span
                        >
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
/* ================================================================
   PAY HERO
   ================================================================ */
.pay-hero {
    position: relative;
    border-radius: 16px;
    border: 1px solid var(--clr-border, rgba(255, 255, 255, 0.06));
    background: linear-gradient(
        135deg,
        rgba(0, 240, 255, 0.05),
        rgba(180, 74, 255, 0.05)
    );
    padding: 40px 32px;
    margin: 24px;
    overflow: hidden;
}
.pay-hero__glow {
    position: absolute;
    width: 280px;
    height: 280px;
    border-radius: 50%;
    filter: blur(120px);
    opacity: 0.35;
    pointer-events: none;
}
.pay-hero__glow--cyan {
    top: -80px;
    left: -40px;
    background: #00f0ff;
}
.pay-hero__glow--purple {
    bottom: -80px;
    right: -40px;
    background: #b44aff;
}
.pay-hero__inner {
    position: relative;
    z-index: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    gap: 20px;
}
.pay-hero__icon {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: rgba(0, 240, 255, 0.08);
    border: 1px solid rgba(0, 240, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
}
.pay-hero__title {
    font-family: var(--font-display, 'Orbitron', sans-serif);
    font-size: 1.8rem;
    font-weight: 800;
    color: #e8e6f0;
    line-height: 1.3;
}
.pay-hero__subtitle {
    font-family: var(--font-ui, sans-serif);
    font-size: 0.9rem;
    color: #8b85a6;
    line-height: 1.6;
    max-width: 520px;
}
.pay-hero__badge {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 16px;
    background: rgba(255, 255, 255, 0.04);
    border: 1px solid var(--clr-border, rgba(255, 255, 255, 0.06));
    border-radius: 10px;
}
.pay-hero__partner-logo {
    height: 24px;
    object-fit: contain;
}
.pay-hero__partner-text {
    font-family: var(--font-mono, monospace);
    font-size: 0.7rem;
    color: #8b85a6;
}

/* ================================================================
   PAY GRID
   ================================================================ */
.pay-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 16px;
}
@media (min-width: 640px) {
    .pay-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
@media (min-width: 1024px) {
    .pay-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

/* ================================================================
   PAY CARD
   ================================================================ */
.pay-card {
    display: flex;
    flex-direction: column;
    background: var(--clr-surface-light, #1a1530);
    border: 1px solid var(--clr-border, rgba(255, 255, 255, 0.06));
    border-radius: 14px;
    overflow: hidden;
    transition: all 300ms ease;
}
.pay-card:hover {
    border-color: rgba(0, 240, 255, 0.2);
    box-shadow: 0 8px 30px rgba(0, 240, 255, 0.06);
    transform: translateY(-2px);
}
.pay-card__logo-wrapper {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100px;
    padding: 20px;
    background: var(--pay-color, rgba(255, 255, 255, 0.03));
    border-bottom: 1px solid var(--clr-border, rgba(255, 255, 255, 0.06));
}
.pay-card__logo {
    max-height: 48px;
    max-width: 120px;
    object-fit: contain;
    filter: brightness(1.1);
    transition: transform 300ms ease;
}
.pay-card:hover .pay-card__logo {
    transform: scale(1.08);
}
.pay-card__body {
    padding: 16px;
    display: flex;
    flex-direction: column;
    gap: 6px;
    flex: 1;
}
.pay-card__name {
    font-family: var(--font-ui, sans-serif);
    font-size: 0.95rem;
    font-weight: 700;
    color: #e8e6f0;
}
.pay-card__operator {
    font-family: var(--font-mono, monospace);
    font-size: 0.65rem;
    color: #8b85a6;
    text-transform: uppercase;
    letter-spacing: 0.06em;
}
.pay-card__desc {
    font-family: var(--font-ui, sans-serif);
    font-size: 0.78rem;
    color: #8b85a6;
    line-height: 1.5;
    margin-top: 4px;
}
.pay-card__meta {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: auto;
    padding-top: 10px;
    border-top: 1px solid var(--clr-border, rgba(255, 255, 255, 0.06));
}
.pay-card__tag {
    padding: 3px 10px;
    border-radius: 20px;
    font-family: var(--font-mono, monospace);
    font-size: 0.62rem;
    font-weight: 600;
    letter-spacing: 0.04em;
}
.pay-card__tag--green {
    background: rgba(57, 255, 20, 0.1);
    color: #39ff14;
    border: 1px solid rgba(57, 255, 20, 0.2);
}
.pay-card__fee {
    font-family: var(--font-mono, monospace);
    font-size: 0.65rem;
    color: #8b85a6;
}

/* ================================================================
   PAY STEPS
   ================================================================ */
.pay-steps {
    display: flex;
    flex-direction: column;
    gap: 0;
}
@media (min-width: 768px) {
    .pay-steps {
        flex-direction: row;
        align-items: flex-start;
        gap: 0;
    }
}
.pay-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    flex: 1;
    padding: 0 12px;
}
.pay-step__number {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: linear-gradient(
        135deg,
        rgba(0, 240, 255, 0.15),
        rgba(180, 74, 255, 0.15)
    );
    border: 1px solid rgba(0, 240, 255, 0.25);
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: var(--font-display, 'Orbitron', sans-serif);
    font-size: 0.9rem;
    font-weight: 800;
    color: #00f0ff;
    margin-bottom: 14px;
    flex-shrink: 0;
}
.pay-step__content {
    max-width: 200px;
}
.pay-step__title {
    font-family: var(--font-ui, sans-serif);
    font-size: 0.85rem;
    font-weight: 700;
    color: #e8e6f0;
    margin-bottom: 6px;
}
.pay-step__desc {
    font-family: var(--font-ui, sans-serif);
    font-size: 0.75rem;
    color: #8b85a6;
    line-height: 1.5;
}
.pay-step__line {
    width: 2px;
    height: 32px;
    background: linear-gradient(
        to bottom,
        rgba(0, 240, 255, 0.3),
        rgba(180, 74, 255, 0.3)
    );
    border-radius: 2px;
    align-self: center;
    margin: 4px 0;
}
@media (min-width: 768px) {
    .pay-step__line {
        width: 100%;
        max-width: 80px;
        height: 2px;
        margin: 22px 0 0 0;
        background: linear-gradient(
            to right,
            rgba(0, 240, 255, 0.3),
            rgba(180, 74, 255, 0.3)
        );
    }
}

/* ================================================================
   PAY GUARANTEES
   ================================================================ */
.pay-guarantees {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}
@media (min-width: 768px) {
    .pay-guarantees {
        grid-template-columns: repeat(4, 1fr);
    }
}
.pay-guarantee {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    gap: 10px;
    padding: 24px 16px;
    background: var(--clr-surface-light, #1a1530);
    border: 1px solid var(--clr-border, rgba(255, 255, 255, 0.06));
    border-radius: 12px;
    transition: all 300ms ease;
}
.pay-guarantee:hover {
    border-color: rgba(0, 240, 255, 0.15);
    box-shadow: 0 4px 20px rgba(0, 240, 255, 0.04);
}
.pay-guarantee__icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid var(--clr-border, rgba(255, 255, 255, 0.06));
    display: flex;
    align-items: center;
    justify-content: center;
}
.pay-guarantee__title {
    font-family: var(--font-ui, sans-serif);
    font-size: 0.82rem;
    font-weight: 700;
    color: #e8e6f0;
}
.pay-guarantee__desc {
    font-family: var(--font-ui, sans-serif);
    font-size: 0.7rem;
    color: #8b85a6;
    line-height: 1.4;
}

/* ================================================================
   RESPONSIVE
   ================================================================ */
@media (max-width: 639px) {
    .pay-hero {
        padding: 24px 16px;
        margin: 16px;
    }
    .pay-hero__title {
        font-size: 1.4rem;
    }
}
</style>
