<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue';

const props = defineProps({
    activePage: {
        type: String,
        required: true,
    },
    breadcrumb: {
        type: String,
        required: true,
    },
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

const sidebarOpen = ref(false);
const darkMode = ref(false);

// تعریف مرکزی منو؛ ماژول‌های بعدی به همین ساختار اضافه می‌شوند.
const menu = computed(() => [
    {
        key: 'dashboard',
        label: 'Dashboard',
        url: props.dashboardUrl,
    },
    {
        key: 'basic-information',
        label: 'Basic Information',
        children: [
            {
                key: 'user-list',
                label: 'Users',
                url: props.listUrl,
                visible: props.canList && Boolean(props.listUrl),
            },
        ],
    },
]);

const visibleMenu = computed(() => {
    return menu.value
        .filter((item) => item.visible !== false)
        .map((item) => {
            if (!item.children) {
                return item;
            }

            return {
                ...item,
                children: item.children.filter(
                    (child) => child.visible !== false,
                ),
            };
        })
        .filter((item) => !item.children || item.children.length > 0);
});

function toggleTheme() {
    darkMode.value = !darkMode.value;

    try {
        localStorage.setItem(
            'ipc-theme',
            darkMode.value ? 'dark' : 'light',
        );
    } catch {
        // تم حتی در صورت غیرفعال بودن storage کار می‌کند.
    }
}

function closeSidebar() {
    sidebarOpen.value = false;
}

function handleKeydown(event) {
    if (event.key === 'Escape') {
        closeSidebar();
    }
}

onMounted(() => {
    try {
        const savedTheme = localStorage.getItem('ipc-theme');

        darkMode.value = savedTheme
            ? savedTheme === 'dark'
            : window.matchMedia('(prefers-color-scheme: dark)').matches;
    } catch {
        darkMode.value = false;
    }

    window.addEventListener('keydown', handleKeydown);
});

onUnmounted(() => {
    window.removeEventListener('keydown', handleKeydown);
});
</script>

<template>
    <div
        class="ipc-shell"
        :data-theme="darkMode ? 'dark' : 'light'"
        dir="ltr"
    >
        <a class="ipc-skip-link" href="#main-content">
            Skip to content
        </a>

        <button
            v-if="sidebarOpen"
            type="button"
            class="ipc-backdrop"
            aria-label="Close navigation"
            @click="closeSidebar"
        ></button>

        <aside
            id="main-sidebar"
            class="ipc-sidebar"
            :class="{ 'is-open': sidebarOpen }"
        >
            <a :href="dashboardUrl" class="ipc-brand">
                <span class="ipc-brand-icon" aria-hidden="true">IPC</span>

                <span>
                    <strong>IPC</strong>
                    <small>Management System</small>
                </span>
            </a>

            <div class="ipc-navigation-label">WORKSPACE</div>

            <nav class="ipc-navigation" aria-label="Main navigation">
                <template v-for="item in visibleMenu" :key="item.key">
                    <details
                        v-if="item.children"
                        class="ipc-menu-group"
                        open
                    >
                        <summary>{{ item.label }}</summary>

                        <a
                            v-for="child in item.children"
                            :key="child.key"
                            :href="child.url"
                            class="ipc-nav-link"
                            :class="{ active: activePage === child.key }"
                            :aria-current="
                                activePage === child.key ? 'page' : undefined
                            "
                            @click="closeSidebar"
                        >
                            {{ child.label }}
                        </a>
                    </details>

                    <a
                        v-else
                        :href="item.url"
                        class="ipc-nav-link"
                        :class="{ active: activePage === item.key }"
                        :aria-current="
                            activePage === item.key ? 'page' : undefined
                        "
                        @click="closeSidebar"
                    >
                        {{ item.label }}
                    </a>
                </template>
            </nav>

            <div class="ipc-sidebar-footer">
                <span class="ipc-status-dot" aria-hidden="true"></span>
                IPC Workspace
            </div>
        </aside>

        <div class="ipc-workspace">
            <header class="ipc-topbar">
                <div class="ipc-topbar-start">
                    <button
                        type="button"
                        class="ipc-button ipc-button-secondary ipc-menu-button"
                        aria-controls="main-sidebar"
                        :aria-expanded="sidebarOpen"
                        @click="sidebarOpen = !sidebarOpen"
                    >
                        Menu
                    </button>

                    <span class="ipc-breadcrumb">{{ breadcrumb }}</span>
                </div>

                <div class="ipc-account">
                    <button
                        type="button"
                        class="ipc-button ipc-button-secondary"
                        :aria-pressed="darkMode"
                        aria-label="Dark mode"
                        @click="toggleTheme"
                    >
                        {{ darkMode ? 'Light Mode' : 'Dark Mode' }}
                    </button>

                    <span v-if="username" class="ipc-username">
                        {{ username }}
                    </span>

                    <form
                        v-if="logoutUrl && csrfToken"
                        :action="logoutUrl"
                        method="POST"
                    >
                        <input
                            type="hidden"
                            name="_token"
                            :value="csrfToken"
                        >

                        <button
                            type="submit"
                            class="ipc-button ipc-button-secondary"
                        >
                            Sign Out
                        </button>
                    </form>
                </div>
            </header>

            <main
                id="main-content"
                class="ipc-content"
                tabindex="-1"
            >
                <slot />
            </main>

            <footer class="ipc-workspace-footer">
                IPC Management System
            </footer>
        </div>
    </div>
</template>

<style>
/* استایل‌های مشترک فقط در محدوده قالب IPC اعمال می‌شوند. */
.ipc-shell,
.ipc-shell * {
    box-sizing: border-box;
}

.ipc-shell {
    --ipc-bg: #f3f6fa;
    --ipc-surface: #ffffff;
    --ipc-surface-soft: #f8fafc;
    --ipc-text: #0f172a;
    --ipc-muted: #64748b;
    --ipc-border: #e2e8f0;
    --ipc-primary: #0f766e;
    --ipc-primary-hover: #115e59;
    --ipc-primary-soft: #e6f5f1;
    --ipc-sidebar-width: 248px;

    min-height: 100vh;
    min-height: 100dvh;
    color: var(--ipc-text);
    background: var(--ipc-bg);
    font-family: Arial, sans-serif;
    color-scheme: light;
}

.ipc-shell[data-theme="dark"] {
    --ipc-bg: #0b1220;
    --ipc-surface: #111c2e;
    --ipc-surface-soft: #172438;
    --ipc-text: #e2e8f0;
    --ipc-muted: #a8b6ca;
    --ipc-border: #2b3b52;
    --ipc-primary: #0f766e;
    --ipc-primary-hover: #115e59;
    --ipc-primary-soft: #123d3b;

    color-scheme: dark;
}

.ipc-shell button,
.ipc-shell input {
    font-family: inherit;
}

.ipc-shell :is(button, a, input, summary, [tabindex]):focus-visible {
    outline: 3px solid #14b8a6;
    outline-offset: 3px;
}

.ipc-sidebar {
    position: fixed;
    inset: 0 auto 0 0;
    z-index: 30;
    display: flex;
    flex-direction: column;
    width: var(--ipc-sidebar-width);
    padding: 28px 18px 20px;
    overflow-y: auto;
    border-right: 1px solid var(--ipc-border);
    background: var(--ipc-surface);
}

.ipc-brand {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 0 8px 30px;
    color: var(--ipc-text);
    text-decoration: none;
}

.ipc-brand-icon {
    display: grid;
    width: 44px;
    height: 44px;
    flex-shrink: 0;
    place-items: center;
    border-radius: 12px;
    color: #ffffff;
    background: var(--ipc-primary);
    font-size: 15px;
    font-weight: 700;
}

.ipc-brand strong {
    display: block;
    font-size: 22px;
}

.ipc-brand small {
    display: block;
    margin-top: 4px;
    color: var(--ipc-muted);
    font-size: 11px;
}

.ipc-navigation-label {
    margin: 12px;
    color: var(--ipc-muted);
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 1.4px;
}

.ipc-nav-link {
    display: block;
    margin: 5px 0;
    padding: 13px 14px;
    border-radius: 9px;
    color: var(--ipc-text);
    text-decoration: none;
    font-size: 14px;
}

.ipc-nav-link:hover {
    background: var(--ipc-surface-soft);
}

.ipc-nav-link.active {
    color: var(--ipc-primary);
    background: var(--ipc-primary-soft);
    font-weight: 700;
}

.ipc-shell[data-theme="dark"] .ipc-nav-link.active {
    color: #5eead4;
}

.ipc-menu-group {
    margin-top: 18px;
}

.ipc-menu-group summary {
    padding: 12px;
    color: var(--ipc-muted);
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
}

.ipc-menu-group .ipc-nav-link {
    margin-left: 14px;
}

.ipc-sidebar-footer {
    display: flex;
    align-items: center;
    gap: 9px;
    margin-top: auto;
    padding: 30px 12px 0;
    color: var(--ipc-muted);
    font-size: 12px;
}

.ipc-status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #14b8a6;
}

.ipc-workspace {
    display: flex;
    flex-direction: column;
    min-width: 0;
    min-height: 100vh;
    min-height: 100dvh;
    margin-left: var(--ipc-sidebar-width);
}

.ipc-topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    min-height: 76px;
    padding: 16px 32px;
    border-bottom: 1px solid var(--ipc-border);
    background: var(--ipc-surface);
}

.ipc-topbar-start,
.ipc-account {
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 0;
}

.ipc-account form {
    margin: 0;
}

.ipc-breadcrumb,
.ipc-username {
    font-size: 13px;
}

.ipc-breadcrumb {
    color: var(--ipc-muted);
}

.ipc-username {
    max-width: 160px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    font-weight: 600;
}

.ipc-content {
    width: 100%;
    max-width: 1440px;
    margin: 0 auto;
    padding: 32px;
}

.ipc-workspace-footer {
    margin-top: auto;
    padding: 20px 32px;
    color: var(--ipc-muted);
    font-size: 12px;
}

.ipc-page-heading {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    margin-bottom: 26px;
}

.ipc-page-heading h1 {
    margin: 0;
    font-size: 28px;
}

.ipc-page-description {
    margin: 10px 0 0;
    color: var(--ipc-muted);
    font-size: 14px;
    line-height: 1.7;
    overflow-wrap: anywhere;
}

.ipc-eyebrow {
    margin: 0 0 10px;
    color: var(--ipc-muted);
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 1.5px;
}

.ipc-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 40px;
    padding: 10px 14px;
    border: 1px solid transparent;
    border-radius: 9px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    white-space: nowrap;
    cursor: pointer;
}

.ipc-button-primary {
    color: #ffffff;
    background: var(--ipc-primary);
}

.ipc-button-primary:hover:not(:disabled) {
    background: var(--ipc-primary-hover);
}

.ipc-button-secondary {
    border-color: var(--ipc-border);
    color: var(--ipc-text);
    background: var(--ipc-surface);
}

.ipc-button-secondary:hover:not(:disabled) {
    background: var(--ipc-surface-soft);
}

.ipc-button:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.ipc-card {
    overflow: hidden;
    border: 1px solid var(--ipc-border);
    border-radius: 16px;
    background: var(--ipc-surface);
}

.ipc-card-heading {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 22px 24px;
    border-bottom: 1px solid var(--ipc-border);
}

.ipc-card-heading h2 {
    margin: 0;
    font-size: 17px;
}

.ipc-card-heading > span {
    color: var(--ipc-muted);
    font-size: 13px;
}

.ipc-notice {
    margin-bottom: 20px;
    padding: 14px 16px;
    border: 1px solid #fecaca;
    border-radius: 10px;
    color: #991b1b;
    background: #fef2f2;
    line-height: 1.6;
}

.ipc-sr-only,
.ipc-skip-link:not(:focus) {
    position: absolute;
    width: 1px;
    height: 1px;
    overflow: hidden;
    clip-path: inset(50%);
    white-space: nowrap;
}

.ipc-skip-link:focus {
    position: fixed;
    top: 12px;
    left: 12px;
    z-index: 50;
    padding: 12px;
    color: var(--ipc-text);
    background: var(--ipc-surface);
}

.ipc-menu-button,
.ipc-backdrop {
    display: none;
}

@media (max-width: 900px) {
    .ipc-sidebar {
        display: none;
    }

    .ipc-sidebar.is-open {
        display: flex;
    }

    .ipc-workspace {
        margin-left: 0;
    }

    .ipc-menu-button {
        display: inline-flex;
    }

    .ipc-backdrop {
        position: fixed;
        inset: 0;
        z-index: 20;
        display: block;
        width: 100%;
        height: 100%;
        border: 0;
        background: rgb(15 23 42 / 55%);
    }
}

@media (max-width: 600px) {
    .ipc-topbar {
        flex-wrap: wrap;
        padding: 14px 16px;
    }

    .ipc-breadcrumb {
        display: none;
    }

    .ipc-account {
        flex-wrap: wrap;
        gap: 8px;
    }

    .ipc-username {
        max-width: 100px;
    }

    .ipc-content {
        padding: 24px 16px;
    }

    .ipc-page-heading {
        align-items: stretch;
        flex-direction: column;
    }

    .ipc-workspace-footer {
        padding: 20px 16px;
    }
}
</style>