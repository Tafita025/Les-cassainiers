<script setup>
import { ref, provide, computed, watch } from 'vue'
import { Link, usePage, useForm } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import HomeLayout from '@/layouts/home/HomeLayout.vue'
import AuthModal from '@/components/home/AuthModal.vue'

/* ================================================================
   PROPS
   ================================================================ */
const props = defineProps({
    products: { type: Array, default: () => [] },
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
   AUTH
   ================================================================ */
const page = usePage()
const user = computed(() => page.props.auth?.user)
const isLoggedIn = computed(() => !!user.value)

/* ================================================================
   MODAL DÉTAIL AVIS
   ================================================================ */
const showReviewModal = ref(false)
const selectedProduct = ref(null)
const productReviews = ref([])
const reviewDistribution = ref({})
const reviewAverage = ref(0)
const reviewTotal = ref(0)
const userExistingReview = ref(null)
const loadingReviews = ref(false)

async function openReviews(product) {
    selectedProduct.value = product
    showReviewModal.value = true
    loadingReviews.value = true
    showRatingForm.value = false

    try {
        const res = await fetch(route('home.review.show', product.id))
        const data = await res.json()
        productReviews.value = data.reviews
        reviewDistribution.value = data.distribution
        reviewAverage.value = data.average
        reviewTotal.value = data.total
        userExistingReview.value = data.userReview

        if (data.userReview) {
            ratingForm.rating = data.userReview.rating
            ratingForm.comment = data.userReview.comment || ''
        } else {
            ratingForm.reset()
        }
    } catch (e) {
        console.error('Erreur chargement avis:', e)
    } finally {
        loadingReviews.value = false
    }
}

function closeReviews() {
    showReviewModal.value = false
    selectedProduct.value = null
    productReviews.value = []
    showRatingForm.value = false
}


/* ================================================================
   FORMULAIRE NOTATION
   ================================================================ */
const showRatingForm = ref(false)
const hoverRating = ref(0)

const ratingForm = useForm({
    rating: 0,
    comment: '',
})

// Plus besoin de vérifier isLoggedIn ici — le middleware s'en charge
function handleRate() {
    showRatingForm.value = true
}

function setRating(n) {
    ratingForm.rating = n
}

function submitRating() {
    if (!selectedProduct.value || ratingForm.rating === 0) return

    ratingForm.post(route('home.review.store', selectedProduct.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            openReviews(selectedProduct.value)
            showRatingForm.value = false
        },
    })
}

/* ================================================================
   HELPERS
   ================================================================ */
function getBarWidth(count) {
    if (reviewTotal.value === 0) return '0%'
    return Math.round((count / reviewTotal.value) * 100) + '%'
}
/* ================================================================
   WATCHER — Fermer le modal review si auth requise
   ================================================================ */
watch(
    () => page.props.flash?.authRequired,
    (required) => {
        if (required) {
            closeReviews()
        }
    }
)
</script>

<template>
    <HomeLayout page-title="Notes">

        <AuthModal />

        <!-- ============================================================
             MODAL AVIS PRODUIT
             ============================================================ -->
        <Teleport to="body">
            <div v-if="showReviewModal && selectedProduct" class="rm-backdrop" @click.self="closeReviews">
                <div class="rm" @click.stop>

                    <button class="rm__close" @click="closeReviews">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                    </button>

                    <!-- Header produit -->
                    <div class="rm__header">
                        <img :src="selectedProduct.image" :alt="selectedProduct.name" class="rm__product-img" />
                        <div class="rm__product-info">
                            <span class="rm__brand">{{ selectedProduct.brand }}</span>
                            <h3 class="rm__product-name">{{ selectedProduct.name }}</h3>
                            <span class="price" style="font-size:0.9rem;">{{ selectedProduct.price }}</span>
                        </div>
                    </div>

                    <!-- Loading -->
                    <div v-if="loadingReviews" class="rm__loading">
                        <div class="rm__spinner"></div>
                        <span>Chargement des avis...</span>
                    </div>

                    <!-- Contenu -->
                    <div v-else class="rm__body">

                        <!-- Stats -->
                        <div class="rm__stats">
                            <div class="rm__stats-left">
                                <span class="rm__big-rating">{{ reviewAverage }}</span>
                                <div class="stars-row">
                                    <svg v-for="n in 5" :key="n" :class="['h-4 w-4', n <= Math.round(reviewAverage) ? 'star-filled' : 'star-empty']" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                </div>
                                <span class="rm__total-count">{{ reviewTotal }} avis</span>
                            </div>
                            <div class="rm__stats-right">
                                <div v-for="star in [5,4,3,2,1]" :key="star" class="rm__bar-row">
                                    <span class="rm__bar-label">{{ star }}</span>
                                    <svg class="h-3 w-3 star-filled" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                    <div class="rm__bar-track">
                                        <div class="rm__bar-fill" :style="{ width: getBarWidth(reviewDistribution[star] || 0) }"></div>
                                    </div>
                                    <span class="rm__bar-count">{{ reviewDistribution[star] || 0 }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Bouton noter -->
                        <div class="rm__rate-section">
                            <button v-if="!showRatingForm" class="btn-cyber btn-accent rm__rate-btn" @click="handleRate">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/></svg>
                                {{ userExistingReview ? 'Modifier mon avis' : 'Donner mon avis' }}
                            </button>

                            <!-- Formulaire notation -->
                            <div v-else class="rm__form">
                                <p class="rm__form-title">{{ userExistingReview ? 'Modifier votre avis' : 'Votre avis' }}</p>

                                <div class="rm__form-stars">
                                    <button
                                        v-for="n in 5" :key="n"
                                        type="button"
                                        class="rm__star-btn"
                                        @mouseenter="hoverRating = n"
                                        @mouseleave="hoverRating = 0"
                                        @click="setRating(n)"
                                    >
                                        <svg
                                            :class="['h-7 w-7', (hoverRating || ratingForm.rating) >= n ? 'star-filled' : 'star-empty']"
                                            fill="currentColor" viewBox="0 0 24 24"
                                        ><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                    </button>
                                    <span v-if="ratingForm.rating" class="rm__star-label">
                                        {{ ['', 'Mauvais', 'Passable', 'Correct', 'Bien', 'Excellent'][ratingForm.rating] }}
                                    </span>
                                </div>
                                <span v-if="ratingForm.errors.rating" class="rm__field-error">{{ ratingForm.errors.rating }}</span>

                                <textarea
                                    v-model="ratingForm.comment"
                                    class="input-cyber rm__textarea"
                                    placeholder="Votre commentaire (optionnel)..."
                                    rows="3"
                                    maxlength="1000"
                                ></textarea>
                                <span class="rm__char-count">{{ ratingForm.comment.length }}/1000</span>

                                <div class="rm__form-actions">
                                    <button type="button" class="btn-cyber rm__cancel-btn" @click="showRatingForm = false">Annuler</button>
                                    <button
                                        type="button"
                                        class="btn-cyber btn-primary rm__submit-btn"
                                        :disabled="ratingForm.processing || ratingForm.rating === 0"
                                        @click="submitRating"
                                    >
                                        <svg v-if="ratingForm.processing" class="h-4 w-4 rm__spin" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182"/></svg>
                                        {{ ratingForm.processing ? 'Envoi...' : 'Publier' }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="rm__divider"></div>

                        <!-- Liste des avis -->
                        <div class="rm__list">
                            <p class="rm__list-title">Avis des clients ({{ reviewTotal }})</p>

                            <div v-if="productReviews.length === 0" class="rm__empty">
                                <svg class="h-10 w-10 text-[#8b85a6]" style="opacity:0.3;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z"/></svg>
                                <p>Aucun avis pour ce produit.</p>
                            </div>

                            <div v-for="review in productReviews" :key="review.id" class="rm__review">
                                <div class="rm__review-header">
                                    <div class="rm__review-avatar">
                                        <span>{{ review.user_initials }}</span>
                                    </div>
                                    <div class="rm__review-meta">
                                        <span class="rm__review-name">{{ review.user_name }}</span>
                                        <span class="rm__review-date">{{ review.date }}</span>
                                    </div>
                                    <div class="stars-row" style="margin-left:auto;">
                                        <svg v-for="n in 5" :key="n" :class="['h-3.5 w-3.5', n <= review.rating ? 'star-filled' : 'star-empty']" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                    </div>
                                </div>
                                <p v-if="review.comment" class="rm__review-comment">{{ review.comment }}</p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </Teleport>

        <!-- ============================================================
             CONTENU PAGE
             ============================================================ -->
        <main class="main-content">

            <section class="section">
                <div class="section__header">
                    <div>
                        <h1 class="hero__title" style="font-size: 1.8rem; margin-bottom: 8px;">
                            Avis &amp; <span class="text-gradient-cyber">Notes</span>
                        </h1>
                        <p class="font-ui text-sm text-[#8b85a6]">Découvrez les retours de nos clients sur chaque produit</p>
                    </div>
                </div>
            </section>

            <section class="section">
                <div v-if="products.length === 0" class="rv-empty">
                    <svg class="h-12 w-12 text-[#8b85a6]" style="opacity:0.4;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z"/></svg>
                    <p class="font-ui text-sm text-[#8b85a6]">Aucun produit noté pour le moment.</p>
                </div>

                <div v-else class="rv-grid">
                    <div
                        v-for="product in products"
                        :key="product.id"
                        class="rv-card"
                        @click="openReviews(product)"
                    >
                        <div class="rv-card__img-wrapper">
                            <img :src="product.image" :alt="product.name" class="rv-card__img" />
                        </div>
                        <div class="rv-card__info">
                            <span class="rv-card__brand">{{ product.brand }}</span>
                            <h3 class="rv-card__name">{{ product.name }}</h3>
                            <div class="rv-card__rating">
                                <div class="rv-card__rating-big">
                                    <span class="rv-card__rating-number">{{ product.rating }}.0</span>
                                    <div class="stars-row">
                                        <svg v-for="n in 5" :key="n" :class="['h-4 w-4', n <= product.rating ? 'star-filled' : 'star-empty']" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                    </div>
                                </div>
                                <span class="rv-card__review-count">{{ product.reviews }} avis</span>
                            </div>
                            <div class="rv-card__footer">
                                <span class="price" style="font-size:0.85rem;">{{ product.price }}</span>
                                <span class="rv-card__see-btn">
                                    Voir les avis
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

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
.rv-empty { display: flex; flex-direction: column; align-items: center; gap: 12px; padding: 60px 20px; text-align: center; }
.rv-grid { display: grid; grid-template-columns: 1fr; gap: 16px; }
@media (min-width: 640px) { .rv-grid { grid-template-columns: repeat(2, 1fr); } }
@media (min-width: 1024px) { .rv-grid { grid-template-columns: repeat(3, 1fr); } }
.rv-card { display: flex; gap: 16px; padding: 16px; background: var(--clr-surface-light, #1a1530); border: 1px solid var(--clr-border, rgba(255,255,255,0.06)); border-radius: 14px; cursor: pointer; transition: all 250ms ease; }
.rv-card:hover { border-color: rgba(0, 240, 255, 0.15); box-shadow: 0 4px 20px rgba(0, 240, 255, 0.04); transform: translateY(-2px); }
.rv-card__img-wrapper { width: 90px; height: 90px; border-radius: 10px; background: linear-gradient(135deg, rgba(0, 240, 255, 0.03), rgba(180, 74, 255, 0.03)); border: 1px solid rgba(255,255,255,0.04); display: flex; align-items: center; justify-content: center; flex-shrink: 0; padding: 8px; }
.rv-card__img { max-width: 100%; max-height: 100%; object-fit: contain; opacity: 0.85; transition: opacity 200ms; }
.rv-card:hover .rv-card__img { opacity: 1; }
.rv-card__info { flex: 1; min-width: 0; display: flex; flex-direction: column; gap: 6px; }
.rv-card__brand { font-family: var(--font-mono, monospace); font-size: 0.6rem; color: #8b85a6; text-transform: uppercase; letter-spacing: 0.06em; }
.rv-card__name { font-family: var(--font-ui, sans-serif); font-size: 0.82rem; font-weight: 700; color: #e8e6f0; line-height: 1.3; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.rv-card__rating { display: flex; align-items: center; gap: 12px; }
.rv-card__rating-big { display: flex; align-items: center; gap: 6px; }
.rv-card__rating-number { font-family: var(--font-display, 'Orbitron', sans-serif); font-size: 1.1rem; font-weight: 800; color: #00f0ff; }
.rv-card__review-count { font-family: var(--font-mono, monospace); font-size: 0.6rem; color: #8b85a6; }
.rv-card__footer { display: flex; align-items: center; justify-content: space-between; margin-top: auto; }
.rv-card__see-btn { display: flex; align-items: center; gap: 4px; font-family: var(--font-mono, monospace); font-size: 0.62rem; color: #00f0ff; opacity: 0; transform: translateX(-4px); transition: all 200ms ease; }
.rv-card:hover .rv-card__see-btn { opacity: 1; transform: translateX(0); }

/* MODAL */
.rm-backdrop { position: fixed; inset: 0; z-index: 100; background: rgba(5, 2, 18, 0.85); backdrop-filter: blur(12px); display: flex; align-items: center; justify-content: center; padding: 16px; animation: fadeIn 200ms ease; }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
.rm { position: relative; background: var(--clr-surface-light, #1a1530); border: 1px solid var(--clr-border, rgba(255,255,255,0.06)); border-radius: 16px; max-width: 560px; width: 100%; max-height: 90vh; overflow-y: auto; box-shadow: 0 25px 60px rgba(0, 0, 0, 0.5), 0 0 40px rgba(0, 240, 255, 0.05); animation: modalSlideUp 300ms ease; }
@keyframes modalSlideUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
.rm__close { position: absolute; top: 12px; right: 12px; z-index: 10; width: 36px; height: 36px; border-radius: 50%; background: rgba(0, 0, 0, 0.4); border: 1px solid rgba(255,255,255,0.06); color: #e8e6f0; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 200ms ease; }
.rm__close:hover { background: rgba(255, 45, 149, 0.2); color: #ff2d95; }
.rm__header { display: flex; align-items: center; gap: 16px; padding: 20px 24px; background: linear-gradient(135deg, rgba(0, 240, 255, 0.03), rgba(180, 74, 255, 0.03)); border-bottom: 1px solid rgba(255,255,255,0.06); border-radius: 16px 16px 0 0; }
.rm__product-img { width: 64px; height: 64px; object-fit: contain; border-radius: 8px; }
.rm__product-info { flex: 1; min-width: 0; }
.rm__brand { font-family: var(--font-mono, monospace); font-size: 0.6rem; color: #8b85a6; text-transform: uppercase; letter-spacing: 0.06em; display: block; margin-bottom: 2px; }
.rm__product-name { font-family: var(--font-ui, sans-serif); font-size: 0.9rem; font-weight: 700; color: #e8e6f0; line-height: 1.3; margin-bottom: 4px; }
.rm__loading { display: flex; flex-direction: column; align-items: center; gap: 12px; padding: 40px; font-family: var(--font-ui); font-size: 0.8rem; color: #8b85a6; }
.rm__spinner { width: 28px; height: 28px; border: 2px solid rgba(0, 240, 255, 0.15); border-top-color: #00f0ff; border-radius: 50%; animation: spin 0.7s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }
.rm__body { padding: 20px 24px; display: flex; flex-direction: column; gap: 16px; }
.rm__stats { display: flex; gap: 24px; align-items: flex-start; }
.rm__stats-left { display: flex; flex-direction: column; align-items: center; gap: 4px; min-width: 80px; }
.rm__big-rating { font-family: var(--font-display, 'Orbitron', sans-serif); font-size: 2.2rem; font-weight: 900; color: #00f0ff; line-height: 1; }
.rm__total-count { font-family: var(--font-mono, monospace); font-size: 0.6rem; color: #8b85a6; }
.rm__stats-right { flex: 1; display: flex; flex-direction: column; gap: 5px; }
.rm__bar-row { display: flex; align-items: center; gap: 6px; }
.rm__bar-label { font-family: var(--font-mono, monospace); font-size: 0.65rem; color: #8b85a6; width: 10px; text-align: right; }
.rm__bar-track { flex: 1; height: 6px; background: rgba(255,255,255,0.04); border-radius: 3px; overflow: hidden; }
.rm__bar-fill { height: 100%; background: linear-gradient(90deg, #00f0ff, #b44aff); border-radius: 3px; transition: width 500ms ease; }
.rm__bar-count { font-family: var(--font-mono, monospace); font-size: 0.6rem; color: #8b85a6; width: 20px; }
.rm__rate-section { display: flex; flex-direction: column; }
.rm__rate-btn { display: flex; align-items: center; gap: 8px; padding: 10px 20px; font-size: 0.82rem; width: 100%; justify-content: center; }
.rm__form { padding: 16px; background: rgba(0, 240, 255, 0.03); border: 1px solid rgba(0, 240, 255, 0.08); border-radius: 12px; display: flex; flex-direction: column; gap: 12px; }
.rm__form-title { font-family: var(--font-ui); font-size: 0.85rem; font-weight: 700; color: #e8e6f0; }
.rm__form-stars { display: flex; align-items: center; gap: 4px; }
.rm__star-btn { background: none; border: none; cursor: pointer; padding: 2px; transition: transform 150ms; }
.rm__star-btn:hover { transform: scale(1.2); }
.rm__star-label { font-family: var(--font-ui); font-size: 0.75rem; color: #00f0ff; margin-left: 8px; font-weight: 600; }
.rm__field-error { font-family: var(--font-ui); font-size: 0.7rem; color: #ff2d95; }
.rm__textarea { resize: vertical; min-height: 70px; font-size: 0.82rem; }
.rm__char-count { font-family: var(--font-mono); font-size: 0.55rem; color: #8b85a6; text-align: right; margin-top: -8px; }
.rm__form-actions { display: flex; gap: 10px; justify-content: flex-end; }
.rm__cancel-btn { padding: 8px 16px; font-size: 0.78rem; background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); color: #8b85a6; }
.rm__cancel-btn:hover { background: rgba(255,255,255,0.08); color: #e8e6f0; }
.rm__submit-btn { padding: 8px 20px; font-size: 0.78rem; display: flex; align-items: center; gap: 6px; }
.rm__submit-btn:disabled { opacity: 0.5; cursor: not-allowed; }
.rm__spin { animation: spin 0.8s linear infinite; }
.rm__divider { height: 1px; background: linear-gradient(to right, transparent, rgba(255,255,255,0.06), transparent); }
.rm__list { display: flex; flex-direction: column; gap: 12px; }
.rm__list-title { font-family: var(--font-ui); font-size: 0.82rem; font-weight: 700; color: #e8e6f0; }
.rm__empty { display: flex; flex-direction: column; align-items: center; gap: 8px; padding: 24px; font-family: var(--font-ui); font-size: 0.8rem; color: #8b85a6; text-align: center; }
.rm__review { padding: 14px; background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.04); border-radius: 10px; transition: all 200ms; }
.rm__review:hover { border-color: rgba(255,255,255,0.08); }
.rm__review-header { display: flex; align-items: center; gap: 10px; margin-bottom: 8px; }
.rm__review-avatar { width: 32px; height: 32px; border-radius: 8px; background: linear-gradient(135deg, rgba(0, 240, 255, 0.12), rgba(180, 74, 255, 0.12)); border: 1px solid rgba(0, 240, 255, 0.2); display: flex; align-items: center; justify-content: center; font-family: var(--font-display, 'Orbitron'); font-size: 0.55rem; font-weight: 800; color: #00f0ff; flex-shrink: 0; }
.rm__review-meta { display: flex; flex-direction: column; gap: 0; }
.rm__review-name { font-family: var(--font-ui); font-size: 0.78rem; font-weight: 600; color: #e8e6f0; }
.rm__review-date { font-family: var(--font-mono); font-size: 0.55rem; color: #8b85a6; }
.rm__review-comment { font-family: var(--font-ui); font-size: 0.8rem; color: #8b85a6; line-height: 1.6; }

@media (max-width: 639px) {
    .rm__stats { flex-direction: column; align-items: center; }
    .rm__stats-right { width: 100%; }
    .rv-card { flex-direction: column; align-items: center; text-align: center; }
    .rv-card__img-wrapper { width: 100%; height: 120px; }
    .rv-card__footer { justify-content: center; gap: 16px; }
    .rv-card__see-btn { opacity: 1; transform: none; }
}
</style>