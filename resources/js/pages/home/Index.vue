<script setup>
import { ref, provide } from 'vue'
import { Link } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import HomeLayout from '@/layouts/home/HomeLayout.vue'
import AuthModal from '@/components/home/AuthModal.vue'

/* ================================================================
   PROPS INERTIA (données du back)
   ================================================================ */
const props = defineProps({
    categories: {
        type: Array,
        default: () => [],
    },
    trending: {
        type: Array,
        default: () => [],
    },
    stats: {
        type: Object,
        default: () => ({ products: 0, customers: 0 }),
    },
})

/* ================================================================
   ÉTATS PARTAGÉS
   ================================================================ */
const sidebarOpen = ref(false)
provide('sidebarOpen', sidebarOpen)

const showAuthModal = ref(false)
const authTab = ref('login')
provide('showAuthModal', showAuthModal)
provide('authTab', authTab)

/* ================================================================
   ICÔNES & COULEURS PAR CATÉGORIE (mapping slug → style)
   ================================================================ */
const categoryStyles = {
    processeurs:       { color: 'rgba(0, 240, 255, 0.15)',  textColor: '#00f0ff', icon: 'cpu' },
    'cartes-graphiques': { color: 'rgba(180, 74, 255, 0.15)', textColor: '#b44aff', icon: 'gpu' },
    'memoire-ram':     { color: 'rgba(57, 255, 20, 0.15)',  textColor: '#39ff14', icon: 'ram' },
    'stockage-ssdhdd': { color: 'rgba(255, 45, 149, 0.15)', textColor: '#ff2d95', icon: 'storage' },
    peripheriques:     { color: 'rgba(251, 191, 36, 0.15)', textColor: '#fbbf24', icon: 'monitor' },
    'boitiers-alim':   { color: 'rgba(0, 240, 255, 0.1)',   textColor: '#00f0ff', icon: 'case' },
}

function getCatStyle(slug) {
    return categoryStyles[slug] || { color: 'rgba(255,255,255,0.08)', textColor: '#8b85a6', icon: 'default' }
}

/* ================================================================
   FORMAT STATS
   ================================================================ */
function formatNumber(n) {
    if (n >= 1000) return (n / 1000).toFixed(1).replace('.0', '') + 'K+'
    return n + '+'
}
</script>

<template>
    <HomeLayout page-title="Accueil">

        <AuthModal />

        <main class="main-content">

            <!-- ====================================================
                 HERO BANNER
                 ==================================================== -->
            <section class="hero animate-fade-in-up">
                <div class="hero__inner">
                    <div class="hero__glow hero__glow--cyan"></div>
                    <div class="hero__glow hero__glow--purple"></div>
                    <div class="hero__text">
                        <span class="badge badge-cyan" style="margin-bottom: 16px;">
                            <span style="width:6px;height:6px;border-radius:50%;background:#00f0ff;" class="animate-pulse"></span>
                            Nouveau : RTX 5090 disponible
                        </span>
                        <h1 class="hero__title">
                            Bienvenue sur <span class="text-gradient-cyber">Les Casainiers</span>
                        </h1>
                        <p class="hero__subtitle">
                            Votre destination de confiance pour les composants informatiques haut de gamme. Performance, fiabilité, prix imbattables.
                        </p>
                        <div class="hero__buttons">
                            <Link :href="route('home.product')" class="btn-cyber btn-primary">Explorer les produits</Link>
                            <Link :href="route('home.offers')" class="btn-cyber btn-accent">Offres Flash</Link>
                        </div>
                    </div>
                    <div class="hero__stats">
                        <div class="glass hero__stat-item">
                            <p class="stat-number text-2xl">{{ formatNumber(stats.products) }}</p>
                            <p class="font-ui text-xs text-[#8b85a6]">Produits</p>
                        </div>
                        <div class="glass hero__stat-item">
                            <p class="stat-number text-2xl">48H</p>
                            <p class="font-ui text-xs text-[#8b85a6]">Livraison</p>
                        </div>
                        <div class="glass hero__stat-item">
                            <p class="stat-number text-2xl">{{ formatNumber(stats.customers) }}</p>
                            <p class="font-ui text-xs text-[#8b85a6]">Clients</p>
                        </div>
                        <div class="glass hero__stat-item">
                            <p class="stat-number text-2xl">4.9</p>
                            <p class="font-ui text-xs text-[#8b85a6]">Note Moyenne</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ====================================================
                 CATÉGORIES POPULAIRES (dynamique)
                 ==================================================== -->
            <section class="section">
                <div class="section__header">
                    <h2 class="section__title">Catégories Populaires</h2>
                    <Link :href="route('home.categories')" class="section__link">
                        Voir tout
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 5 7 7-7 7"/></svg>
                    </Link>
                </div>
                <div class="categories-grid">
                    <Link
                        v-for="cat in categories"
                        :key="cat.id"
                        :href="route('home.product', { categorie: cat.slug })"
                        class="cat-card group"
                    >
                        <div class="cat-card__icon" :style="{ '--cat-color': getCatStyle(cat.slug).color }">
                            <!-- CPU -->
                            <svg v-if="getCatStyle(cat.slug).icon === 'cpu'" class="h-6 w-6" :style="{ color: getCatStyle(cat.slug).textColor }" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 3v1.5M4.5 8.25H3m18 0h-1.5M4.5 12H3m18 0h-1.5m-15 3.75H3m18 0h-1.5M8.25 19.5V21M12 3v1.5m0 15V21m3.75-18v1.5m0 15V21m-9-1.5h10.5a2.25 2.25 0 0 0 2.25-2.25V6.75a2.25 2.25 0 0 0-2.25-2.25H6.75A2.25 2.25 0 0 0 4.5 6.75v10.5a2.25 2.25 0 0 0 2.25 2.25Z"/></svg>
                            <!-- GPU -->
                            <svg v-else-if="getCatStyle(cat.slug).icon === 'gpu'" class="h-6 w-6" :style="{ color: getCatStyle(cat.slug).textColor }" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6Zm9.75 0A2.25 2.25 0 0 1 15.75 3.75H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6Z"/></svg>
                            <!-- RAM -->
                            <svg v-else-if="getCatStyle(cat.slug).icon === 'ram'" class="h-6 w-6" :style="{ color: getCatStyle(cat.slug).textColor }" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 14.25h13.5m-13.5 0a3 3 0 0 1-3-3m3 3a3 3 0 1 0 0 6h13.5a3 3 0 1 0 0-6m-16.5-3a3 3 0 0 1 3-3h13.5a3 3 0 0 1 3 3"/></svg>
                            <!-- Storage -->
                            <svg v-else-if="getCatStyle(cat.slug).icon === 'storage'" class="h-6 w-6" :style="{ color: getCatStyle(cat.slug).textColor }" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375"/></svg>
                            <!-- Monitor -->
                            <svg v-else-if="getCatStyle(cat.slug).icon === 'monitor'" class="h-6 w-6" :style="{ color: getCatStyle(cat.slug).textColor }" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 20.25h12m-7.5-3v3m3-3v3m-10.125-3h17.25c.621 0 1.125-.504 1.125-1.125V4.875c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125Z"/></svg>
                            <!-- Case -->
                            <svg v-else-if="getCatStyle(cat.slug).icon === 'case'" class="h-6 w-6" :style="{ color: getCatStyle(cat.slug).textColor }" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5V18M15 7.5V18M3 16.811V8.69c0-.864.933-1.406 1.683-.977l7.108 4.061a1.125 1.125 0 0 1 0 1.954l-7.108 4.061A1.125 1.125 0 0 1 3 16.811Z"/></svg>
                            <!-- Default -->
                            <svg v-else class="h-6 w-6" :style="{ color: getCatStyle(cat.slug).textColor }" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z"/></svg>
                        </div>
                        <p class="cat-card__name">{{ cat.name }}</p>
                        <p class="cat-card__count">{{ cat.products_count }} article{{ cat.products_count > 1 ? 's' : '' }}</p>
                    </Link>
                </div>
            </section>

            <!-- ====================================================
                 PRODUITS TENDANCE (dynamique)
                 ==================================================== -->
            <section class="section">
                <div class="section__header">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <h2 class="section__title">Tendances</h2>
                        <span class="badge badge-green">
                            <span style="width:6px;height:6px;border-radius:50%;background:#39ff14;" class="animate-pulse"></span>
                            Live
                        </span>
                    </div>
                    <Link :href="route('home.product')" class="section__link">
                        Voir tout
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 5 7 7-7 7"/></svg>
                    </Link>
                </div>
                <div class="products-grid">
                    <Link
                        v-for="product in trending"
                        :key="product.id"
                        :href="route('home.product', { categorie: product.category })"
                        class="card group"
                        style="text-decoration:none;"
                    >
                        <div class="card-img-wrapper" style="height:192px;display:flex;align-items:center;justify-content:center;padding:16px;">
                            <img :src="product.image" :alt="product.name" class="home-product__image" />
                        </div>
                        <div style="padding:16px;">
                            <p class="font-mono text-[0.65rem] text-[#8b85a6]" style="margin-bottom:4px;">{{ product.brand }}</p>
                            <h3 class="font-ui line-clamp-2 text-sm font-semibold text-[#e8e6f0]" style="margin-bottom:8px;">{{ product.name }}</h3>
                            <div class="stars-row" style="margin-bottom:12px;">
                                <svg
                                    v-for="n in 5" :key="n"
                                    :class="['h-3.5 w-3.5', n <= product.rating ? 'star-filled' : 'star-empty']"
                                    fill="currentColor" viewBox="0 0 24 24"
                                ><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                <span class="font-mono text-[0.6rem] text-[#8b85a6]" style="margin-left:4px;">({{ product.reviews }})</span>
                            </div>
                            <div style="display:flex;align-items:center;justify-content:space-between;">
                                <span class="price text-base">{{ product.price }}</span>
                                <span class="btn-cyber btn-primary" style="padding:8px 12px;font-size:0;">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z"/></svg>
                                </span>
                            </div>
                        </div>
                    </Link>
                </div>
            </section>

            <!-- ====================================================
                 BANNIÈRE OFFRES FLASH (inchangé)
                 ==================================================== -->
            <section class="section">
                <div class="flash-banner">
                    <div class="flash-banner__glow flash-banner__glow--pink"></div>
                    <div class="flash-banner__glow flash-banner__glow--purple"></div>
                    <div class="flash-banner__content">
                        <div>
                            <span class="badge badge-pink" style="margin-bottom: 12px;">
                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z"/></svg>
                                Offres Flash
                            </span>
                            <h2 class="flash-banner__title">
                                Jusqu'à <span class="text-glow-pink" style="color:#ff2d95;">-40%</span> sur les composants
                            </h2>
                            <p class="font-ui text-sm text-[#8b85a6]" style="max-width:420px;">
                                Offres limitées dans le temps sur une sélection de composants. Chaque offre a sa propre durée — ne manquez pas les meilleures réductions.
                            </p>
                        </div>
                        <div class="flash-banner__right">
                            <div class="flash-banner__icon">
                                <svg class="h-12 w-12 text-[#ff2d95]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="m3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z"/></svg>
                            </div>
                            <Link :href="route('home.offers')" class="btn-cyber btn-accent" style="width:100%;text-align:center;">Voir les offres</Link>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ====================================================
                 PAIEMENT SÉCURISÉ (inchangé)
                 ==================================================== -->
            <section class="section">
                <div class="section__header">
                    <h2 class="section__title">Paiement sécurisé</h2>
                    <Link :href="route('home.payment')" class="section__link">
                        En savoir plus
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 5 7 7-7 7"/></svg>
                    </Link>
                </div>
                <div class="home-trust">
                    <div class="home-trust__item">
                        <div class="home-trust__icon" style="--trust-color: rgba(0, 240, 255, 0.12);">
                            <svg class="h-5 w-5 text-[#00f0ff]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z"/></svg>
                        </div>
                        <div>
                            <p class="home-trust__title">Paiement sécurisé</p>
                            <p class="home-trust__desc">SSL via Papi.mg</p>
                        </div>
                    </div>
                    <div class="home-trust__item">
                        <div class="home-trust__icon" style="--trust-color: rgba(57, 255, 20, 0.12);">
                            <svg class="h-5 w-5 text-[#39ff14]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"/></svg>
                        </div>
                        <div>
                            <p class="home-trust__title">Livraison 48h</p>
                            <p class="home-trust__desc">Antananarivo</p>
                        </div>
                    </div>
                    <div class="home-trust__item">
                        <div class="home-trust__icon" style="--trust-color: rgba(180, 74, 255, 0.12);">
                            <svg class="h-5 w-5 text-[#b44aff]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182"/></svg>
                        </div>
                        <div>
                            <p class="home-trust__title">Retour 7 jours</p>
                            <p class="home-trust__desc">Gratuit</p>
                        </div>
                    </div>
                    <div class="home-trust__item">
                        <div class="home-trust__icon" style="--trust-color: rgba(255, 45, 149, 0.12);">
                            <svg class="h-5 w-5 text-[#ff2d95]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 0 1-.825-.242m9.345-8.334a2.126 2.126 0 0 0-.476-.095 48.64 48.64 0 0 0-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0 0 11.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155"/></svg>
                        </div>
                        <div>
                            <p class="home-trust__title">Support 24/7</p>
                            <p class="home-trust__desc">Chat & e-mail</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ====================================================
                 PARTENAIRES (inchangé)
                 ==================================================== -->
            <section class="section" style="text-align: center;">
                <h2 class="section__title" style="margin-bottom: 24px;">Nos Partenaires</h2>
                <div class="partners">
                    <span>NVIDIA</span>
                    <span>AMD</span>
                    <span>INTEL</span>
                    <span>CORSAIR</span>
                    <span>SAMSUNG</span>
                    <span>ASUS</span>
                    <span>MSI</span>
                </div>
            </section>

            <!-- ====================================================
                 FOOTER
                 ==================================================== -->
            <footer class="page-footer">
                <div class="divider-glow" style="margin-bottom: 20px;"></div>
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
.home-product__image { max-height: 140px; max-width: 100%; object-fit: contain; border-radius: 8px; opacity: 0.85; transition: opacity 200ms ease, transform 300ms ease; }
.card:hover .home-product__image { opacity: 1; transform: scale(1.05); }
.flash-banner__icon { width: 80px; height: 80px; border-radius: 50%; background: rgba(255, 45, 149, 0.08); border: 1px solid rgba(255, 45, 149, 0.15); display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; }
.home-trust { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
@media (min-width: 768px) { .home-trust { grid-template-columns: repeat(4, 1fr); } }
.home-trust__item { display: flex; align-items: center; gap: 12px; padding: 16px; background: var(--clr-surface-light, #1a1530); border: 1px solid var(--clr-border, rgba(255,255,255,0.06)); border-radius: 12px; transition: all 300ms ease; }
.home-trust__item:hover { border-color: rgba(0, 240, 255, 0.15); box-shadow: 0 4px 20px rgba(0, 240, 255, 0.04); }
.home-trust__icon { width: 40px; height: 40px; border-radius: 10px; background: var(--trust-color, rgba(255,255,255,0.05)); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.home-trust__title { font-family: var(--font-ui, sans-serif); font-size: 0.8rem; font-weight: 700; color: #e8e6f0; }
.home-trust__desc { font-family: var(--font-mono, monospace); font-size: 0.65rem; color: #8b85a6; }
</style>