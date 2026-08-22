import { createInertiaApp } from '@inertiajs/vue3';
import PrimeVue from 'primevue/config';
import ConfirmationService from 'primevue/confirmationservice';
import DialogService from 'primevue/dialogservice';
import ToastService from 'primevue/toastservice';
import Aura from '@primeuix/themes/aura';
import { createApp, h } from 'vue';
import { initializeTheme } from '@/composables/useAppearance';

import 'primeicons/primeicons.css';

const appName = import.meta.env.VITE_APP_NAME || 'SPMI';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(PrimeVue, {
                theme: {
                    preset: Aura,
                    options: {
                        darkModeSelector: false,
                    },
                },
            })
            .use(ToastService)
            .use(ConfirmationService)
            .use(DialogService)
            .mount(el);
    },
    layout: () => null,
    progress: {
        color: '#00479b',
    },
});

// This will set light / dark mode on page load...
initializeTheme();
