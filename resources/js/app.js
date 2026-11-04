import './bootstrap';

import { createApp } from 'vue';
import Dashboard from './components/Dashboard.vue';
import Tickets from './components/Tickets.vue';
import Assets from './components/Assets.vue';
import Reservations from './components/Reservations.vue';

import Alpine from 'alpinejs';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY || process.env.MIX_PUSHER_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER || process.env.MIX_PUSHER_CLUSTER,
    encrypted: true,
    forceTLS: true,
});

const vueApp = createApp({
    data() {
        return {
            page: null,
            user: window.Auth ? window.Auth.user : null,
        };
    },
    mounted() {
        const appEl = document.getElementById('app');
        if (appEl) {
            this.page = appEl.dataset.page;
        }
    }
});

vueApp.component('dashboard', Dashboard);
vueApp.component('tickets-list', Tickets);
vueApp.component('assets-list', Assets);
vueApp.component('reservations-list', Reservations);

window.addEventListener('DOMContentLoaded', () => {
    const appEl = document.getElementById('app');
    if (appEl) {
        vueApp.mount('#app');
    }
});

window.Alpine = Alpine;

if (window.Echo && window.Auth && window.Auth.user) {
    window.Echo.private(`App.Models.User.${window.Auth.user.id}`)
        .notification((notif) => {
            alert(notif.type + ': ' + (notif.data.message || JSON.stringify(notif.data)));
        });
}

Alpine.start();

// Global AJAX form submit handler to avoid CSP form-action blocking in cPanel.
// This intercepts form submits and sends them via fetch instead of native form submission.
window.addEventListener('submit', async (event) => {
    const form = event.target;
    if (!(form instanceof HTMLFormElement)) {
        return;
    }

    const action = form.getAttribute('action') || window.location.pathname;
    const url = new URL(action, window.location.href);
    if (url.origin !== window.location.origin) {
        return; // Do not intercept cross-origin forms.
    }

    event.preventDefault();

    const method = (form.method || 'GET').toUpperCase();
    const formData = new FormData(form);
    if (!formData.has('_token')) {
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (token) {
            formData.append('_token', token);
        }
    }

    let requestUrl = url.toString();
    let fetchOptions = {
        credentials: 'same-origin',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'text/html,application/xhtml+xml',
        },
        redirect: 'follow',
    };

    if (method === 'GET') {
        const searchParams = new URLSearchParams();
        for (const [key, value] of formData.entries()) {
            searchParams.append(key, value);
        }
        requestUrl = `${url.pathname}${searchParams.toString() ? `?${searchParams.toString()}` : ''}`;
        fetchOptions.method = 'GET';
    } else {
        if (method !== 'POST') {
            formData.append('_method', method);
        }
        fetchOptions.method = 'POST';
        fetchOptions.body = formData;
    }

    try {
        const response = await fetch(requestUrl, fetchOptions);
        if (response.redirected) {
            window.location.href = response.url;
            return;
        }

        const contentType = response.headers.get('content-type') || '';
        if (contentType.includes('text/html')) {
            const html = await response.text();
            document.open();
            document.write(html);
            document.close();
            return;
        }

        if (!response.ok) {
            const html = await response.text();
            document.open();
            document.write(html);
            document.close();
        }
    } catch (error) {
        console.error('AJAX form submission failed:', error);
        form.submit();
    }
});
