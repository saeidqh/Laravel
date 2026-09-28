<script setup>
import { reactive, ref } from 'vue';

const showPassword = ref(false);
const submitting = ref(false);
const errors = ref({});
const errorMessage = ref('');
const successMessage = ref('');

const initialForm = () => ({
    FirstName: '',
    LastName: '',
    UserName: '',
    Email: '',
    mobile: '',
    nationalCode: '',
    Password: '',
    isActive: '1',
    RequireChangePassword: '0',
});

const form = reactive(initialForm());

function fieldError(field) {
    return errors.value[field]?.[0] ?? '';
}

async function submit() {
    if (submitting.value) {
        return;
    }

    errors.value = {};
    errorMessage.value = '';
    successMessage.value = '';

    const url = document
        .querySelector('meta[name="user-store-url"]')
        ?.getAttribute('content');

    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute('content');

    if (!url || !csrfToken) {
        errorMessage.value = 'The form configuration is missing. Please reload the page.';
        return;
    }

    submitting.value = true;

    try {
        const response = await fetch(url, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({ ...form }),
        });

        const data = await response.json().catch(() => null);

        if (response.status === 422) {
            errors.value = data?.errors ?? {};
            errorMessage.value = 'Please correct the highlighted fields.';
            return;
        }

        if (response.status === 401 || response.status === 419) {
            errorMessage.value = 'Your session has expired. Reload the page and sign in again.';
            return;
        }

        if (response.status === 403) {
            errorMessage.value = data?.message || 'You do not have permission to create users.';
            return;
        }

        if (!response.ok || response.redirected || !data?.user?.id) {
            errorMessage.value = 'The server could not confirm the save. Check before trying again.';
            return;
        }

    successMessage.value = `${data.message} Username: ${data.user.username}`;

    Object.assign(form, initialForm());
    showPassword.value = false;

    if (data.redirect_url) {
        window.location.assign(data.redirect_url);
    }
    } catch {
        errorMessage.value = 'The request could not be completed. Check your connection and whether the user was saved before trying again.';
    } finally {
        submitting.value = false;
    }
}
</script>

<template>
    <div class="page" dir="ltr">
        <header class="topbar">
            <div class="brand">
                <span class="brand-icon" aria-hidden="true">E</span>

                <div>
                    <strong>IPC</strong>
                    <span>Management System</span>
                </div>
            </div>

            <a href="/dashboard" class="back-link">Back to Dashboard</a>
        </header>

        <main class="content">
            <div class="heading">
                <p class="eyebrow">USER MANAGEMENT</p>
                <h1>Create User</h1>
                <p>Enter the account information for a new user.</p>
            </div>

            <div v-if="successMessage" class="notice notice-success" role="status">
                {{ successMessage }}
            </div>

            <div v-if="errorMessage" class="notice notice-error" role="alert">
                {{ errorMessage }}
            </div>

            <section class="card" aria-labelledby="form-title">
                <div class="card-heading">
                    <h2 id="form-title">User information</h2>
                    <p>Fields marked with * are required.</p>
                </div>

                <form :aria-busy="submitting" @submit.prevent="submit">
                    <fieldset :disabled="submitting" class="form-fields">
                        <div class="form-grid">
                            <div class="field">
                                <label for="first-name">First name *</label>
                                <input
                                    id="first-name"
                                    v-model="form.FirstName"
                                    name="FirstName"
                                    type="text"
                                    maxlength="40"
                                    autocomplete="given-name"
                                    required
                                    :aria-invalid="!!fieldError('FirstName')"
                                    :aria-describedby="fieldError('FirstName') ? 'first-name-error' : undefined"
                                >
                                <p v-if="fieldError('FirstName')" id="first-name-error" class="field-error">
                                    {{ fieldError('FirstName') }}
                                </p>
                            </div>

                            <div class="field">
                                <label for="last-name">Last name *</label>
                                <input
                                    id="last-name"
                                    v-model="form.LastName"
                                    name="LastName"
                                    type="text"
                                    maxlength="40"
                                    autocomplete="family-name"
                                    required
                                    :aria-invalid="!!fieldError('LastName')"
                                    :aria-describedby="fieldError('LastName') ? 'last-name-error' : undefined"
                                >
                                <p v-if="fieldError('LastName')" id="last-name-error" class="field-error">
                                    {{ fieldError('LastName') }}
                                </p>
                            </div>

                            <div class="field">
                                <label for="username">Username *</label>
                                <input
                                    id="username"
                                    v-model="form.UserName"
                                    name="UserName"
                                    type="text"
                                    maxlength="40"
                                    autocomplete="off"
                                    autocapitalize="none"
                                    :spellcheck="false"
                                    required
                                    :aria-invalid="!!fieldError('UserName')"
                                    :aria-describedby="fieldError('UserName') ? 'username-error' : undefined"
                                >
                                <p v-if="fieldError('UserName')" id="username-error" class="field-error">
                                    {{ fieldError('UserName') }}
                                </p>
                            </div>

                            <div class="field">
                                <label for="email">Email</label>
                                <input
                                    id="email"
                                    v-model="form.Email"
                                    name="Email"
                                    type="email"
                                    maxlength="50"
                                    autocomplete="off"
                                    :aria-invalid="!!fieldError('Email')"
                                    :aria-describedby="fieldError('Email') ? 'email-error' : undefined"
                                >
                                <p v-if="fieldError('Email')" id="email-error" class="field-error">
                                    {{ fieldError('Email') }}
                                </p>
                            </div>

                            <div class="field">
                                <label for="mobile">Mobile</label>
                                <input
                                    id="mobile"
                                    v-model="form.mobile"
                                    name="mobile"
                                    type="tel"
                                    maxlength="50"
                                    autocomplete="off"
                                    :aria-invalid="!!fieldError('mobile')"
                                    :aria-describedby="fieldError('mobile') ? 'mobile-error' : undefined"
                                >
                                <p v-if="fieldError('mobile')" id="mobile-error" class="field-error">
                                    {{ fieldError('mobile') }}
                                </p>
                            </div>

                            <div class="field">
                                <label for="national-code">National code</label>
                                <input
                                    id="national-code"
                                    v-model="form.nationalCode"
                                    name="nationalCode"
                                    type="text"
                                    maxlength="10"
                                    pattern="[0-9]{10}"
                                    inputmode="numeric"
                                    autocomplete="off"
                                    :aria-invalid="!!fieldError('nationalCode')"
                                    :aria-describedby="fieldError('nationalCode') ? 'national-code-error' : undefined"
                                >
                                <p v-if="fieldError('nationalCode')" id="national-code-error" class="field-error">
                                    {{ fieldError('nationalCode') }}
                                </p>
                            </div>

                            <div class="field">
                                <label for="password">Password *</label>

                                <div class="password-field">
                                    <input
                                        id="password"
                                        v-model="form.Password"
                                        name="Password"
                                        :type="showPassword ? 'text' : 'password'"
                                        minlength="8"
                                        maxlength="72"
                                        autocomplete="new-password"
                                        required
                                        :aria-invalid="!!fieldError('Password')"
                                        :aria-describedby="fieldError('Password') ? 'password-help password-error' : 'password-help'"
                                    >

                                    <button
                                        type="button"
                                        :aria-label="showPassword ? 'Hide password' : 'Show password'"
                                        :aria-pressed="showPassword"
                                        @click="showPassword = !showPassword"
                                    >
                                        {{ showPassword ? 'Hide' : 'Show' }}
                                    </button>
                                </div>

                                <p id="password-help" class="field-help">Use at least 8 characters.</p>
                                <p v-if="fieldError('Password')" id="password-error" class="field-error">
                                    {{ fieldError('Password') }}
                                </p>
                            </div>

                            <div class="field">
                                <label for="is-active">Account status</label>
                                <select
                                    id="is-active"
                                    v-model="form.isActive"
                                    name="isActive"
                                    :aria-invalid="!!fieldError('isActive')"
                                    :aria-describedby="fieldError('isActive') ? 'is-active-error' : undefined"
                                >
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                                <p v-if="fieldError('isActive')" id="is-active-error" class="field-error">
                                    {{ fieldError('isActive') }}
                                </p>
                            </div>

                            <div class="field">
                                <label for="require-change-password">Password change on login</label>
                                <select
                                    id="require-change-password"
                                    v-model="form.RequireChangePassword"
                                    name="RequireChangePassword"
                                    :aria-invalid="!!fieldError('RequireChangePassword')"
                                    :aria-describedby="fieldError('RequireChangePassword') ? 'require-change-password-error' : undefined"
                                >
                                    <option value="0">Not required</option>
                                    <option value="1">Required</option>
                                </select>
                                <p
                                    v-if="fieldError('RequireChangePassword')"
                                    id="require-change-password-error"
                                    class="field-error"
                                >
                                    {{ fieldError('RequireChangePassword') }}
                                </p>
                            </div>
                        </div>
                    </fieldset>

                    <div class="card-footer">
                        <p>Organizational positions and permissions are assigned separately.</p>

                        <button type="submit" :disabled="submitting">
                            {{ submitting ? 'Saving...' : 'Create User' }}
                        </button>
                    </div>
                </form>
            </section>
        </main>
    </div>
</template>

<style scoped>
.page,
.page * {
    box-sizing: border-box;
}

.page {
    min-height: 100vh;
    min-height: 100dvh;
    color: #0f172a;
    background: #f3f6fa;
    font-family: Arial, sans-serif;
}

.topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 16px 32px;
    border-bottom: 1px solid #e2e8f0;
    background: #ffffff;
}

.brand {
    display: flex;
    align-items: center;
    gap: 12px;
}

.brand-icon {
    display: grid;
    width: 44px;
    height: 44px;
    place-items: center;
    border-radius: 12px;
    color: #ffffff;
    background: #0f766e;
    font-size: 24px;
    font-weight: 700;
}

.brand strong {
    display: block;
    font-size: 20px;
}

.brand span:last-child {
    color: #64748b;
    font-size: 12px;
}

.back-link {
    color: #0f766e;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
}

.content {
    max-width: 1050px;
    margin: 0 auto;
    padding: 36px 32px;
}

.heading {
    margin-bottom: 26px;
}

.eyebrow {
    margin: 0 0 10px;
    color: #0f766e;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 1.5px;
}

.heading h1 {
    margin: 0;
    font-size: 28px;
}

.heading > p:last-child,
.card-heading p,
.card-footer p {
    color: #64748b;
    font-size: 14px;
    line-height: 1.6;
}

.card {
    overflow: hidden;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    background: #ffffff;
}

.card-heading,
.card-footer {
    padding: 22px 24px;
}

.card-heading {
    border-bottom: 1px solid #eef2f6;
}

.card-heading h2 {
    margin: 0;
    font-size: 17px;
}

.card-heading p {
    margin: 8px 0 0;
}

.form-fields {
    min-width: 0;
    margin: 0;
    padding: 0;
    border: 0;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 22px;
    padding: 28px 24px;
}

.field {
    display: flex;
    min-width: 0;
    flex-direction: column;
    gap: 8px;
}

.field label {
    color: #334155;
    font-size: 14px;
    font-weight: 600;
}

.field input,
.field select {
    width: 100%;
    min-height: 44px;
    padding: 10px 12px;
    border: 1px solid #cbd5e1;
    border-radius: 9px;
    color: #0f172a;
    background: #ffffff;
    font: inherit;
    font-size: 14px;
}

.field input:focus,
.field select:focus {
    border-color: #0f766e;
    outline: 3px solid rgba(13, 148, 136, 0.15);
}

.field input[aria-invalid="true"],
.field select[aria-invalid="true"] {
    border-color: #b91c1c;
}

.password-field {
    position: relative;
}

.password-field input {
    padding-right: 75px;
}

.password-field button {
    position: absolute;
    top: 50%;
    right: 8px;
    transform: translateY(-50%);
    padding: 7px;
    border: 0;
    color: #0f766e;
    background: transparent;
    cursor: pointer;
    font-weight: 600;
}

.field-error,
.field-help {
    margin: 0;
    font-size: 12px;
    line-height: 1.5;
}

.field-error {
    color: #b91c1c;
}

.field-help {
    color: #64748b;
}

.card-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    border-top: 1px solid #eef2f6;
}

.card-footer p {
    margin: 0;
}

.card-footer > button {
    padding: 11px 18px;
    border: 0;
    border-radius: 9px;
    color: #ffffff;
    background: #0f766e;
    font-weight: 700;
    white-space: nowrap;
    cursor: pointer;
}

.card-footer > button:hover:not(:disabled) {
    background: #115e59;
}

.card-footer > button:disabled {
    opacity: 0.6;
    cursor: wait;
}

button:focus-visible,
.back-link:focus-visible {
    outline: 3px solid #14b8a6;
    outline-offset: 3px;
}

.notice {
    margin-bottom: 20px;
    padding: 14px 16px;
    border: 1px solid;
    border-radius: 10px;
    font-size: 14px;
    line-height: 1.6;
    overflow-wrap: anywhere;
}

.notice-success {
    border-color: #a7f3d0;
    color: #065f46;
    background: #ecfdf5;
}

.notice-error {
    border-color: #fecaca;
    color: #991b1b;
    background: #fef2f2;
}

@media (max-width: 650px) {
    .topbar,
    .content {
        padding-right: 16px;
        padding-left: 16px;
    }

    .form-grid {
        grid-template-columns: 1fr;
    }

    .card-footer {
        align-items: stretch;
        flex-direction: column;
    }
}
</style>