<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { inject, computed } from 'vue'
import ProfileDropdown from '@/components/home/ProfileDropdown.vue'
import { route } from 'ziggy-js';

/* ================================================================
   INJECTION
   ================================================================ */
const sidebarOpen = inject('sidebarOpen')
const showAuthModal = inject('showAuthModal')
const authTab = inject('authTab')

/* ================================================================
   AUTH & CART
   ================================================================ */
const page = usePage()
const isLoggedIn = computed(() => !!page.props.auth?.user)
const cartCount = computed(() => page.props.cart?.count || 0)
</script>

<template>
    <header class="topbar">

        <!-- Hamburger mobile -->
        <button
            class="hamburger"
            :class="{ 'hamburger--active': sidebarOpen }"
            @click="sidebarOpen = !sidebarOpen"
        >
            <span class="hamburger__line"></span>
            <span class="hamburger__line"></span>
            <span class="hamburger__line"></span>
        </button>

        <!-- Breadcrumb -->
        <div class="topbar__breadcrumb">
            <span class="text-[#8b85a6]">Dashboard</span>
            <svg class="h-3.5 w-3.5 text-[#8b85a6]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 5 7 7-7 7"/></svg>
            <span class="text-[#00f0ff]">{{ pageTitle }}</span>
        </div>

        <!-- Recherche -->
        <div class="topbar__search">
            <svg class="topbar__search-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
            <input type="text" placeholder="Rechercher un composant..." class="input-cyber py-2.5 pl-10 text-sm" />
        </div>

        <!-- Actions droite -->
        <div class="topbar__actions">
            <!-- Panier -->
            <Link
                v-if="isLoggedIn"
                :href="route('home.pannier')"
                class="topbar__icon-btn"
            >
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z"/></svg>
                <span v-if="cartCount > 0" class="topbar__badge-count">{{ cartCount }}</span>
            </Link>
            
            <!-- Panier invité (ouvre la modal auth) -->
            <button
                v-else
                @click="showAuthModal = true; authTab = 'login'"
                class="topbar__icon-btn"
            >
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z"/></svg>
                <span class="topbar__badge-count">0</span>
            </button>

            <!-- Notification -->
            <button class="topbar__icon-btn">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"/></svg>
                <span class="notif-dot" style="position:absolute;top:6px;right:6px;"></span>
            </button>

            <!-- Connecté : ProfileDropdown -->
            <ProfileDropdown v-if="isLoggedIn" />

            <!-- Invité : ouvre la modal auth -->
            <button v-else class="topbar__guest" @click="showAuthModal = true; authTab = 'login'">
                <div class="topbar__guest-avatar">
                    <svg class="h-4 w-4 text-[#8b85a6]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/></svg>
                </div>
                <span class="topbar__guest-label">Invité</span>
            </button>
        </div>

    </header>
</template>

<script>
export default {
    props: {
        pageTitle: {
            type: String,
            default: 'Accueil'
        }
    }
}
</script>