<script setup>
import { computed, ref } from 'vue';
import AppLayout from '../layout/AppLayout.vue';

const props = defineProps({
    initialUsers: {
        type: Object,
        required: true,
    },
    initialSearch: {
        type: String,
        default: '',
    },
    listUrl: {
        type: String,
        required: true,
    },
    createUrl: {
        type: String,
        required: true,
    },
    dashboardUrl: {
        type: String,
        required: true,
    },
    canCreate: {
        type: Boolean,
        default: false,
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
});

const users = ref(props.initialUsers);
const search = ref(props.initialSearch);
const appliedSearch = ref(props.initialSearch);
const loading = ref(false);
const errorMessage = ref('');

const rows = computed(() => users.value.data ?? []);
const currentPage = computed(() => Number(users.value.current_page) || 1);
const lastPage = computed(() => Number(users.value.last_page) || 1);

const summary = computed(() => {
    if (!Number(users.value.total)) {
        return 'No users found';
    }

    if (!rows.value.length) {
        return `No users on this page (${users.value.total} total)`;
    }

    return `${users.value.from}–${users.value.to} of ${users.value.total} users`;
});

function isActive(value) {
    return value === true || value === 1 || value === '1';
}

async function loadUsers(page = 1, query = appliedSearch.value) {
    if (loading.value) {
        return;
    }

    loading.value = true;
    errorMessage.value = '';

    try {
        const normalizedQuery = query.trim();
        const url = new URL(props.listUrl, window.location.origin);

        url.searchParams.set('page', String(page));
        url.searchParams.delete('search');

        if (normalizedQuery) {
            url.searchParams.set('search', normalizedQuery);
        }

        const response = await fetch(url, {
            credentials: 'same-origin',
            headers: {
                Accept: 'application/json',
            },
        });

        const data = await response.json().catch(() => null);

        if (response.status === 401 || response.status === 419) {
            errorMessage.value =
                'Your session has expired. Please sign in again.';
            return;
        }

        if (response.status === 403) {
            errorMessage.value =
                data?.message || 'You do not have permission to view users.';
            return;
        }

        if (response.status === 422) {
            errorMessage.value =
                data?.errors?.search?.[0]
                || data?.errors?.page?.[0]
                || 'Please check the search value.';
            return;
        }

        if (
            !response.ok
            || response.redirected
            || !Array.isArray(data?.data)
        ) {
            errorMessage.value =
                'Users could not be loaded. Please try again.';
            return;
        }

        users.value = data;
        appliedSearch.value = normalizedQuery;
    } catch {
        errorMessage.value = 'Connection failed. Please try again.';
    } finally {
        loading.value = false;
    }
}

function submitSearch() {
    return loadUsers(1, search.value);
}

function clearSearch() {
    search.value = '';
    return loadUsers(1, '');
}
</script>

<template>
    <AppLayout
        active-page="user-list"
        breadcrumb="Basic Information / Users"
        :username="username"
        :logout-url="logoutUrl"
        :csrf-token="csrfToken"
        :dashboard-url="dashboardUrl"
        :list-url="listUrl"
        :can-list="true"
    >
        <div class="ipc-page-heading">
            <div>
                <p class="ipc-eyebrow">USER MANAGEMENT</p>
                <h1>Users</h1>

                <p class="ipc-page-description">
                    Manage user accounts and account status.
                </p>
            </div>

            <a
                v-if="canCreate"
                :href="createUrl"
                class="ipc-button ipc-button-primary"
            >
                + Create User
            </a>
        </div>

        <div v-if="errorMessage" class="ipc-notice" role="alert">
            {{ errorMessage }}
        </div>

        <section class="ipc-card" aria-labelledby="users-title">
            <div class="ipc-card-heading">
                <h2 id="users-title">User list</h2>
                <span>{{ users.total }} users</span>
            </div>

            <form class="search-form" @submit.prevent="submitSearch">
                <label for="user-search" class="ipc-sr-only">
                    Search users
                </label>

                <input
                    id="user-search"
                    v-model="search"
                    type="search"
                    maxlength="100"
                    placeholder="Search name, username, email or mobile..."
                    :disabled="loading"
                >

                <button
                    type="submit"
                    class="ipc-button ipc-button-primary"
                    :disabled="loading"
                >
                    {{ loading ? 'Loading...' : 'Search' }}
                </button>

                <button
                    type="button"
                    class="ipc-button ipc-button-secondary"
                    :disabled="loading"
                    @click="clearSearch"
                >
                    Clear
                </button>
            </form>

            <div
                class="table-wrapper"
                :aria-busy="loading"
                tabindex="0"
                role="region"
                aria-label="Users table"
            >
                <table>
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Name</th>
                            <th scope="col">Username</th>
                            <th scope="col">Email</th>
                            <th scope="col">Mobile</th>
                            <th scope="col">Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr v-for="user in rows" :key="user.UserID">
                            <td class="muted">{{ user.UserID }}</td>

                            <td class="user-name">
                                {{ user.FirstName }} {{ user.LastName }}
                            </td>

                            <td>{{ user.UserName }}</td>
                            <td>{{ user.Email || '—' }}</td>
                            <td>{{ user.mobile || '—' }}</td>

                            <td>
                                <span
                                    class="status"
                                    :class="
                                        isActive(user.isActive)
                                            ? 'status-active'
                                            : 'status-inactive'
                                    "
                                >
                                    {{
                                        isActive(user.isActive)
                                            ? 'Active'
                                            : 'Inactive'
                                    }}
                                </span>
                            </td>
                        </tr>

                        <tr v-if="!rows.length">
                            <td colspan="6" class="empty-state">
                                No users match your search.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <footer class="card-footer">
                <p role="status" aria-live="polite">
                    {{ loading ? 'Loading users...' : summary }}
                </p>

                <nav class="pagination" aria-label="Users pagination">
                    <button
                        type="button"
                        class="ipc-button ipc-button-secondary"
                        :disabled="loading || currentPage <= 1"
                        @click="loadUsers(currentPage - 1)"
                    >
                        Previous
                    </button>

                    <span>{{ currentPage }} / {{ lastPage }}</span>

                    <button
                        type="button"
                        class="ipc-button ipc-button-secondary"
                        :disabled="loading || currentPage >= lastPage"
                        @click="loadUsers(currentPage + 1)"
                    >
                        Next
                    </button>
                </nav>
            </footer>
        </section>
    </AppLayout>
</template>

<style scoped>
.search-form {
    display: flex;
    gap: 10px;
    padding: 20px 24px;
}

.search-form input {
    flex: 1;
    min-width: 0;
    min-height: 42px;
    padding: 10px 12px;
    border: 1px solid var(--ipc-border);
    border-radius: 9px;
    color: var(--ipc-text);
    background: var(--ipc-surface);
    font-size: 14px;
}

.search-form input::placeholder {
    color: var(--ipc-muted);
}

.table-wrapper {
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
    text-align: left;
    font-size: 14px;
}

th,
td {
    padding: 16px 24px;
    border-bottom: 1px solid var(--ipc-border);
    white-space: nowrap;
}

th {
    color: var(--ipc-muted);
    background: var(--ipc-surface-soft);
    font-size: 12px;
    font-weight: 600;
}

tbody tr:hover {
    background: var(--ipc-surface-soft);
}

.user-name {
    font-weight: 600;
}

.muted {
    color: var(--ipc-muted);
}

.status {
    display: inline-block;
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.status-active {
    color: #047857;
    background: #ecfdf5;
}

.status-inactive {
    color: #b91c1c;
    background: #fef2f2;
}

.empty-state {
    padding: 48px 24px;
    color: var(--ipc-muted);
    text-align: center;
    white-space: normal;
}

.card-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 20px 24px;
}

.card-footer p {
    margin: 0;
    color: var(--ipc-muted);
    font-size: 13px;
}

.pagination {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 13px;
}

@media (max-width: 650px) {
    .search-form {
        flex-wrap: wrap;
    }

    .search-form input {
        flex-basis: 100%;
    }

    .card-footer {
        align-items: stretch;
        flex-direction: column;
    }

    .pagination {
        justify-content: space-between;
    }
}
</style>