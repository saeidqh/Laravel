<script setup>
import AppLayout from './layout/AppLayout.vue';

defineProps({
    username: {
        type: String,
        default: '',
    },
    logoutUrl: {
        type: String,
        default: '',
    },
    csrfToken: {
        type: String,
        default: '',
    },
    dashboardUrl: {
        type: String,
        required: true,
    },
    listUrl: {
        type: String,
        default: '',
    },
    canList: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <AppLayout
        active-page="dashboard"
        breadcrumb="Workspace / Dashboard"
        :username="username"
        :logout-url="logoutUrl"
        :csrf-token="csrfToken"
        :dashboard-url="dashboardUrl"
        :list-url="listUrl"
        :can-list="canList"
    >
        <div class="ipc-page-heading">
            <div>
                <p class="ipc-eyebrow">YOUR WORKSPACE</p>
                <h1>Dashboard</h1>

                <p class="ipc-page-description">
                    Welcome back{{ username ? `, ${username}` : '' }}.
                </p>
            </div>
        </div>

        <section class="welcome-card" aria-labelledby="welcome-title">
            <div class="welcome-icon" aria-hidden="true">IPC</div>

            <div>
                <h2 id="welcome-title">Welcome to IPC</h2>

                <p>
                    Access your available modules using the navigation menu.
                </p>
            </div>
        </section>

        <section class="ipc-card module-panel" aria-labelledby="modules-title">
            <div class="ipc-card-heading">
                <h2 id="modules-title">Workspace modules</h2>
            </div>

            <div class="module-content">
                <a
                    v-if="canList && listUrl"
                    :href="listUrl"
                    class="module-link"
                >
                    <strong>Users</strong>
                    <span>Manage user accounts and account status.</span>
                </a>

                <p v-else class="module-empty">
                    No modules are available to display.
                </p>
            </div>
        </section>
    </AppLayout>
</template>

<style scoped>
.welcome-card {
    display: flex;
    align-items: center;
    gap: 20px;
    padding: 28px;
    border: 1px solid var(--ipc-border);
    border-radius: 16px;
    background: var(--ipc-primary-soft);
}

.welcome-icon {
    display: grid;
    width: 60px;
    height: 60px;
    flex-shrink: 0;
    place-items: center;
    border-radius: 16px;
    color: #ffffff;
    background: var(--ipc-primary);
    font-size: 20px;
    font-weight: 700;
}

.welcome-card h2 {
    margin: 0 0 8px;
    font-size: 20px;
}

.welcome-card p {
    margin: 0;
    color: var(--ipc-muted);
    font-size: 14px;
    line-height: 1.8;
}

.module-panel {
    margin-top: 24px;
}

.module-content {
    padding: 24px;
}

.module-link {
    display: flex;
    flex-direction: column;
    gap: 10px;
    max-width: 360px;
    padding: 24px;
    border: 1px solid var(--ipc-border);
    border-radius: 12px;
    color: var(--ipc-text);
    background: var(--ipc-surface-soft);
    text-decoration: none;
}

.module-link:hover {
    border-color: #14b8a6;
}

.module-link span,
.module-empty {
    color: var(--ipc-muted);
    font-size: 14px;
    line-height: 1.7;
}

.module-empty {
    margin: 0;
}

@media (max-width: 600px) {
    .welcome-card {
        align-items: flex-start;
        flex-direction: column;
        padding: 22px;
    }
}
</style>