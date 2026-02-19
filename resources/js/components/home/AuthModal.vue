<script setup>
import { inject, ref, watch } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import { route } from 'ziggy-js'

/* ================================================================
   INJECTION — États partagés depuis la page parente
   ================================================================ */
const showAuthModal = inject('showAuthModal')
const authTab = inject('authTab')

/* ================================================================
   WATCHER GLOBAL — Middleware déclenche le modal
   ================================================================ */
const page = usePage()

watch(
    () => page.props.flash?.authRequired,
    (required) => {
        if (required) {
            showAuthModal.value = true
            authTab.value = 'login'
        }
    }
)

/* ================================================================
   FORMULAIRE CONNEXION
   ================================================================ */
const loginForm = useForm({
    email: '',
    password: '',
    remember: false,
})

const handleLogin = () => {
    loginForm.post(route('auth.login'), {
        onSuccess: () => {
            showAuthModal.value = false
            loginForm.reset()
        },
        onError: () => {
            loginForm.reset('password')
        },
    })
}

/* ================================================================
   FORMULAIRE INSCRIPTION
   ================================================================ */
const registerForm = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
})

const handleRegister = () => {
    registerForm.post(route('auth.register'), {
        onSuccess: () => {
            showAuthModal.value = false
            registerForm.reset()
        },
        onError: () => {
            registerForm.reset('password', 'password_confirmation')
        },
    })
}

/* ================================================================
   VISIBILITÉ MOT DE PASSE
   ================================================================ */
const showLoginPassword = ref(false)
const showRegisterPassword = ref(false)
const showRegisterConfirm = ref(false)
</script>

<template>
    <Teleport to="body">
        <div v-if="showAuthModal" class="modal-backdrop" @click.self="showAuthModal = false">
            <div class="modal" @click.stop>

                <!-- Bouton fermer -->
                <button class="modal__close" @click="showAuthModal = false">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                </button>

                <!-- Logo -->
                <div class="modal__logo">
                    <div class="modal__logo-icon">
                        <svg class="h-5 w-5 text-[#0d0a1a]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 0 1-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 0 1 4.5 0m0 0v5.714a2.25 2.25 0 0 0 .659 1.591L19 14.5M14.25 3.104c.251.023.501.05.75.082M19 14.5l-2.47 2.47a2.25 2.25 0 0 1-1.591.659H9.061a2.25 2.25 0 0 1-1.591-.659L5 14.5m14 0V19a2.25 2.25 0 0 1-2.25 2.25H7.25A2.25 2.25 0 0 1 5 19v-4.5"/></svg>
                    </div>
                    <span class="modal__logo-text">Les Casainiers</span>
                </div>

                <!-- Tabs -->
                <div class="modal__tabs">
                    <button class="modal__tab" :class="{ 'modal__tab--active': authTab === 'login' }" @click="authTab = 'login'">Connexion</button>
                    <button class="modal__tab" :class="{ 'modal__tab--active': authTab === 'register' }" @click="authTab = 'register'">Inscription</button>
                </div>

                <!-- ========================
                     FORMULAIRE CONNEXION
                     ======================== -->
                <form v-if="authTab === 'login'" class="modal__body" @submit.prevent="handleLogin">

                    <div v-if="loginForm.errors.email || loginForm.errors.password" class="modal__alert">
                        <svg class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
                        <span>{{ loginForm.errors.email || loginForm.errors.password }}</span>
                    </div>

                    <div class="modal__field">
                        <label class="modal__label" for="login-email">Adresse e-mail</label>
                        <input
                            id="login-email"
                            v-model="loginForm.email"
                            type="email"
                            placeholder="votre@email.mg"
                            class="input-cyber"
                            :class="{ 'input-cyber--error': loginForm.errors.email }"
                            autocomplete="email"
                        />
                    </div>

                    <div class="modal__field">
                        <label class="modal__label" for="login-password">Mot de passe</label>
                        <div class="modal__password-wrapper">
                            <input
                                id="login-password"
                                v-model="loginForm.password"
                                :type="showLoginPassword ? 'text' : 'password'"
                                placeholder="••••••••"
                                class="input-cyber"
                                :class="{ 'input-cyber--error': loginForm.errors.password }"
                                autocomplete="current-password"
                            />
                            <button type="button" class="modal__eye-btn" @click="showLoginPassword = !showLoginPassword">
                                <svg v-if="!showLoginPassword" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                                <svg v-else class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12c1.292 4.338 5.31 7.5 10.066 7.5.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                            </button>
                        </div>
                    </div>

                    <a href="#" class="modal__forgot">Mot de passe oublié ?</a>

                    <button type="submit" class="btn-cyber btn-primary modal__submit" :disabled="loginForm.processing">
                        <svg v-if="loginForm.processing" class="h-4 w-4 modal__spinner" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182"/></svg>
                        {{ loginForm.processing ? 'Connexion...' : 'Se connecter' }}
                    </button>

                    <p class="modal__switch">Pas encore de compte ? <button type="button" @click="authTab = 'register'" class="modal__switch-link">Créer un compte</button></p>
                </form>

                <!-- ========================
                     FORMULAIRE INSCRIPTION
                     ======================== -->
                <form v-if="authTab === 'register'" class="modal__body" @submit.prevent="handleRegister">

                    <div v-if="Object.keys(registerForm.errors).length" class="modal__alert">
                        <svg class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
                        <span>{{ Object.values(registerForm.errors)[0] }}</span>
                    </div>

                    <div class="modal__field">
                        <label class="modal__label" for="register-name">Nom complet</label>
                        <input
                            id="register-name"
                            v-model="registerForm.name"
                            type="text"
                            placeholder="Rakoto Jean"
                            class="input-cyber"
                            :class="{ 'input-cyber--error': registerForm.errors.name }"
                            autocomplete="name"
                        />
                        <span v-if="registerForm.errors.name" class="modal__field-error">{{ registerForm.errors.name }}</span>
                    </div>

                    <div class="modal__field">
                        <label class="modal__label" for="register-email">Adresse e-mail</label>
                        <input
                            id="register-email"
                            v-model="registerForm.email"
                            type="email"
                            placeholder="votre@email.mg"
                            class="input-cyber"
                            :class="{ 'input-cyber--error': registerForm.errors.email }"
                            autocomplete="email"
                        />
                        <span v-if="registerForm.errors.email" class="modal__field-error">{{ registerForm.errors.email }}</span>
                    </div>

                    <div class="modal__field">
                        <label class="modal__label" for="register-password">Mot de passe</label>
                        <div class="modal__password-wrapper">
                            <input
                                id="register-password"
                                v-model="registerForm.password"
                                :type="showRegisterPassword ? 'text' : 'password'"
                                placeholder="Min. 8 caractères"
                                class="input-cyber"
                                :class="{ 'input-cyber--error': registerForm.errors.password }"
                                autocomplete="new-password"
                            />
                            <button type="button" class="modal__eye-btn" @click="showRegisterPassword = !showRegisterPassword">
                                <svg v-if="!showRegisterPassword" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                                <svg v-else class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12c1.292 4.338 5.31 7.5 10.066 7.5.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                            </button>
                        </div>
                        <span v-if="registerForm.errors.password" class="modal__field-error">{{ registerForm.errors.password }}</span>
                    </div>

                    <div class="modal__field">
                        <label class="modal__label" for="register-password-confirm">Confirmer le mot de passe</label>
                        <div class="modal__password-wrapper">
                            <input
                                id="register-password-confirm"
                                v-model="registerForm.password_confirmation"
                                :type="showRegisterConfirm ? 'text' : 'password'"
                                placeholder="••••••••"
                                class="input-cyber"
                                :class="{ 'input-cyber--error': registerForm.errors.password_confirmation }"
                                autocomplete="new-password"
                            />
                            <button type="button" class="modal__eye-btn" @click="showRegisterConfirm = !showRegisterConfirm">
                                <svg v-if="!showRegisterConfirm" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                                <svg v-else class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12c1.292 4.338 5.31 7.5 10.066 7.5.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-cyber btn-accent modal__submit" :disabled="registerForm.processing">
                        <svg v-if="registerForm.processing" class="h-4 w-4 modal__spinner" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182"/></svg>
                        {{ registerForm.processing ? 'Création...' : 'Créer mon compte' }}
                    </button>

                    <p class="modal__switch">Déjà inscrit ? <button type="button" @click="authTab = 'login'" class="modal__switch-link">Se connecter</button></p>
                </form>

            </div>
        </div>
    </Teleport>
</template>

<style scoped>
.modal__alert { display: flex; align-items: flex-start; gap: 8px; padding: 10px 14px; background: rgba(255, 45, 149, 0.08); border: 1px solid rgba(255, 45, 149, 0.2); border-radius: 8px; font-family: var(--font-ui, sans-serif); font-size: 0.78rem; color: #ff2d95; line-height: 1.4; margin-bottom: 4px; }
.modal__field-error { font-family: var(--font-ui, sans-serif); font-size: 0.7rem; color: #ff2d95; margin-top: 4px; display: block; }
.input-cyber--error { border-color: rgba(255, 45, 149, 0.4) !important; box-shadow: 0 0 0 2px rgba(255, 45, 149, 0.1) !important; }
.modal__password-wrapper { position: relative; }
.modal__password-wrapper .input-cyber { padding-right: 42px; }
.modal__eye-btn { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); width: 30px; height: 30px; border-radius: 6px; background: transparent; border: none; color: #8b85a6; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 150ms ease; }
.modal__eye-btn:hover { color: #00f0ff; background: rgba(0, 240, 255, 0.06); }
.modal__spinner { animation: spin 0.8s linear infinite; }
@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
.modal__submit:disabled { opacity: 0.6; cursor: not-allowed; pointer-events: none; }
</style>