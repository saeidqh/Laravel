<script setup>
import { onMounted, onUnmounted, ref } from 'vue';

const props = defineProps({
    loginUrl: {
        type: String,
        required: true,
    },
    csrfToken: {
        type: String,
        required: true,
    },
    initialUsername: {
        type: String,
        default: '',
    },
    errorMessage: {
        type: String,
        default: '',
    },
});

const username = ref(props.initialUsername);
const password = ref('');
const showPassword = ref(false);
const isSubmitting = ref(false);

function submitLogin(event) {
    if (isSubmitting.value) {
        event.preventDefault();
        return;
    }

    isSubmitting.value = true;
}

function resetSubmitting() {
    isSubmitting.value = false;
}

onMounted(() => {
    window.addEventListener('pageshow', resetSubmitting);
});

onUnmounted(() => {
    window.removeEventListener('pageshow', resetSubmitting);
});
</script>

<template>
    <main class="login-page" dir="ltr">
        <section class="login-card" aria-labelledby="login-title">
            <div class="brand">
                <div class="brand-icon" aria-hidden="true">IPC</div>

                <div>
                    <h1 id="login-title">IPC Management System</h1>
                    <p>Sign in to access your workspace.</p>
                </div>
            </div>

            <form
                :action="props.loginUrl"
                method="POST"
                class="login-form"
                :aria-busy="isSubmitting"
                @submit="submitLogin"
            >
                <input
                    type="hidden"
                    name="_token"
                    :value="props.csrfToken"
                />

                <p
                    v-if="props.errorMessage"
                    id="login-error"
                    class="error-message"
                    role="alert"
                >
                    {{ props.errorMessage }}
                </p>

                <div class="field">
                    <label for="username">Username</label>

                    <input
                        id="username"
                        v-model="username"
                        name="username"
                        type="text"
                        autocomplete="username"
                        autocapitalize="none"
                        :spellcheck="false"
                        maxlength="40"
                        placeholder="Enter your username"
                        :aria-describedby="
                            props.errorMessage ? 'login-error' : undefined
                        "
                        required
                    />
                </div>

                <div class="field">
                    <label for="password">Password</label>

                    <div class="password-wrapper">
                        <input
                            id="password"
                            v-model="password"
                            name="password"
                            :type="showPassword ? 'text' : 'password'"
                            autocomplete="current-password"
                            placeholder="Enter your password"
                            :aria-describedby="
                                props.errorMessage ? 'login-error' : undefined
                            "
                            required
                        />

                        <button
                            type="button"
                            class="toggle-password"
                            aria-controls="password"
                            :aria-label="
                                showPassword ? 'Hide password' : 'Show password'
                            "
                            :aria-pressed="showPassword"
                            @click="showPassword = !showPassword"
                        >
                            {{ showPassword ? 'Hide' : 'Show' }}
                        </button>
                    </div>
                </div>

                <button
                    type="submit"
                    class="login-button"
                    :disabled="isSubmitting"
                >
                    {{ isSubmitting ? 'Signing in...' : 'Sign In' }}
                </button>
            </form>

            <p class="footer-text">
                IPC Management System
            </p>
        </section>
    </main>
</template>

<style scoped>
.login-page,
.login-page * {
    box-sizing: border-box;
}

.login-page {
    min-height: 100vh;
    min-height: 100dvh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 24px;
    background:
        radial-gradient(
            circle at top left,
            rgba(21, 94, 117, 0.22),
            transparent 40%
        ),
        linear-gradient(135deg, #eaf4f7 0%, #f8fafc 52%, #e8eef6 100%);
    font-family: Arial, sans-serif;
}

.login-card {
    width: 100%;
    max-width: 440px;
    padding: 38px;
    border: 1px solid #dbe5eb;
    border-radius: 18px;
    background: rgba(255, 255, 255, 0.96);
    box-shadow: 0 20px 45px rgba(15, 23, 42, 0.14);
}

.brand {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 32px;
}

.brand-icon {
    width: 48px;
    height: 48px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    color: #ffffff;
    background: #155e75;
    font-size: 24px;
    font-weight: 700;
}

.brand h1 {
    margin: 0;
    color: #0f172a;
    font-size: 20px;
    line-height: 1.4;
}

.brand p {
    margin: 5px 0 0;
    color: #64748b;
    font-size: 14px;
    line-height: 1.5;
}

.login-form {
    display: grid;
    gap: 20px;
}

.field {
    display: grid;
    gap: 8px;
}

.field label {
    color: #334155;
    font-size: 14px;
    font-weight: 600;
}

.field input {
    width: 100%;
    min-width: 0;
    padding: 12px 14px;
    border: 1px solid #cbd5e1;
    border-radius: 9px;
    outline: none;
    color: #0f172a;
    background: #ffffff;
    font-family: inherit;
    font-size: 16px;
    transition:
        border-color 0.2s,
        box-shadow 0.2s;
}

.field input::placeholder {
    color: #64748b;
}

.field input:focus {
    border-color: #0f766e;
    box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.15);
}

.password-wrapper {
    position: relative;
}

.password-wrapper input {
    padding-right: 72px;
}

.toggle-password {
    position: absolute;
    top: 50%;
    right: 8px;
    padding: 8px;
    border: 0;
    border-radius: 5px;
    transform: translateY(-50%);
    color: #0f766e;
    background: transparent;
    cursor: pointer;
    font-family: inherit;
    font-size: 13px;
    font-weight: 600;
}

.toggle-password:hover {
    color: #115e59;
    background: #f0fdfa;
}

.toggle-password:focus-visible,
.login-button:focus-visible {
    outline: 3px solid #155e75;
    outline-offset: 3px;
}

.login-button {
    padding: 13px 18px;
    border: 0;
    border-radius: 9px;
    color: #ffffff;
    background: #0f766e;
    cursor: pointer;
    font-family: inherit;
    font-size: 15px;
    font-weight: 700;
    transition:
        background 0.2s,
        transform 0.15s;
}

.login-button:hover:not(:disabled) {
    background: #115e59;
}

.login-button:active:not(:disabled) {
    transform: translateY(1px);
}

.login-button:disabled {
    opacity: 0.65;
    cursor: wait;
}

.error-message {
    margin: 0;
    padding: 12px 14px;
    border: 1px solid #fecaca;
    border-radius: 8px;
    color: #991b1b;
    background: #fef2f2;
    font-size: 13px;
    line-height: 1.6;
    overflow-wrap: anywhere;
}

.footer-text {
    margin: 28px 0 0;
    color: #64748b;
    text-align: center;
    font-size: 12px;
}

@media (max-width: 480px) {
    .login-page {
        padding: 16px;
    }

    .login-card {
        padding: 26px 20px;
    }

    .brand h1 {
        font-size: 18px;
    }
}

@media (prefers-reduced-motion: reduce) {
    .field input,
    .login-button {
        transition: none;
    }
}
</style>