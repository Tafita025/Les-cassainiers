<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { inject, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

/* ================================================================
   INJECTION
   ================================================================ */
const sidebarOpen = inject('sidebarOpen');
const showAuthModal = inject('showAuthModal');
const authTab = inject('authTab');

/* ================================================================
   AUTH + CATÉGORIES — Données via HandleInertiaRequests
   ================================================================ */
const page = usePage();
const user = computed(() => page.props.auth?.user);
const isLoggedIn = computed(() => !!user.value);
const sidebarCategories = computed(() => page.props.categories || []);

const initials = computed(() => {
    if (!user.value?.name) return '';
    return user.value.name
        .split(' ')
        .filter(Boolean)
        .map((w) => w[0].toUpperCase())
        .slice(0, 2)
        .join('');
});

/* ================================================================
   LOGOUT
   ================================================================ */
const logoutForm = useForm({});

const handleLogout = () => {
    logoutForm.post(route('auth.logout'), {
        preserveScroll: true,
        onFinish: () => {
            sidebarOpen.value = false;
        },
    });
};

/* ================================================================
   NAVIGATION — Détection page courante
   ================================================================ */
const currentRoute = computed(() => page.props.ziggy?.location ?? page.url);

function currentPath() {
    try {
        return new URL(currentRoute.value, window.location.origin).pathname;
    } catch {
        return '';
    }
}
function currentSearch() {
    try {
        return new URL(currentRoute.value, window.location.origin).search;
    } catch {
        return '';
    }
}

function isActive(routeName, params = {}) {
    try {
        const target = new URL(route(routeName, params));
        const cur = currentPath();
        const curSearch = currentSearch();

        if (target.pathname === '/accueil')
            return cur === '/accueil' || cur === '/';
        if (params.categorie)
            return (
                cur === target.pathname &&
                curSearch.includes(`categorie=${params.categorie}`)
            );
        if (routeName === 'home.categories') return cur === target.pathname;
        if (routeName === 'home.product' && !params.categorie)
            return cur === target.pathname && !curSearch.includes('categorie=');
        return cur.startsWith(target.pathname);
    } catch {
        return false;
    }
}
</script>

<template>
    <div
        class="overlay"
        :class="{ 'overlay--active': sidebarOpen }"
        @click="sidebarOpen = false"
    ></div>

    <aside class="sidebar" :class="{ 'sidebar--open': sidebarOpen }">
        <!-- Logo -->
        <div class="sidebar__header">
            <div class="sidebar__logo-icon">
                <svg
                    class="h-5 w-5 text-[#0d0a1a]"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2.5"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M9.75 3.104v5.714a2.25 2.25 0 0 1-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 0 1 4.5 0m0 0v5.714a2.25 2.25 0 0 0 .659 1.591L19 14.5M14.25 3.104c.251.023.501.05.75.082M19 14.5l-2.47 2.47a2.25 2.25 0 0 1-1.591.659H9.061a2.25 2.25 0 0 1-1.591-.659L5 14.5m14 0V19a2.25 2.25 0 0 1-2.25 2.25H7.25A2.25 2.25 0 0 1 5 19v-4.5"
                    />
                </svg>
            </div>
            <Link :href="route('home.index')" class="sidebar__logo-text"
                >Les Casainiers</Link
            >
        </div>

        <nav class="sidebar__nav">
            <!-- MENU -->
            <p class="sidebar__title">Menu</p>
            <ul>
                <li>
                    <Link
                        :href="route('home.index')"
                        class="sidebar__link"
                        :class="{
                            'sidebar__link--active': isActive('home.index'),
                        }"
                        @click="sidebarOpen = false"
                    >
                        <svg
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="m2.25 12 8.954-8.955a1.126 1.126 0 0 1 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"
                            />
                        </svg>
                        Accueil
                    </Link>
                </li>
                <li>
                    <Link
                        :href="route('home.product')"
                        class="sidebar__link"
                        :class="{
                            'sidebar__link--active': isActive('home.product'),
                        }"
                        @click="sidebarOpen = false"
                    >
                        <svg
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M8.25 3v1.5M4.5 8.25H3m18 0h-1.5M4.5 12H3m18 0h-1.5m-15 3.75H3m18 0h-1.5M8.25 19.5V21M12 3v1.5m0 15V21m3.75-18v1.5m0 15V21m-9-1.5h10.5a2.25 2.25 0 0 0 2.25-2.25V6.75a2.25 2.25 0 0 0-2.25-2.25H6.75A2.25 2.25 0 0 0 4.5 6.75v10.5a2.25 2.25 0 0 0 2.25 2.25Z"
                            />
                        </svg>
                        Produits
                    </Link>
                </li>
                <li>
                    <Link
                        :href="route('home.offers')"
                        class="sidebar__link"
                        :class="{
                            'sidebar__link--active': isActive('home.offers'),
                        }"
                        @click="sidebarOpen = false"
                    >
                        <svg
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z"
                            />
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M6 6h.008v.008H6V6Z"
                            />
                        </svg>
                        Offres Flash
                        <span class="badge badge-pink" style="margin-left: auto"
                            >-40%</span
                        >
                    </Link>
                </li>
                <li>
                    <Link
                        :href="route('home.payment')"
                        class="sidebar__link"
                        :class="{
                            'sidebar__link--active': isActive('home.payment'),
                        }"
                        @click="sidebarOpen = false"
                    >
                        <svg
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z"
                            />
                        </svg>
                        Paiement
                    </Link>
                </li>
                <li>
                    <Link
                        :href="route('home.review')"
                        class="sidebar__link"
                        :class="{
                            'sidebar__link--active': isActive('home.review'),
                        }"
                        @click="sidebarOpen = false"
                    >
                     
                        <svg
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z"
                            />
                        </svg>
                        Notes
                    </Link>
                </li>
            </ul>

            <!-- CATÉGORIES DYNAMIQUES -->
            <p class="sidebar__title" style="margin-top: 24px">Catégories</p>
            <ul>
                <li v-for="cat in sidebarCategories" :key="cat.id">
                    <Link
                        :href="route('home.product', { categorie: cat.slug })"
                        class="sidebar__link"
                        :class="{
                            'sidebar__link--active': isActive('home.product', {
                                categorie: cat.slug,
                            }),
                        }"
                        @click="sidebarOpen = false"
                    >
                        <svg
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z"
                            />
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M6 6h.008v.008H6V6Z"
                            />
                        </svg>
                        {{ cat.name }}
                        <span
                            v-if="cat.products_count"
                            class="badge badge-subtle"
                            style="margin-left: auto"
                            >{{ cat.products_count }}</span
                        >
                    </Link>
                </li>
            </ul>

            <Link
                :href="route('home.categories')"
                class="sidebar__see-all"
                :class="{
                    'sidebar__see-all--active': isActive('home.categories'),
                }"
                @click="sidebarOpen = false"
            >
                Voir toutes les catégories
                <svg
                    class="h-3 w-3"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="m9 5 7 7-7 7"
                    />
                </svg>
            </Link>
        </nav>

        <!-- FOOTER -->
        <div class="sidebar__footer">
            <div v-if="isLoggedIn" class="sidebar__profile">
                <!-- <div class="sidebar__profile-avatar">
                    <span class="sidebar__profile-initials">{{
                        initials
                    }}</span>
                </div> -->
                <div class="sidebar__profile-info">
                    <span class="sidebar__profile-name">{{ user.name }}</span>
                    <span class="sidebar__profile-email">{{ user.email }}</span>
                </div>
                <button
                    class="sidebar__logout-btn"
                    @click="handleLogout"
                    title="Déconnexion"
                >
                    <svg
                        class="h-4 w-4"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9"
                        />
                    </svg>
                </button>
            </div>
            <button
                v-else
                class="sidebar__guest-btn"
                @click="
                    showAuthModal = true;
                    authTab = 'login';
                    sidebarOpen = false;
                "
            >
                <div class="sidebar__guest-icon">
                    <svg
                        class="h-4 w-4 text-[#8b85a6]"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"
                        />
                    </svg>
                </div>
                <div class="sidebar__guest-info">
                    <span class="sidebar__guest-label">Invité</span>
                    <span class="sidebar__guest-cta">Se connecter</span>
                </div>
                <svg
                    class="sidebar__guest-arrow"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="m9 5 7 7-7 7"
                    />
                </svg>
            </button>
        </div>
    </aside>
</template>

<style scoped>
.sidebar__see-all {
    display: flex;
    align-items: center;
    gap: 6px;
    margin: 10px 5px 0px 5px;
    padding: 16px 12px;
    font-family: var(--font-mono, monospace);
    font-size: 0.7rem;
    color: #00f0ff;
    text-decoration: none;
    border-radius: 3px;
    transition: all 200ms ease;
    opacity: 0.7;
}
.sidebar__see-all:hover {
    opacity: 1;
    background: rgba(0, 240, 255, 0.06);
}
.sidebar__see-all--active {
    opacity: 1;
    color: #00f0ff;
    background: rgba(0, 240, 255, 0.08);
}

/* Badge count subtle */
.badge-subtle {
    font-family: var(--font-mono, monospace);
    font-size: 0.55rem;
    padding: 2px 7px;
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.04);
    border: 1px solid rgba(255, 255, 255, 0.06);
    color: #8b85a6;
}

.sidebar__profile {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 12px;
    background: rgba(0, 240, 255, 0.04);
    border: 1px solid rgba(0, 240, 255, 0.1);
    border-radius: 10px;
}
.sidebar__profile-avatar {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    background: linear-gradient(
        135deg,
        rgba(0, 240, 255, 0.2),
        rgba(180, 74, 255, 0.2)
    );
    border: 1px solid rgba(0, 240, 255, 0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.sidebar__profile-initials {
    font-family: var(--font-display, 'Orbitron', sans-serif);
    font-size: 0.65rem;
    font-weight: 800;
    color: #00f0ff;
    letter-spacing: 0.04em;
}
.sidebar__profile-info {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 1px;
}
.sidebar__profile-name {
    font-family: var(--font-ui, sans-serif);
    font-size: 0.78rem;
    font-weight: 700;
    color: #e8e6f0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.sidebar__profile-email {
    font-family: var(--font-mono, monospace);
    font-size: 0.6rem;
    color: #8b85a6;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.sidebar__logout-btn {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: transparent;
    border: 1px solid transparent;
    color: #8b85a6;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    flex-shrink: 0;
    transition: all 200ms ease;
}
.sidebar__logout-btn:hover {
    background: rgba(255, 45, 149, 0.1);
    border-color: rgba(255, 45, 149, 0.2);
    color: #ff2d95;
}
</style>
