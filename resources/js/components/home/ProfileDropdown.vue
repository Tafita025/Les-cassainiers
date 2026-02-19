<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { usePage, useForm } from '@inertiajs/vue3'
import { route } from 'ziggy-js'

/* ================================================================
   AUTH
   ================================================================ */
const page = usePage()
const user = computed(() => page.props.auth?.user)

const initials = computed(() => {
    if (!user.value?.name) return ''
    return user.value.name
        .split(' ')
        .filter(Boolean)
        .map(w => w[0].toUpperCase())
        .slice(0, 2)
        .join('')
})

/* ================================================================
   DROPDOWN STATE
   ================================================================ */
const isOpen = ref(false)
const dropdownRef = ref(null)

const toggle = () => {
    isOpen.value = !isOpen.value
}

const close = () => {
    isOpen.value = false
}

const handleClickOutside = (e) => {
    if (dropdownRef.value && !dropdownRef.value.contains(e.target)) {
        close()
    }
}

onMounted(() => document.addEventListener('click', handleClickOutside))
onUnmounted(() => document.removeEventListener('click', handleClickOutside))

/* ================================================================
   LOGOUT
   ================================================================ */
const logoutForm = useForm({})

const handleLogout = () => {
    close()
    logoutForm.post(route('auth.logout'), {
        preserveScroll: true,
    })
}
</script>

<template>
    <div v-if="user" ref="dropdownRef" class="pd">

        <!-- ====================================================
             TRIGGER
             ==================================================== -->
        <button class="pd__trigger" @click="toggle">
            <div class="pd__avatar">
                <span class="pd__initials">{{ initials }}</span>
                <span class="pd__status-dot"></span>
            </div>
            <div class="pd__trigger-info">
                <span class="pd__trigger-name">{{ user.name }}</span>
                <span class="pd__trigger-role">Membre</span>
            </div>
            <svg
                class="pd__chevron"
                :class="{ 'pd__chevron--open': isOpen }"
                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
            >
                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
            </svg>
        </button>

        <!-- ====================================================
             MENU DÉROULANT
             ==================================================== -->
        <Transition name="dropdown">
            <div v-if="isOpen" class="pd__menu">

                <!-- Header : profil -->
                <div class="pd__header">
                    <div class="pd__header-avatar">
                        <span class="pd__header-initials">{{ initials }}</span>
                    </div>
                    <div class="pd__header-info">
                        <span class="pd__header-name">{{ user.name }}</span>
                        <span class="pd__header-email">{{ user.email }}</span>
                    </div>
                    <span class="pd__header-badge">Pro</span>
                </div>

                <div class="pd__divider"></div>

                <!-- Section : Compte -->
                <p class="pd__section-label">Compte</p>

                <!-- Paramètres -->
                <button class="pd__item" @click="close">
                    <div class="pd__item-icon pd__item-icon--cyan">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.212-1.281c-.063-.374-.313-.686-.645-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                    </div>
                    <div class="pd__item-content">
                        <span class="pd__item-label">Paramètres</span>
                        <span class="pd__item-desc">Profil, mot de passe, préférences</span>
                    </div>
                    <svg class="pd__item-arrow" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                </button>

                <!-- Confidentialité -->
                <button class="pd__item" @click="close">
                    <div class="pd__item-icon pd__item-icon--purple">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z"/></svg>
                    </div>
                    <div class="pd__item-content">
                        <span class="pd__item-label">Confidentialité</span>
                        <span class="pd__item-desc">Données, cookies, sécurité</span>
                    </div>
                    <svg class="pd__item-arrow" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                </button>

                <div class="pd__divider"></div>

                <!-- Déconnexion -->
                <button class="pd__item pd__item--danger" @click="handleLogout">
                    <div class="pd__item-icon pd__item-icon--pink">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9"/></svg>
                    </div>
                    <div class="pd__item-content">
                        <span class="pd__item-label">Déconnexion</span>
                        <span class="pd__item-desc">Fermer votre session</span>
                    </div>
                </button>

                <!-- Footer menu -->
                <div class="pd__footer">
                    <svg class="h-3 w-3 text-[#8b85a6]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 0 1-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 0 1 4.5 0m0 0v5.714a2.25 2.25 0 0 0 .659 1.591L19 14.5M14.25 3.104c.251.023.501.05.75.082M19 14.5l-2.47 2.47a2.25 2.25 0 0 1-1.591.659H9.061a2.25 2.25 0 0 1-1.591-.659L5 14.5m14 0V19a2.25 2.25 0 0 1-2.25 2.25H7.25A2.25 2.25 0 0 1 5 19v-4.5"/></svg>
                    <span>Les Casainiers v1.2</span>
                </div>

            </div>
        </Transition>

    </div>
</template>

<style scoped>
.pd {
    position: relative;
}

/* ================================================================
   TRIGGER
   ================================================================ */
.pd__trigger {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 5px 10px 5px 5px;
    border-radius: 12px;
    border: 1px solid transparent;
    background: transparent;
    cursor: pointer;
    transition: all 200ms ease;
}
.pd__trigger:hover {
    background: rgba(255, 255, 255, 0.04);
    border-color: var(--clr-border, rgba(255,255,255,0.06));
}

/* Avatar + status */
.pd__avatar {
    position: relative;
    width: 34px;
    height: 34px;
    border-radius: 10px;
    background: linear-gradient(135deg, rgba(0, 240, 255, 0.15), rgba(180, 74, 255, 0.2));
    border: 1px solid rgba(0, 240, 255, 0.25);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: all 250ms ease;
}
.pd__trigger:hover .pd__avatar {
    border-color: rgba(0, 240, 255, 0.4);
    box-shadow: 0 0 12px rgba(0, 240, 255, 0.1);
}
.pd__initials {
    font-family: var(--font-display, 'Orbitron', sans-serif);
    font-size: 0.6rem;
    font-weight: 800;
    color: #00f0ff;
    letter-spacing: 0.05em;
}
.pd__status-dot {
    position: absolute;
    bottom: -2px;
    right: -2px;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: #39ff14;
    border: 2px solid var(--clr-surface, #0d0a1a);
    box-shadow: 0 0 6px rgba(57, 255, 20, 0.4);
}

/* Trigger info */
.pd__trigger-info {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 0;
}
.pd__trigger-name {
    font-family: var(--font-ui, sans-serif);
    font-size: 0.78rem;
    font-weight: 600;
    color: #e8e6f0;
    max-width: 120px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    line-height: 1.2;
}
.pd__trigger-role {
    font-family: var(--font-mono, monospace);
    font-size: 0.55rem;
    color: #8b85a6;
    text-transform: uppercase;
    letter-spacing: 0.08em;
}
.pd__chevron {
    width: 14px;
    height: 14px;
    color: #8b85a6;
    transition: transform 250ms ease;
    flex-shrink: 0;
}
.pd__chevron--open {
    transform: rotate(180deg);
}

/* ================================================================
   MENU DÉROULANT
   ================================================================ */
.pd__menu {
    position: absolute;
    top: calc(100% + 10px);
    right: 0;
    width: 280px;
    background: var(--clr-surface-light, #1a1530);
    border: 1px solid var(--clr-border, rgba(255,255,255,0.08));
    border-radius: 16px;
    box-shadow:
        0 16px 48px rgba(0, 0, 0, 0.5),
        0 0 0 1px rgba(0, 240, 255, 0.04);
    padding: 6px;
    z-index: 200;
    overflow: hidden;
}

/* ── Header ── */
.pd__header {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 12px;
    background: linear-gradient(135deg, rgba(0, 240, 255, 0.04), rgba(180, 74, 255, 0.04));
    border-radius: 12px;
    border: 1px solid rgba(255, 255, 255, 0.03);
}
.pd__header-avatar {
    width: 42px;
    height: 42px;
    border-radius: 12px;
    background: linear-gradient(135deg, rgba(0, 240, 255, 0.2), rgba(180, 74, 255, 0.25));
    border: 1px solid rgba(0, 240, 255, 0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.pd__header-initials {
    font-family: var(--font-display, 'Orbitron', sans-serif);
    font-size: 0.8rem;
    font-weight: 800;
    color: #00f0ff;
    letter-spacing: 0.05em;
}
.pd__header-info {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 2px;
}
.pd__header-name {
    font-family: var(--font-ui, sans-serif);
    font-size: 0.85rem;
    font-weight: 700;
    color: #e8e6f0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.pd__header-email {
    font-family: var(--font-mono, monospace);
    font-size: 0.6rem;
    color: #8b85a6;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.pd__header-badge {
    padding: 3px 10px;
    border-radius: 20px;
    font-family: var(--font-mono, monospace);
    font-size: 0.55rem;
    font-weight: 700;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: #00f0ff;
    background: rgba(0, 240, 255, 0.1);
    border: 1px solid rgba(0, 240, 255, 0.2);
    flex-shrink: 0;
}

/* ── Divider ── */
.pd__divider {
    height: 1px;
    background: linear-gradient(to right, transparent, var(--clr-border, rgba(255,255,255,0.06)), transparent);
    margin: 6px 12px;
}

/* ── Section label ── */
.pd__section-label {
    font-family: var(--font-mono, monospace);
    font-size: 0.55rem;
    font-weight: 600;
    color: #8b85a6;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    padding: 8px 12px 4px;
}

/* ── Item ── */
.pd__item {
    display: flex;
    align-items: center;
    gap: 12px;
    width: 100%;
    padding: 10px 12px;
    border: none;
    background: transparent;
    border-radius: 10px;
    cursor: pointer;
    transition: all 180ms ease;
}
.pd__item:hover {
    background: rgba(255, 255, 255, 0.04);
}

/* Item icon */
.pd__item-icon {
    width: 34px;
    height: 34px;
    border-radius: 9px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: all 200ms ease;
}
.pd__item-icon--cyan {
    background: rgba(0, 240, 255, 0.08);
    border: 1px solid rgba(0, 240, 255, 0.12);
    color: #00f0ff;
}
.pd__item:hover .pd__item-icon--cyan {
    background: rgba(0, 240, 255, 0.14);
    box-shadow: 0 0 10px rgba(0, 240, 255, 0.08);
}
.pd__item-icon--purple {
    background: rgba(180, 74, 255, 0.08);
    border: 1px solid rgba(180, 74, 255, 0.12);
    color: #b44aff;
}
.pd__item:hover .pd__item-icon--purple {
    background: rgba(180, 74, 255, 0.14);
    box-shadow: 0 0 10px rgba(180, 74, 255, 0.08);
}
.pd__item-icon--pink {
    background: rgba(255, 45, 149, 0.08);
    border: 1px solid rgba(255, 45, 149, 0.12);
    color: #ff2d95;
}
.pd__item:hover .pd__item-icon--pink {
    background: rgba(255, 45, 149, 0.14);
    box-shadow: 0 0 10px rgba(255, 45, 149, 0.08);
}

/* Item content */
.pd__item-content {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 1px;
    text-align: left;
}
.pd__item-label {
    font-family: var(--font-ui, sans-serif);
    font-size: 0.8rem;
    font-weight: 600;
    color: #e8e6f0;
    transition: color 180ms ease;
}
.pd__item-desc {
    font-family: var(--font-ui, sans-serif);
    font-size: 0.62rem;
    color: #8b85a6;
    line-height: 1.3;
}

/* Item arrow */
.pd__item-arrow {
    width: 14px;
    height: 14px;
    color: #8b85a6;
    flex-shrink: 0;
    opacity: 0;
    transform: translateX(-4px);
    transition: all 200ms ease;
}
.pd__item:hover .pd__item-arrow {
    opacity: 1;
    transform: translateX(0);
    color: #00f0ff;
}

/* Danger variant */
.pd__item--danger:hover {
    background: rgba(255, 45, 149, 0.06);
}
.pd__item--danger:hover .pd__item-label {
    color: #ff2d95;
}

/* ── Footer ── */
.pd__footer {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 10px 12px 8px;
    font-family: var(--font-mono, monospace);
    font-size: 0.52rem;
    color: #8b85a6;
    opacity: 0.5;
}

/* ================================================================
   TRANSITION
   ================================================================ */
.dropdown-enter-active,
.dropdown-leave-active {
    transition: all 250ms cubic-bezier(0.16, 1, 0.3, 1);
}
.dropdown-enter-from,
.dropdown-leave-to {
    opacity: 0;
    transform: translateY(-8px) scale(0.97);
}

/* ================================================================
   RESPONSIVE
   ================================================================ */
@media (max-width: 639px) {
    .pd__trigger-info { display: none; }
    .pd__chevron { display: none; }
    .pd__menu { right: -12px; width: 260px; }
}
</style>