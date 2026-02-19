<script setup>
import Aside from '@/components/home/Aside.vue'
import Topbar from '@/components/home/Topbar.vue'
import '../../../css/home.css'
import { usePage } from '@inertiajs/vue3'
import { computed, watch, ref } from 'vue'

/* Le pageTitle est passé par la page via un prop */
defineProps({
    pageTitle: {
        type: String,
        default: 'Accueil'
    }
})

const page = usePage()

// Toast state
const toast = ref(null) // { type: 'success' | 'error', message: string }
let toastTimer = null

watch(
    () => page.props.flash,
    (flash) => {
        if (flash?.success) showToast('success', flash.success)
        else if (flash?.error) showToast('error', flash.error)
    },
    { immediate: true, deep: true }
)

function showToast(type, message) {
    clearTimeout(toastTimer)
    toast.value = { type, message }
    toastTimer = setTimeout(() => (toast.value = null), 3500)
}
</script>

<template>
    <div class="page-wrapper">

        <!-- ============================================================
             COMPOSANT : Sidebar + Overlay
             ============================================================ -->
        <Aside />

        <!-- ============================================================
             CONTENU PRINCIPAL
             ============================================================ -->
        <div class="main-area">

            <!-- ============================================================
                 COMPOSANT : Topbar (Navigation supérieure)
                 ============================================================ -->
            <Topbar :page-title="pageTitle" />

            <!-- ============================================================
                 CONTENU DE LA PAGE (slot)
                 ============================================================ -->
            <slot />

        </div>

    </div>
     <!-- Toast notifications -->
    <Teleport to="body">
        <Transition name="toast">
            <div v-if="toast" :class="['toast', `toast--${toast.type}`]">
                <svg v-if="toast.type === 'success'" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                </svg>
                <svg v-else class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/>
                </svg>
                <span>{{ toast.message }}</span>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
/* ================================================================
   PAGE WRAPPER — Layout principal flex
   ================================================================ */
.page-wrapper {
    display: flex;
    min-height: 100vh;
    background: var(--clr-surface);
}

/* ================================================================
   MAIN AREA — Zone de contenu avec marge sidebar
   ================================================================ */
.main-area {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
}

@media (min-width: 1024px) {
    .main-area {
        margin-left: var(--sidebar-width);
    }
}
/* Toast */
.toast {
    position: fixed;
    bottom: 24px;
    right: 24px;
    z-index: 9999;
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 18px;
    border-radius: 10px;
    font-family: var(--font-ui, sans-serif);
    font-size: 0.85rem;
    font-weight: 500;
    max-width: 360px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
    backdrop-filter: blur(12px);
}

.toast--success {
    background: rgba(57, 255, 20, 0.1);
    border: 1px solid rgba(57, 255, 20, 0.25);
    color: #39ff14;
}

.toast--error {
    background: rgba(255, 45, 149, 0.1);
    border: 1px solid rgba(255, 45, 149, 0.25);
    color: #ff2d95;
}

/* Animation */
.toast-enter-active { transition: all 300ms cubic-bezier(0.34, 1.56, 0.64, 1); }
.toast-leave-active { transition: all 250ms ease; }
.toast-enter-from  { opacity: 0; transform: translateY(16px) scale(0.95); }
.toast-leave-to    { opacity: 0; transform: translateY(8px); }

</style>