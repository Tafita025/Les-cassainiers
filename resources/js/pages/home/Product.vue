<script setup>
import { ref, provide, computed } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import HomeLayout from '@/layouts/home/HomeLayout.vue'
import AuthModal from '@/components/home/AuthModal.vue'

/* ================================================================
   PROPS INERTIA (données du back)
   ================================================================ */
const props = defineProps({
    products: {
        type: Array,
        default: () => [],
    },
    currentCategory: {
        type: String,
        default: null,
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
   AUTH
   ================================================================ */
const page = usePage()
const isLoggedIn = computed(() => !!page.props.auth?.user)

/* ================================================================
   ÉTAT — Modal détail produit
   ================================================================ */
const showProductModal = ref(false)
const selectedProduct = ref(null)
const quantity = ref(1)
const processing = ref(false)

function openProduct(product) {
    selectedProduct.value = product
    quantity.value = 1
    showProductModal.value = true
}

function closeProduct() {
    showProductModal.value = false
    selectedProduct.value = null
}

function increaseQty() {
    if (quantity.value < 10) quantity.value++
}
function decreaseQty() {
    if (quantity.value > 1) quantity.value--
}

/* ================================================================
   AJOUT AU PANIER
   ================================================================ */
function addToCart(product, qty = 1) {
    // Si non connecté, ouvrir la modal d'authentification
    if (!isLoggedIn.value) {
        showAuthModal.value = true
        authTab.value = 'login'
        return
    }

    processing.value = true
    router.post(
        route('home.pannier.add', product.id),
        { quantity: qty },
        {
            preserveScroll: true,
            onFinish: () => {
                processing.value = false
                // Fermer la modal produit si elle est ouverte
                if (showProductModal.value) {
                    closeProduct()
                }
            },
        }
    )
}

/* ================================================================
   TITRE DYNAMIQUE
   ================================================================ */
const pageSubtitle = computed(() => {
    if (props.currentCategory) {
        return `${props.products.length} produit${props.products.length > 1 ? 's' : ''} dans cette catégorie`
    }
    return `${props.products.length} produits disponibles`
})
</script>

<template>
    <HomeLayout page-title="Produits">

        <!-- Modal Auth -->
        <AuthModal />

        <!-- ============================================================
             MODAL DÉTAIL PRODUIT
             ============================================================ -->
        <Teleport to="body">
            <div
                v-if="showProductModal && selectedProduct"
                class="product-modal-backdrop"
                @click.self="closeProduct"
            >
                <div class="product-modal" @click.stop>

                    <button class="product-modal__close" @click="closeProduct">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                    </button>

                    <div class="product-modal__image-wrapper">
                        <img :src="selectedProduct.image" :alt="selectedProduct.name" class="product-modal__image" />
                    </div>

                    <div class="product-modal__info">
                        <p class="product-modal__brand">{{ selectedProduct.brand }}</p>
                        <h2 class="product-modal__name">{{ selectedProduct.name }}</h2>

                        <div class="product-modal__rating">
                            <div class="stars-row">
                                <svg v-for="n in 5" :key="n" :class="['h-4 w-4', n <= selectedProduct.rating ? 'star-filled' : 'star-empty']" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                            </div>
                            <span class="product-modal__reviews">({{ selectedProduct.reviews }} avis)</span>
                        </div>

                        <p class="product-modal__description">{{ selectedProduct.description }}</p>

                        <!-- Stock -->
                        <div class="product-modal__stock">
                            <span v-if="selectedProduct.stock > 0" class="product-modal__stock-tag product-modal__stock-tag--green">
                                <span class="product-modal__stock-dot"></span>
                                En stock ({{ selectedProduct.stock }})
                            </span>
                            <span v-else class="product-modal__stock-tag product-modal__stock-tag--red">Rupture de stock</span>
                        </div>

                        <span class="price text-xl">{{ selectedProduct.price }}</span>

                        <div class="product-modal__actions">
                            <div class="product-modal__qty">
                                <button class="product-modal__qty-btn" @click="decreaseQty" :disabled="processing">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14"/></svg>
                                </button>
                                <span class="product-modal__qty-value">{{ quantity }}</span>
                                <button class="product-modal__qty-btn" @click="increaseQty" :disabled="processing">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                </button>
                            </div>
                            <button
                                class="btn-cyber btn-primary product-modal__cart-btn"
                                :disabled="selectedProduct.stock === 0 || processing"
                                @click="addToCart(selectedProduct, quantity)"
                            >
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z"/></svg>
                                {{ processing ? 'Ajout...' : selectedProduct.stock === 0 ? 'Indisponible' : 'Ajouter au panier' }}
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

            <!-- En-tête -->
            <section class="section">
                <div class="section__header">
                    <div>
                        <h1 class="hero__title" style="font-size: 1.8rem; margin-bottom: 8px;">
                            Nos <span class="text-gradient-cyber">Produits</span>
                        </h1>
                        <p class="font-ui text-sm text-[#8b85a6]">{{ pageSubtitle }}</p>
                    </div>
                    <!-- Bouton reset filtre catégorie -->
                    <Link
                        v-if="currentCategory"
                        :href="route('home.product')"
                        class="btn-cyber btn-primary"
                        style="padding: 8px 16px; font-size: 0.78rem;"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                        Toutes les catégories
                    </Link>
                </div>
            </section>

            <!-- Grille Produits -->
            <section class="section">

                <!-- Vide -->
                <div v-if="products.length === 0" class="products-empty">
                    <svg class="h-12 w-12 text-[#8b85a6]" style="opacity:0.4;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z"/></svg>
                    <p class="font-ui text-sm text-[#8b85a6]">Aucun produit trouvé dans cette catégorie.</p>
                    <Link :href="route('home.product')" class="btn-cyber btn-primary" style="margin-top:12px;padding:8px 20px;font-size:0.8rem;">
                        Voir tous les produits
                    </Link>
                </div>

                <div v-else class="products-grid">
                    <div
                        v-for="product in products"
                        :key="product.id"
                        class="card group"
                        style="cursor: pointer;"
                        @click="openProduct(product)"
                    >
                        <div class="card-img-wrapper" style="height: 192px; display: flex; align-items: center; justify-content: center; padding: 16px;">
                            <img :src="product.image" :alt="product.name" class="product-card__image" />
                        </div>

                        <div style="padding: 16px;">
                            <p class="font-mono text-[0.65rem] text-[#8b85a6]" style="margin-bottom: 4px;">{{ product.brand }}</p>
                            <h3 class="font-ui line-clamp-2 text-sm font-semibold text-[#e8e6f0]" style="margin-bottom: 8px;">{{ product.name }}</h3>

                            <div class="stars-row" style="margin-bottom: 12px;">
                                <svg v-for="n in 5" :key="n" :class="['h-3.5 w-3.5', n <= product.rating ? 'star-filled' : 'star-empty']" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                <span class="font-mono text-[0.6rem] text-[#8b85a6]" style="margin-left: 4px;">({{ product.reviews }})</span>
                            </div>

                            <div style="display: flex; align-items: center; justify-content: space-between;">
                                <span class="price text-base">{{ product.price }}</span>
                                <button class="btn-cyber btn-primary" style="padding: 8px 12px; font-size: 0;" @click.stop="addToCart(product, 1)">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Footer -->
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
   PRODUCT CARD IMAGE
   ================================================================ */
.product-card__image { max-height: 140px; max-width: 100%; object-fit: contain; border-radius: 8px; opacity: 0.85; transition: opacity 200ms ease, transform 300ms ease; }
.card:hover .product-card__image { opacity: 1; transform: scale(1.05); }

/* ================================================================
   EMPTY STATE
   ================================================================ */
.products-empty { display: flex; flex-direction: column; align-items: center; gap: 12px; padding: 60px 20px; text-align: center; }

/* ================================================================
   STOCK TAG
   ================================================================ */
.product-modal__stock { display: flex; align-items: center; }
.product-modal__stock-tag { display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px; border-radius: 20px; font-family: var(--font-mono, monospace); font-size: 0.65rem; font-weight: 600; }
.product-modal__stock-tag--green { background: rgba(57, 255, 20, 0.08); color: #39ff14; border: 1px solid rgba(57, 255, 20, 0.15); }
.product-modal__stock-tag--red { background: rgba(255, 45, 149, 0.08); color: #ff2d95; border: 1px solid rgba(255, 45, 149, 0.15); }
.product-modal__stock-dot { width: 6px; height: 6px; border-radius: 50%; background: #39ff14; animation: pulse 2s infinite; }
@keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.4; } }

/* ================================================================
   PRODUCT MODAL
   ================================================================ */
.product-modal-backdrop { position: fixed; inset: 0; z-index: 100; background: rgba(5, 2, 18, 0.85); backdrop-filter: blur(12px); display: flex; align-items: center; justify-content: center; padding: 16px; animation: fadeIn 200ms ease; }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
.product-modal { position: relative; background: var(--clr-surface-light, #1a1530); border: 1px solid var(--clr-border, rgba(255,255,255,0.06)); border-radius: 16px; max-width: 520px; width: 100%; max-height: 90vh; overflow-y: auto; box-shadow: 0 25px 60px rgba(0, 0, 0, 0.5), 0 0 40px rgba(0, 240, 255, 0.05); animation: modalSlideUp 300ms ease; }
@keyframes modalSlideUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
.product-modal__close { position: absolute; top: 12px; right: 12px; z-index: 10; width: 36px; height: 36px; border-radius: 50%; background: rgba(0, 0, 0, 0.4); border: 1px solid var(--clr-border, rgba(255,255,255,0.06)); color: #e8e6f0; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 200ms ease; }
.product-modal__close:hover { background: rgba(255, 45, 149, 0.2); color: #ff2d95; }
.product-modal__image-wrapper { display: flex; align-items: center; justify-content: center; padding: 32px 24px; background: linear-gradient(135deg, rgba(0, 240, 255, 0.03), rgba(180, 74, 255, 0.03)); border-bottom: 1px solid var(--clr-border, rgba(255,255,255,0.06)); border-radius: 16px 16px 0 0; }
.product-modal__image { max-height: 180px; max-width: 100%; object-fit: contain; }
.product-modal__info { padding: 24px; display: flex; flex-direction: column; gap: 12px; }
.product-modal__brand { font-family: var(--font-mono, monospace); font-size: 0.7rem; color: #8b85a6; text-transform: uppercase; letter-spacing: 0.08em; }
.product-modal__name { font-family: var(--font-ui, sans-serif); font-size: 1.1rem; font-weight: 700; color: #e8e6f0; line-height: 1.35; }
.product-modal__rating { display: flex; align-items: center; gap: 8px; }
.product-modal__reviews { font-family: var(--font-mono, monospace); font-size: 0.7rem; color: #8b85a6; }
.product-modal__description { font-family: var(--font-ui, sans-serif); font-size: 0.82rem; color: #8b85a6; line-height: 1.6; }
.product-modal__actions { display: flex; align-items: center; gap: 12px; padding-top: 12px; border-top: 1px solid var(--clr-border, rgba(255,255,255,0.06)); }
.product-modal__qty { display: flex; align-items: center; border: 1px solid var(--clr-border, rgba(255,255,255,0.06)); border-radius: 8px; overflow: hidden; }
.product-modal__qty-btn { width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; background: rgba(255, 255, 255, 0.03); color: #8b85a6; border: none; cursor: pointer; transition: all 150ms ease; }
.product-modal__qty-btn:hover:not(:disabled) { background: rgba(0, 240, 255, 0.1); color: #00f0ff; }
.product-modal__qty-btn:disabled { opacity: 0.5; cursor: not-allowed; }
.product-modal__qty-value { width: 40px; text-align: center; font-family: var(--font-ui, sans-serif); font-size: 0.85rem; font-weight: 600; color: #e8e6f0; }
.product-modal__cart-btn { flex: 1; display: flex; align-items: center; justify-content: center; gap: 8px; padding: 10px 20px !important; font-size: 0.85rem !important; }
.product-modal__cart-btn:disabled { opacity: 0.5; cursor: not-allowed; }

@media (max-width: 639px) {
    .product-modal__actions { flex-direction: column; }
    .product-modal__qty { align-self: flex-start; }
    .product-modal__cart-btn { width: 100%; }
}
</style>