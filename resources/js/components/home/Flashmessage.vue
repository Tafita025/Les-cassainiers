<script setup>
import { computed, watch, ref } from 'vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()
const show = ref(false)

const flash = computed(() => page.props.flash)

const message = computed(() => {
    if (flash.value?.success) return flash.value.success
    if (flash.value?.error) return flash.value.error
    return null
})

const type = computed(() => {
    if (flash.value?.success) return 'success'
    if (flash.value?.error) return 'error'
    return null
})

watch(
    () => flash.value,
    (newFlash) => {
        if (newFlash?.success || newFlash?.error) {
            show.value = true
            setTimeout(() => {
                show.value = false
            }, 5000)
        }
    },
    { deep: true, immediate: true }
)
</script>

<template>
    <Teleport to="body">
        <Transition name="flash">
            <div
                v-if="show && message"
                class="flash-message"
                :class="`flash-message--${type}`"
                @click="show = false"
            >
                <div class="flash-message__icon">
                    <svg v-if="type === 'success'" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <svg v-else class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>
                <p class="flash-message__text">{{ message }}</p>
                <button class="flash-message__close" @click="show = false">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
.flash-message {
    position: fixed;
    top: 24px;
    right: 24px;
    z-index: 9999;
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 320px;
    max-width: 480px;
    padding: 16px 20px;
    border-radius: 12px;
    backdrop-filter: blur(12px);
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
    cursor: pointer;
    transition: all 200ms ease;
}

.flash-message:hover {
    transform: translateY(-2px);
    box-shadow: 0 15px 50px rgba(0, 0, 0, 0.4);
}

.flash-message--success {
    background: rgba(57, 255, 20, 0.15);
    border: 1px solid rgba(57, 255, 20, 0.3);
    color: #39ff14;
}

.flash-message--error {
    background: rgba(255, 45, 149, 0.15);
    border: 1px solid rgba(255, 45, 149, 0.3);
    color: #ff2d95;
}

.flash-message__icon {
    flex-shrink: 0;
}

.flash-message__text {
    flex: 1;
    font-family: var(--font-ui, sans-serif);
    font-size: 0.85rem;
    font-weight: 500;
    line-height: 1.5;
}

.flash-message__close {
    flex-shrink: 0;
    width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    background: rgba(0, 0, 0, 0.2);
    border: none;
    color: currentColor;
    cursor: pointer;
    transition: all 150ms ease;
}

.flash-message__close:hover {
    background: rgba(0, 0, 0, 0.3);
}

/* Transitions */
.flash-enter-active,
.flash-leave-active {
    transition: all 300ms ease;
}

.flash-enter-from {
    opacity: 0;
    transform: translateX(100px);
}

.flash-leave-to {
    opacity: 0;
    transform: translateX(100px) scale(0.95);
}

/* Responsive */
@media (max-width: 639px) {
    .flash-message {
        top: 16px;
        right: 16px;
        left: 16px;
        min-width: auto;
    }
}
</style>