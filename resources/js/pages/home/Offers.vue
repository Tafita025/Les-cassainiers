<script setup>
import { ref, provide, onMounted, onUnmounted } from 'vue'
import { Link } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import HomeLayout from '@/layouts/home/HomeLayout.vue'
import AuthModal from '@/components/home/AuthModal.vue'

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
   MODAL DÉTAIL OFFRE
   ================================================================ */
const showOfferModal = ref(false)
const selectedOffer = ref({
    name: '',
    brand: '',
    description: '',
    image: '',
    offerPrice: '',
    originalPrice: '',
    discountPercent: '',
    rating: 0,
    reviews: 0,
})
const quantity = ref(1)

function openOffer(data) {
    selectedOffer.value = data
    quantity.value = 1
    showOfferModal.value = true
}

function closeOffer() {
    showOfferModal.value = false
}

function increaseQty() {
    if (quantity.value < 10) quantity.value++
}
function decreaseQty() {
    if (quantity.value > 1) quantity.value--
}

/* ================================================================
   COUNTDOWN — Timer par offre (remplacé par la BDD plus tard)
   ================================================================ */
const days = ref(0)
const hours = ref(0)
const minutes = ref(0)
const seconds = ref(0)
let countdownInterval = null

function startCountdown() {
    const endDate = new Date()
    endDate.setDate(endDate.getDate() + 3)
    endDate.setHours(endDate.getHours() + 5)

    countdownInterval = setInterval(() => {
        const now = new Date().getTime()
        const diff = endDate.getTime() - now

        if (diff <= 0) {
            clearInterval(countdownInterval)
            days.value = 0
            hours.value = 0
            minutes.value = 0
            seconds.value = 0
            return
        }

        days.value = Math.floor(diff / (1000 * 60 * 60 * 24))
        hours.value = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))
        minutes.value = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60))
        seconds.value = Math.floor((diff % (1000 * 60)) / 1000)
    }, 1000)
}

onMounted(() => startCountdown())
onUnmounted(() => {
    if (countdownInterval) clearInterval(countdownInterval)
})

function pad(n) {
    return String(n).padStart(2, '0')
}
</script>

<template>
    <HomeLayout page-title="Offres Flash">

        <!-- Modal Auth (composant séparé) -->
        <AuthModal />

        <!-- ============================================================
             MODAL DÉTAIL OFFRE
             ============================================================ -->
        <Teleport to="body">
            <div
                v-if="showOfferModal"
                class="offer-modal-backdrop"
                @click.self="closeOffer"
            >
                <div class="offer-modal" @click.stop>
                    <button class="offer-modal__close" @click="closeOffer">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                    </button>

                    <!-- Image -->
                    <div class="offer-modal__image-wrapper">
                        <div class="offer-modal__discount-tag">{{ selectedOffer.discountPercent }}</div>
                        <img :src="selectedOffer.image" :alt="selectedOffer.name" class="offer-modal__image" />
                    </div>

                    <!-- Infos -->
                    <div class="offer-modal__info">
                        <p class="offer-modal__brand">{{ selectedOffer.brand }}</p>
                        <h2 class="offer-modal__name">{{ selectedOffer.name }}</h2>

                        <div class="offer-modal__rating">
                            <div class="stars-row">
                                <template v-for="n in 5" :key="n">
                                    <svg :class="['h-4 w-4', n <= selectedOffer.rating ? 'star-filled' : 'star-empty']" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                </template>
                            </div>
                            <span class="offer-modal__reviews">({{ selectedOffer.reviews }} avis)</span>
                        </div>

                        <p class="offer-modal__description">{{ selectedOffer.description }}</p>

                        <!-- Timer -->
                        <div class="offer-modal__timer">
                            <svg class="h-4 w-4 text-[#ff2d95]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                            <span class="offer-modal__timer-label">Offre expire dans</span>
                            <div class="offer-modal__timer-cells">
                                <span class="offer-modal__timer-cell">{{ pad(days) }}j</span>
                                <span class="offer-modal__timer-cell">{{ pad(hours) }}h</span>
                                <span class="offer-modal__timer-cell">{{ pad(minutes) }}m</span>
                                <span class="offer-modal__timer-cell">{{ pad(seconds) }}s</span>
                            </div>
                        </div>

                        <!-- Prix -->
                        <div class="offer-modal__prices">
                            <span class="price text-xl">{{ selectedOffer.offerPrice }}</span>
                            <span class="price-old text-sm">{{ selectedOffer.originalPrice }}</span>
                            <span class="offer-modal__percent">{{ selectedOffer.discountPercent }}</span>
                        </div>

                        <!-- Quantité + Panier -->
                        <div class="offer-modal__actions">
                            <div class="offer-modal__qty">
                                <button class="offer-modal__qty-btn" @click="decreaseQty">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14"/></svg>
                                </button>
                                <span class="offer-modal__qty-value">{{ quantity }}</span>
                                <button class="offer-modal__qty-btn" @click="increaseQty">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                </button>
                            </div>
                            <button class="btn-cyber btn-accent offer-modal__cart-btn">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z"/></svg>
                                Profiter de l'offre
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </Teleport>

        <!-- ============================================================
             CONTENU PAGE
             ============================================================ -->
        <main class="main-content">

            <!-- ====================================================
                 HERO — Bannière Offres Flash
                 ==================================================== -->
            <section class="offer-hero animate-fade-in-up">
                <div class="offer-hero__glow offer-hero__glow--pink"></div>
                <div class="offer-hero__glow offer-hero__glow--purple"></div>
                <div class="offer-hero__inner">
                    <div class="offer-hero__text">
                        <span class="badge badge-pink" style="margin-bottom: 16px;">
                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z"/></svg>
                            Offres Flash
                        </span>
                        <h1 class="offer-hero__title">
                            Jusqu'à <span class="offer-hero__highlight">-40%</span> sur une sélection
                        </h1>
                        <p class="offer-hero__subtitle">
                            Offres limitées dans le temps sur les meilleurs composants. Chaque offre a sa propre durée — dépêchez-vous avant qu'il ne soit trop tard !
                        </p>
                    </div>
                    <div class="offer-hero__icon-wrapper">
                        <svg class="h-16 w-16 text-[#ff2d95]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="m3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z"/></svg>
                    </div>
                </div>
            </section>

            <!-- ====================================================
                 SECTION : Offres en cours
                 ==================================================== -->
            <section class="section">
                <div class="section__header">
                    <h2 class="section__title">Offres en cours</h2>
                    <span class="badge badge-green">
                        <span style="width:6px;height:6px;border-radius:50%;background:#39ff14;" class="animate-pulse"></span>
                        6 offres actives
                    </span>
                </div>

                <div class="offer-grid">

                    <!-- ========== OFFRE 1 : RTX 4070 Ti Super ========== -->
                    <div class="offer-card group" @click="openOffer({ name: 'NVIDIA GeForce RTX 4070 Ti Super — 16 Go GDDR6X', brand: 'NVIDIA', description: 'Carte graphique haut de gamme avec 16 Go de GDDR6X. Architecture Ada Lovelace, DLSS 3.5, ray tracing 3e gen. Parfaite pour le gaming 1440p ultra et le 4K.', image: '/images/products/rtx-4070ti-super.png', offerPrice: '4 395 000 Ar', originalPrice: '5 495 000 Ar', discountPercent: '-20%', rating: 5, reviews: 89 })">
                        <div class="offer-card__image-wrapper">
                            <div class="offer-card__discount">-20%</div>
                            <img src="/images/products/rtx-4070ti-super.png" alt="NVIDIA GeForce RTX 4070 Ti Super" class="offer-card__image" />
                        </div>
                        <div class="offer-card__body">
                            <p class="offer-card__brand">NVIDIA</p>
                            <h3 class="offer-card__name">NVIDIA GeForce RTX 4070 Ti Super — 16 Go GDDR6X</h3>
                            <div class="stars-row" style="margin-bottom: 8px;">
                                <template v-for="n in 5" :key="n"><svg class="star-filled h-3.5 w-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg></template>
                                <span class="font-mono text-[0.6rem] text-[#8b85a6]" style="margin-left: 4px;">(89)</span>
                            </div>
                            <div class="offer-card__prices">
                                <span class="price text-base">4 395 000 Ar</span>
                                <span class="price-old text-xs">5 495 000 Ar</span>
                            </div>
                            <div class="offer-card__timer">
                                <svg class="h-3 w-3 text-[#ff2d95]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                <span>{{ pad(days) }}j {{ pad(hours) }}h {{ pad(minutes) }}m {{ pad(seconds) }}s</span>
                            </div>
                        </div>
                    </div>

                    <!-- ========== OFFRE 2 : AMD Ryzen 7 7800X3D ========== -->
                    <div class="offer-card group" @click="openOffer({ name: 'AMD Ryzen 7 7800X3D — 8 Cores / 16 Threads', brand: 'AMD', description: 'Le meilleur processeur gaming grâce à la technologie 3D V-Cache. 8 cœurs, 16 threads, 104 Mo de cache total, boost à 5.0 GHz. Socket AM5.', image: '/images/products/ryzen-7-7800x3d.png', offerPrice: '1 895 000 Ar', originalPrice: '2 495 000 Ar', discountPercent: '-24%', rating: 5, reviews: 312 })">
                        <div class="offer-card__image-wrapper">
                            <div class="offer-card__discount">-24%</div>
                            <img src="/images/products/ryzen-7-7800x3d.png" alt="AMD Ryzen 7 7800X3D" class="offer-card__image" />
                        </div>
                        <div class="offer-card__body">
                            <p class="offer-card__brand">AMD</p>
                            <h3 class="offer-card__name">AMD Ryzen 7 7800X3D — 8 Cores / 16 Threads</h3>
                            <div class="stars-row" style="margin-bottom: 8px;">
                                <template v-for="n in 5" :key="n"><svg class="star-filled h-3.5 w-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg></template>
                                <span class="font-mono text-[0.6rem] text-[#8b85a6]" style="margin-left: 4px;">(312)</span>
                            </div>
                            <div class="offer-card__prices">
                                <span class="price text-base">1 895 000 Ar</span>
                                <span class="price-old text-xs">2 495 000 Ar</span>
                            </div>
                            <div class="offer-card__timer">
                                <svg class="h-3 w-3 text-[#ff2d95]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                <span>{{ pad(days) }}j {{ pad(hours) }}h {{ pad(minutes) }}m {{ pad(seconds) }}s</span>
                            </div>
                        </div>
                    </div>

                    <!-- ========== OFFRE 3 : Samsung 990 PRO 2 To ========== -->
                    <div class="offer-card group" @click="openOffer({ name: 'Samsung 990 PRO NVMe M.2 — 2 To', brand: 'SAMSUNG', description: 'SSD NVMe M.2 ultra rapide. Lecture séquentielle jusqu\'à 7 450 Mo/s, écriture 6 900 Mo/s. Contrôleur Pascal, NAND V8 TLC. PCIe Gen 4.0 x4.', image: '/images/products/samsung-990-pro-2to.png', offerPrice: '695 000 Ar', originalPrice: '995 000 Ar', discountPercent: '-30%', rating: 4, reviews: 245 })">
                        <div class="offer-card__image-wrapper">
                            <div class="offer-card__discount">-30%</div>
                            <img src="/images/products/samsung-990-pro-2to.png" alt="Samsung 990 PRO 2 To" class="offer-card__image" />
                        </div>
                        <div class="offer-card__body">
                            <p class="offer-card__brand">SAMSUNG</p>
                            <h3 class="offer-card__name">Samsung 990 PRO NVMe M.2 — 2 To</h3>
                            <div class="stars-row" style="margin-bottom: 8px;">
                                <template v-for="n in 5" :key="n"><svg :class="['h-3.5 w-3.5', n <= 4 ? 'star-filled' : 'star-empty']" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg></template>
                                <span class="font-mono text-[0.6rem] text-[#8b85a6]" style="margin-left: 4px;">(245)</span>
                            </div>
                            <div class="offer-card__prices">
                                <span class="price text-base">695 000 Ar</span>
                                <span class="price-old text-xs">995 000 Ar</span>
                            </div>
                            <div class="offer-card__timer">
                                <svg class="h-3 w-3 text-[#ff2d95]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                <span>{{ pad(days) }}j {{ pad(hours) }}h {{ pad(minutes) }}m {{ pad(seconds) }}s</span>
                            </div>
                        </div>
                    </div>

                    <!-- ========== OFFRE 4 : Corsair DDR5 32 Go ========== -->
                    <div class="offer-card group" @click="openOffer({ name: 'Corsair Vengeance DDR5 32 Go — 6000 MT/s', brand: 'CORSAIR', description: 'Kit mémoire DDR5 haute performance 32 Go (2x16 Go) à 6000 MT/s. Profils XMP 3.0, dissipateur aluminium compact. Compatible Intel et AMD.', image: '/images/products/corsair-vengeance-ddr5.png', offerPrice: '495 000 Ar', originalPrice: '695 000 Ar', discountPercent: '-29%', rating: 4, reviews: 178 })">
                        <div class="offer-card__image-wrapper">
                            <div class="offer-card__discount">-29%</div>
                            <img src="/images/products/corsair-vengeance-ddr5.png" alt="Corsair Vengeance DDR5 32 Go" class="offer-card__image" />
                        </div>
                        <div class="offer-card__body">
                            <p class="offer-card__brand">CORSAIR</p>
                            <h3 class="offer-card__name">Corsair Vengeance DDR5 32 Go — 6000 MT/s</h3>
                            <div class="stars-row" style="margin-bottom: 8px;">
                                <template v-for="n in 5" :key="n"><svg :class="['h-3.5 w-3.5', n <= 4 ? 'star-filled' : 'star-empty']" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg></template>
                                <span class="font-mono text-[0.6rem] text-[#8b85a6]" style="margin-left: 4px;">(178)</span>
                            </div>
                            <div class="offer-card__prices">
                                <span class="price text-base">495 000 Ar</span>
                                <span class="price-old text-xs">695 000 Ar</span>
                            </div>
                            <div class="offer-card__timer">
                                <svg class="h-3 w-3 text-[#ff2d95]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                <span>{{ pad(days) }}j {{ pad(hours) }}h {{ pad(minutes) }}m {{ pad(seconds) }}s</span>
                            </div>
                        </div>
                    </div>

                    <!-- ========== OFFRE 5 : ASUS ROG Swift PG27AQDM ========== -->
                    <div class="offer-card group" @click="openOffer({ name: 'ASUS ROG Swift PG27AQDM — 27&quot; OLED 240Hz', brand: 'ASUS', description: 'Moniteur gaming OLED 27 pouces 1440p. 240Hz, 0.03ms, HDR True Black 400. G-Sync compatible, DisplayPort 1.4 et HDMI 2.0.', image: '/images/products/asus-pg27aqdm.png', offerPrice: '3 995 000 Ar', originalPrice: '5 295 000 Ar', discountPercent: '-25%', rating: 5, reviews: 56 })">
                        <div class="offer-card__image-wrapper">
                            <div class="offer-card__discount">-25%</div>
                            <img src="/images/products/asus-pg27aqdm.png" alt="ASUS ROG Swift PG27AQDM" class="offer-card__image" />
                        </div>
                        <div class="offer-card__body">
                            <p class="offer-card__brand">ASUS</p>
                            <h3 class="offer-card__name">ASUS ROG Swift PG27AQDM — 27" OLED 240Hz</h3>
                            <div class="stars-row" style="margin-bottom: 8px;">
                                <template v-for="n in 5" :key="n"><svg class="star-filled h-3.5 w-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg></template>
                                <span class="font-mono text-[0.6rem] text-[#8b85a6]" style="margin-left: 4px;">(56)</span>
                            </div>
                            <div class="offer-card__prices">
                                <span class="price text-base">3 995 000 Ar</span>
                                <span class="price-old text-xs">5 295 000 Ar</span>
                            </div>
                            <div class="offer-card__timer">
                                <svg class="h-3 w-3 text-[#ff2d95]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                <span>{{ pad(days) }}j {{ pad(hours) }}h {{ pad(minutes) }}m {{ pad(seconds) }}s</span>
                            </div>
                        </div>
                    </div>

                    <!-- ========== OFFRE 6 : Logitech G915 TKL ========== -->
                    <div class="offer-card group" @click="openOffer({ name: 'Logitech G915 TKL Lightspeed — Clavier Mécanique', brand: 'LOGITECH', description: 'Clavier gaming mécanique sans fil ultra fin. Switches GL tactiles, Lightspeed sans fil, autonomie 40h, RGB par touche. Format TKL compact.', image: '/images/products/logitech-g915-tkl.png', offerPrice: '845 000 Ar', originalPrice: '1 195 000 Ar', discountPercent: '-29%', rating: 5, reviews: 203 })">
                        <div class="offer-card__image-wrapper">
                            <div class="offer-card__discount">-29%</div>
                            <img src="/images/products/logitech-g915-tkl.png" alt="Logitech G915 TKL Lightspeed" class="offer-card__image" />
                        </div>
                        <div class="offer-card__body">
                            <p class="offer-card__brand">LOGITECH</p>
                            <h3 class="offer-card__name">Logitech G915 TKL Lightspeed — Clavier Mécanique</h3>
                            <div class="stars-row" style="margin-bottom: 8px;">
                                <template v-for="n in 5" :key="n"><svg class="star-filled h-3.5 w-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg></template>
                                <span class="font-mono text-[0.6rem] text-[#8b85a6]" style="margin-left: 4px;">(203)</span>
                            </div>
                            <div class="offer-card__prices">
                                <span class="price text-base">845 000 Ar</span>
                                <span class="price-old text-xs">1 195 000 Ar</span>
                            </div>
                            <div class="offer-card__timer">
                                <svg class="h-3 w-3 text-[#ff2d95]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                <span>{{ pad(days) }}j {{ pad(hours) }}h {{ pad(minutes) }}m {{ pad(seconds) }}s</span>
                            </div>
                        </div>
                    </div>

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
/* ================================================================
   OFFER HERO
   ================================================================ */
.offer-hero {
    position: relative;
    border-radius: 16px;
    border: 1px solid var(--clr-border, rgba(255,255,255,0.06));
    background: linear-gradient(135deg, rgba(255,45,149,0.06), rgba(180,74,255,0.06));
    padding: 40px 32px;
    margin: 24px;
    overflow: hidden;
}
.offer-hero__glow {
    position: absolute;
    width: 300px;
    height: 300px;
    border-radius: 50%;
    filter: blur(120px);
    opacity: 0.4;
    pointer-events: none;
}
.offer-hero__glow--pink { top: -100px; right: -50px; background: #ff2d95; }
.offer-hero__glow--purple { bottom: -100px; left: -50px; background: #b44aff; }
.offer-hero__inner {
    position: relative;
    z-index: 1;
    display: flex;
    flex-direction: column;
    gap: 32px;
    align-items: flex-start;
}
@media (min-width: 768px) {
    .offer-hero__inner { flex-direction: row; align-items: center; justify-content: space-between; }
}
.offer-hero__text { max-width: 480px; }
.offer-hero__title {
    font-family: var(--font-display, 'Orbitron', sans-serif);
    font-size: 1.6rem;
    font-weight: 800;
    color: #e8e6f0;
    line-height: 1.3;
    margin-bottom: 12px;
}
@media (min-width: 768px) { .offer-hero__title { font-size: 2rem; } }
.offer-hero__highlight { color: #ff2d95; text-shadow: 0 0 20px rgba(255,45,149,0.5); }
.offer-hero__subtitle { font-family: var(--font-ui, sans-serif); font-size: 0.9rem; color: #8b85a6; line-height: 1.6; }
.offer-hero__icon-wrapper {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: rgba(255,45,149,0.08);
    border: 1px solid rgba(255,45,149,0.15);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

/* ================================================================
   OFFER GRID
   ================================================================ */
.offer-grid { display: grid; grid-template-columns: 1fr; gap: 20px; }
@media (min-width: 640px) { .offer-grid { grid-template-columns: repeat(2, 1fr); } }
@media (min-width: 1024px) { .offer-grid { grid-template-columns: repeat(3, 1fr); } }

/* ================================================================
   OFFER CARD
   ================================================================ */
.offer-card {
    position: relative;
    background: var(--clr-surface-light, #1a1530);
    border: 1px solid var(--clr-border, rgba(255,255,255,0.06));
    border-radius: 14px;
    overflow: hidden;
    cursor: pointer;
    transition: all 300ms ease;
}
.offer-card:hover {
    border-color: rgba(255,45,149,0.25);
    box-shadow: 0 8px 30px rgba(255,45,149,0.08), 0 0 20px rgba(255,45,149,0.04);
    transform: translateY(-3px);
}
.offer-card__image-wrapper {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 180px;
    padding: 16px;
    background: linear-gradient(135deg, rgba(255,45,149,0.04), rgba(180,74,255,0.04));
    border-bottom: 1px solid var(--clr-border, rgba(255,255,255,0.06));
}
.offer-card__image { max-height: 130px; max-width: 100%; object-fit: contain; transition: transform 300ms ease; }
.offer-card:hover .offer-card__image { transform: scale(1.06); }
.offer-card__discount {
    position: absolute; top: 10px; left: 10px;
    padding: 4px 10px;
    background: linear-gradient(135deg, #ff2d95, #b44aff);
    border-radius: 20px;
    font-family: var(--font-display, 'Orbitron', sans-serif);
    font-size: 0.7rem; font-weight: 700; color: #fff;
    letter-spacing: 0.02em;
    box-shadow: 0 2px 10px rgba(255,45,149,0.3);
}
.offer-card__body { padding: 16px; }
.offer-card__brand { font-family: var(--font-mono, monospace); font-size: 0.65rem; color: #8b85a6; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 4px; }
.offer-card__name { font-family: var(--font-ui, sans-serif); font-size: 0.85rem; font-weight: 600; color: #e8e6f0; line-height: 1.35; margin-bottom: 8px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.offer-card__prices { display: flex; align-items: center; gap: 8px; margin-bottom: 10px; }
.offer-card__timer {
    display: flex; align-items: center; gap: 6px;
    padding: 6px 10px;
    background: rgba(255,45,149,0.06);
    border: 1px solid rgba(255,45,149,0.12);
    border-radius: 8px;
    font-family: var(--font-mono, monospace);
    font-size: 0.68rem; color: #ff2d95;
}

/* ================================================================
   OFFER MODAL
   ================================================================ */
.offer-modal-backdrop {
    position: fixed; inset: 0; z-index: 100;
    background: rgba(5,2,18,0.85);
    backdrop-filter: blur(12px);
    display: flex; align-items: center; justify-content: center;
    padding: 16px;
    animation: fadeIn 200ms ease;
}
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
.offer-modal {
    position: relative;
    background: var(--clr-surface-light, #1a1530);
    border: 1px solid rgba(255,45,149,0.15);
    border-radius: 16px;
    max-width: 520px; width: 100%; max-height: 90vh; overflow-y: auto;
    box-shadow: 0 25px 60px rgba(0,0,0,0.5), 0 0 40px rgba(255,45,149,0.08);
    animation: modalSlideUp 300ms ease;
}
@keyframes modalSlideUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
.offer-modal__close {
    position: absolute; top: 12px; right: 12px; z-index: 10;
    width: 36px; height: 36px; border-radius: 50%;
    background: rgba(0,0,0,0.4);
    border: 1px solid var(--clr-border, rgba(255,255,255,0.06));
    color: #e8e6f0;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; transition: all 200ms ease;
}
.offer-modal__close:hover { background: rgba(255,45,149,0.2); color: #ff2d95; }
.offer-modal__image-wrapper {
    position: relative;
    display: flex; align-items: center; justify-content: center;
    padding: 32px 24px;
    background: linear-gradient(135deg, rgba(255,45,149,0.04), rgba(180,74,255,0.04));
    border-bottom: 1px solid var(--clr-border, rgba(255,255,255,0.06));
    border-radius: 16px 16px 0 0;
}
.offer-modal__image { max-height: 180px; max-width: 100%; object-fit: contain; }
.offer-modal__discount-tag {
    position: absolute; top: 12px; left: 12px;
    padding: 5px 14px;
    background: linear-gradient(135deg, #ff2d95, #b44aff);
    border-radius: 20px;
    font-family: var(--font-display, 'Orbitron', sans-serif);
    font-size: 0.8rem; font-weight: 700; color: #fff;
    box-shadow: 0 2px 12px rgba(255,45,149,0.35);
}
.offer-modal__info { padding: 24px; display: flex; flex-direction: column; gap: 12px; }
.offer-modal__brand { font-family: var(--font-mono, monospace); font-size: 0.7rem; color: #8b85a6; text-transform: uppercase; letter-spacing: 0.08em; }
.offer-modal__name { font-family: var(--font-ui, sans-serif); font-size: 1.1rem; font-weight: 700; color: #e8e6f0; line-height: 1.35; }
.offer-modal__rating { display: flex; align-items: center; gap: 8px; }
.offer-modal__reviews { font-family: var(--font-mono, monospace); font-size: 0.7rem; color: #8b85a6; }
.offer-modal__description { font-family: var(--font-ui, sans-serif); font-size: 0.82rem; color: #8b85a6; line-height: 1.6; }
.offer-modal__timer {
    display: flex; align-items: center; gap: 8px;
    padding: 10px 14px;
    background: rgba(255,45,149,0.06);
    border: 1px solid rgba(255,45,149,0.15);
    border-radius: 10px;
}
.offer-modal__timer-label { font-family: var(--font-ui, sans-serif); font-size: 0.75rem; color: #8b85a6; margin-right: auto; }
.offer-modal__timer-cells { display: flex; gap: 4px; }
.offer-modal__timer-cell {
    padding: 3px 8px;
    background: rgba(255,45,149,0.1);
    border-radius: 6px;
    font-family: var(--font-display, 'Orbitron', sans-serif);
    font-size: 0.72rem; font-weight: 700; color: #ff2d95;
}
.offer-modal__prices { display: flex; align-items: center; gap: 10px; }
.offer-modal__percent {
    padding: 2px 8px;
    background: linear-gradient(135deg, rgba(255,45,149,0.15), rgba(180,74,255,0.15));
    border-radius: 6px;
    font-family: var(--font-display, 'Orbitron', sans-serif);
    font-size: 0.7rem; font-weight: 700; color: #ff2d95;
}
.offer-modal__actions {
    display: flex; align-items: center; gap: 12px;
    padding-top: 12px;
    border-top: 1px solid var(--clr-border, rgba(255,255,255,0.06));
}
.offer-modal__qty {
    display: flex; align-items: center;
    border: 1px solid var(--clr-border, rgba(255,255,255,0.06));
    border-radius: 8px; overflow: hidden;
}
.offer-modal__qty-btn {
    width: 36px; height: 36px;
    display: flex; align-items: center; justify-content: center;
    background: rgba(255,255,255,0.03); color: #8b85a6;
    border: none; cursor: pointer; transition: all 150ms ease;
}
.offer-modal__qty-btn:hover { background: rgba(255,45,149,0.1); color: #ff2d95; }
.offer-modal__qty-value { width: 40px; text-align: center; font-family: var(--font-ui, sans-serif); font-size: 0.85rem; font-weight: 600; color: #e8e6f0; }
.offer-modal__cart-btn { flex: 1; display: flex; align-items: center; justify-content: center; gap: 8px; padding: 10px 20px !important; font-size: 0.85rem !important; }

/* ================================================================
   RESPONSIVE
   ================================================================ */
@media (max-width: 639px) {
    .offer-hero { padding: 24px 16px; margin: 16px; }
    .offer-hero__title { font-size: 1.3rem; }
    .offer-modal__actions { flex-direction: column; }
    .offer-modal__qty { align-self: flex-start; }
    .offer-modal__cart-btn { width: 100%; }
}
</style>