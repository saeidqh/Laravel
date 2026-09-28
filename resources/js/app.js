import { createApp } from 'vue';

import LoginForm from './components/LoginForm.vue';
import Dashboard from './components/Dashboard.vue';
import UserCreate from './components/user/UserCreate.vue';
import UserList from './components/user/UserList.vue';

const element = document.getElementById('app');

function getAccountProps(element) {
    return {
        username: element.dataset.username || '',
        logoutUrl: element.dataset.logoutUrl || '',
        csrfToken: element.dataset.csrfToken || '',
    };
}

function mountPage(element) {
    const page = element.dataset.page;
    const accountProps = getAccountProps(element);

    if (page === 'dashboard') {
        createApp(Dashboard, {
            ...accountProps,
            dashboardUrl: element.dataset.dashboardUrl,
            listUrl: element.dataset.listUrl || '',
            canList: element.dataset.canList === 'true',
        }).mount(element);

        return;
    }

    if (page === 'user-list') {
        const pageProps = JSON.parse(element.dataset.props || '{}');

        createApp(UserList, {
            ...pageProps,
            ...accountProps,
        }).mount(element);

        return;
    }

    if (page === 'user-create') {
        // رفتار فعلی فرم ایجاد کاربر حفظ شده است.
        createApp(UserCreate).mount(element);

        return;
    }

    createApp(LoginForm, {
        loginUrl: element.dataset.loginUrl,
        csrfToken: element.dataset.csrfToken,
        initialUsername: element.dataset.username,
        errorMessage: element.dataset.error,
    }).mount(element);
}

if (element) {
    mountPage(element);
}