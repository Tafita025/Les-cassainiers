<script setup>
import { ref, computed, provide, watch } from 'vue'
import { Link } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import HomeLayout from '@/layouts/home/HomeLayout.vue'
import AuthModal from '@/components/home/AuthModal.vue'

/* ================================================================
   PROPS INERTIA
   ================================================================ */
const props = defineProps({
    categories: {
        type: Array,
        default: () => [],
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
   SEARCH + LOADING
   ================================================================ */
const search = ref('')
const isSearching = ref(false)
let searchTimeout = null
const SEARCH_DELAY = 1500

watch(search, (newVal, oldVal) => {
    if (newVal.trim() !== oldVal.trim()) {
        isSearching.value = true
        clearTimeout(searchTimeout)
        searchTimeout = setTimeout(() => {
            isSearching.value = false
        }, SEARCH_DELAY)
    }
})

/* ================================================================
   COULEURS PAR INDEX (cycle cyberpunk)
   ================================================================ */
const accentColors = ['#00f0ff', '#b44aff', '#39ff14', '#ff2d95', '#fbbf24']

function getColor(index) {
    return accentColors[index % accentColors.length]
}

/* ================================================================
   FILTRE
   ================================================================ */
const filteredCategories = computed(() => {
    if (!search.value.trim()) return props.categories
    const q = search.value.toLowerCase().trim()
    return props.categories.filter(
        c => c.name.toLowerCase().includes(q) || (c.description && c.description.toLowerCase().includes(q))
    )
})

const skeletonCount = computed(() => {
    return Math.min(filteredCategories.value.length || 4, 6)
})
</script>

<template>
    <HomeLayout page-title="Catégories">

        <AuthModal />

        <main class="main-content">

            <!-- EN-TÊTE + SEARCH -->
            <section class="section">
                <div class="cat-page-header">
                    <div>
                        <h1 class="hero__title" style="font-size:1.8rem;margin-bottom:8px;">
                            Toutes les <span class="text-gradient-cyber">Catégories</span>
                        </h1>
                        <p class="font-ui text-sm text-[#8b85a6]">
                            <template v-if="isSearching">Recherche en cours…</template>
                            <template v-else>{{ filteredCategories.length }} catégorie{{ filteredCategories.length > 1 ? 's' : '' }} disponible{{ filteredCategories.length > 1 ? 's' : '' }}</template>
                        </p>
                    </div>
                    <div class="cat-search" :class="{ 'cat-search--active': isSearching }">
                        <div class="cat-search__icon">
                            <svg v-if="!isSearching" class="h-4 w-4 text-[#8b85a6]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                            <svg v-else class="h-4 w-4 text-[#00f0ff] cat-spinner" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182"/></svg>
                        </div>
                        <input v-model="search" type="text" placeholder="Rechercher une catégorie..." class="cat-search__input" />
                        <button v-if="search" class="cat-search__clear" @click="search = ''; isSearching = false;">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>
            </section>

            <!-- GRILLE CATÉGORIES -->
            <section class="section">

                <!-- Skeleton Pulse -->
                <div v-if="isSearching" class="cat-grid">
                    <div v-for="n in skeletonCount" :key="'skel-' + n" class="cat-skeleton" :style="{ animationDelay: (n - 1) * 120 + 'ms' }">
                        <div class="cat-skeleton__icon"></div>
                        <div class="cat-skeleton__info">
                            <div class="cat-skeleton__line cat-skeleton__line--title"></div>
                            <div class="cat-skeleton__line cat-skeleton__line--desc"></div>
                        </div>
                        <div class="cat-skeleton__right">
                            <div class="cat-skeleton__badge"></div>
                            <div class="cat-skeleton__arrow"></div>
                        </div>
                    </div>
                </div>

                <!-- Aucun résultat -->
                <div v-else-if="filteredCategories.length === 0" class="cat-empty">
                    <div class="cat-empty__icon-wrapper">
                        <svg class="h-12 w-12 text-[#8b85a6]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                    </div>
                    <p class="cat-empty__text">Aucune catégorie trouvée pour "<span class="cat-empty__query">{{ search }}</span>"</p>
                    <button class="btn-cyber btn-primary cat-empty__reset" @click="search = ''">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182"/></svg>
                        Réinitialiser
                    </button>
                </div>

                <!-- Cards Catégories -->
                <div v-else class="cat-grid">
                    <Link
                        v-for="(cat, index) in filteredCategories"
                        :key="cat.id"
                        :href="route('home.product', { categorie: cat.slug })"
                        class="cat-card-full group"
                        :style="{ animationDelay: index * 60 + 'ms' }"
                    >
                        <div class="cat-card-full__icon" :style="{ '--cat-accent': getColor(index) }">
                            <svg class="h-7 w-7" :style="{ color: getColor(index) }" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z"/></svg>
                        </div>
                        <div class="cat-card-full__info">
                            <h3 class="cat-card-full__name">{{ cat.name }}</h3>
                            <p class="cat-card-full__desc">{{ cat.description || 'Découvrez nos produits dans cette catégorie.' }}</p>
                        </div>
                        <div class="cat-card-full__right">
                            <span class="cat-card-full__count">{{ cat.count }} article{{ cat.count > 1 ? 's' : '' }}</span>
                            <svg class="cat-card-full__arrow" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m9 5 7 7-7 7"/></svg>
                        </div>
                    </Link>
                </div>
            </section>

            <!-- FOOTER -->
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
/* ================================================================
   PAGE HEADER
   ================================================================ */
.cat-page-header { display: flex; flex-direction: column; gap: 20px; }
@media (min-width: 640px) { .cat-page-header { flex-direction: row; align-items: center; justify-content: space-between; } }

/* ================================================================
   SEARCH INPUT
   ================================================================ */
.cat-search { position: relative; width: 100%; max-width: 360px; transition: all 300ms ease; }
.cat-search--active { box-shadow: 0 0 0 3px rgba(0, 240, 255, 0.1); border-radius: 10px; }
.cat-search__icon { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); pointer-events: none; }
.cat-search__input { width: 100%; padding: 10px 40px 10px 42px; background: var(--clr-surface-light, #1a1530); border: 1px solid var(--clr-border, rgba(255,255,255,0.06)); border-radius: 10px; font-family: var(--font-ui, sans-serif); font-size: 0.85rem; color: #e8e6f0; outline: none; transition: all 200ms ease; }
.cat-search__input::placeholder { color: #8b85a6; font-size: 0.82rem; }
.cat-search__input:focus { border-color: rgba(0, 240, 255, 0.3); box-shadow: 0 0 0 3px rgba(0, 240, 255, 0.08); }
.cat-search__clear { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); width: 28px; height: 28px; border-radius: 50%; background: rgba(255, 255, 255, 0.05); border: none; color: #8b85a6; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 150ms ease; }
.cat-search__clear:hover { background: rgba(255, 45, 149, 0.15); color: #ff2d95; }
.cat-spinner { animation: spin 0.8s linear infinite; }
@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }

/* ================================================================
   EMPTY STATE
   ================================================================ */
.cat-empty { display: flex; flex-direction: column; align-items: center; gap: 14px; padding: 60px 20px; text-align: center; animation: fadeInUp 400ms ease both; }
.cat-empty__icon-wrapper { width: 80px; height: 80px; border-radius: 50%; background: rgba(255, 255, 255, 0.03); border: 1px solid var(--clr-border, rgba(255,255,255,0.06)); display: flex; align-items: center; justify-content: center; opacity: 0.5; }
.cat-empty__text { font-family: var(--font-ui, sans-serif); font-size: 0.9rem; color: #8b85a6; }
.cat-empty__query { color: #ff2d95; font-weight: 600; }
.cat-empty__reset { margin-top: 4px; padding: 8px 20px; font-size: 0.8rem; display: flex; align-items: center; gap: 6px; }

/* ================================================================
   CATEGORY GRID
   ================================================================ */
.cat-grid { display: flex; flex-direction: column; gap: 10px; }

/* ================================================================
   SKELETON PULSE CARDS
   ================================================================ */
.cat-skeleton { display: flex; align-items: center; gap: 16px; padding: 16px 20px; background: var(--clr-surface-light, #1a1530); border: 1px solid var(--clr-border, rgba(255,255,255,0.06)); border-radius: 12px; animation: skeletonSlideIn 400ms ease both, skeletonPulse 1.4s ease-in-out infinite; }
.cat-skeleton__icon { width: 52px; height: 52px; border-radius: 12px; background: linear-gradient(135deg, rgba(0, 240, 255, 0.06), rgba(180, 74, 255, 0.06)); flex-shrink: 0; }
.cat-skeleton__info { flex: 1; display: flex; flex-direction: column; gap: 8px; min-width: 0; }
.cat-skeleton__line { border-radius: 6px; background: rgba(255, 255, 255, 0.04); }
.cat-skeleton__line--title { width: 55%; height: 14px; }
.cat-skeleton__line--desc { width: 80%; height: 10px; }
.cat-skeleton__right { display: flex; align-items: center; gap: 12px; flex-shrink: 0; }
.cat-skeleton__badge { width: 72px; height: 24px; border-radius: 20px; background: rgba(255, 255, 255, 0.03); }
.cat-skeleton__arrow { width: 18px; height: 18px; border-radius: 4px; background: rgba(255, 255, 255, 0.03); }
@keyframes skeletonSlideIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
@keyframes skeletonPulse { 0%, 100% { opacity: 0.6; border-color: rgba(255, 255, 255, 0.04); } 50% { opacity: 1; border-color: rgba(0, 240, 255, 0.12); box-shadow: 0 0 16px rgba(0, 240, 255, 0.04), inset 0 0 20px rgba(0, 240, 255, 0.02); } }

/* ================================================================
   CATEGORY CARD
   ================================================================ */
.cat-card-full { display: flex; align-items: center; gap: 16px; padding: 16px 20px; background: var(--clr-surface-light, #1a1530); border: 1px solid var(--clr-border, rgba(255,255,255,0.06)); border-radius: 12px; text-decoration: none; transition: all 250ms ease; animation: cardReveal 400ms ease both; }
@keyframes cardReveal { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
.cat-card-full:hover { border-color: rgba(0, 240, 255, 0.2); box-shadow: 0 4px 24px rgba(0, 240, 255, 0.05); transform: translateX(4px); }
.cat-card-full__icon { width: 52px; height: 52px; border-radius: 12px; background: color-mix(in srgb, var(--cat-accent, #00f0ff) 10%, transparent); border: 1px solid color-mix(in srgb, var(--cat-accent, #00f0ff) 20%, transparent); display: flex; align-items: center; justify-content: center; flex-shrink: 0; transition: all 250ms ease; }
.cat-card-full:hover .cat-card-full__icon { background: color-mix(in srgb, var(--cat-accent, #00f0ff) 15%, transparent); box-shadow: 0 0 16px color-mix(in srgb, var(--cat-accent, #00f0ff) 20%, transparent); }
.cat-card-full__info { flex: 1; min-width: 0; }
.cat-card-full__name { font-family: var(--font-ui, sans-serif); font-size: 0.92rem; font-weight: 700; color: #e8e6f0; margin-bottom: 2px; }
.cat-card-full__desc { font-family: var(--font-ui, sans-serif); font-size: 0.75rem; color: #8b85a6; line-height: 1.4; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.cat-card-full__right { display: flex; align-items: center; gap: 12px; flex-shrink: 0; }
.cat-card-full__count { font-family: var(--font-mono, monospace); font-size: 0.68rem; color: #8b85a6; padding: 4px 10px; background: rgba(255, 255, 255, 0.03); border: 1px solid var(--clr-border, rgba(255,255,255,0.06)); border-radius: 20px; white-space: nowrap; }
.cat-card-full__arrow { width: 18px; height: 18px; color: #8b85a6; transition: all 250ms ease; }
.cat-card-full:hover .cat-card-full__arrow { color: #00f0ff; transform: translateX(3px); }

@keyframes fadeInUp { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }

/* ================================================================
   RESPONSIVE
   ================================================================ */
@media (max-width: 639px) {
    .cat-card-full, .cat-skeleton { padding: 14px 16px; gap: 12px; }
    .cat-card-full__icon, .cat-skeleton__icon { width: 44px; height: 44px; border-radius: 10px; }
    .cat-card-full__icon svg { width: 22px; height: 22px; }
    .cat-card-full__desc, .cat-skeleton__line--desc { display: none; }
    .cat-card-full__count, .cat-skeleton__badge { display: none; }
}
</style>