import "./bootstrap";
import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import "primeicons/primeicons.css";
import PrimeVue from "primevue/config";
import ToastService from "primevue/toastservice";
import ConfirmationService from 'primevue/confirmationservice'
import DialogService from 'primevue/dialogservice'
import { Form } from "@primevue/forms";
import Aura from "@primeuix/themes/aura";
import {
    AutoComplete,
    Avatar, Badge,
    Button, ButtonGroup, Card,
    Column,
    ColumnGroup,
    DataTable,
    Dialog,
    FloatLabel,
    IconField,
    Image, InputGroup, InputGroupAddon,
    InputIcon,
    InputText,
    Menu,
    Menubar,
    MultiSelect, OverlayBadge, Panel,
    Password,
    ProgressSpinner,
    Ripple,
    Row, ScrollPanel,
    Select,
    Skeleton, Step, StepItem, StepList, StepPanel, StepPanels, Stepper,
    Textarea,
    TieredMenu, Toast,
    Toolbar, Tooltip,
} from "primevue";

import Layout from "./components/app.vue";

createInertiaApp({
    title: (title) => `${title} - SPMI`,
    resolve: async (name) => {
        const page = await resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue'));
        page.default.layout = page.default.layout === undefined ? Layout : page.default.layout;
        return page;
    },
    setup({ el, App, props, plugin }) {
        const appVue = createApp({ render: () => h(App, props) });
        
        appVue.use(plugin);
        appVue.use(PrimeVue, {
            theme: {
                preset: Aura,
                options: {
                    darkModeSelector: false,
                },
                ripple: true,
            },
        });
        appVue.use(ToastService);
        appVue.use(ConfirmationService);
        appVue.use(DialogService);
        appVue.directive('tooltip', Tooltip)
        appVue.component("Form", Form);
        appVue.component("Toast", Toast);
        appVue.component("Avatar", Avatar);
        appVue.component("Menubar", Menubar);
        appVue.component("Badge", Badge);
        appVue.component("OverlayBadge", OverlayBadge);
        appVue.component("Button", Button);
        appVue.component("ButtonGroup", ButtonGroup);
        appVue.component("Image", Image);
        appVue.component("Card", Card);
        appVue.component("ScrollPanel", ScrollPanel);
        appVue.component("Panel", Panel);
        appVue.component("InputGroup", InputGroup);
        appVue.component("InputGroupAddon", InputGroupAddon);
        appVue.component("InputText", InputText);
        appVue.component("InputIcon", InputIcon);
        appVue.component("IconField", IconField);
        appVue.component("AutoComplete", AutoComplete);
        appVue.component("Select", Select);
        appVue.component("Textarea", Textarea);
        appVue.component("FloatLabel", FloatLabel);
        appVue.component("Password", Password);
        appVue.component("DataTable", DataTable);
        appVue.component("Row", Row);
        appVue.component("Column", Column);
        appVue.component("ColumnGroup", ColumnGroup);
        appVue.component("MultiSelect", MultiSelect);
        appVue.component("ProgressSpinner", ProgressSpinner);
        appVue.component("TieredMenu", TieredMenu);
        appVue.component("Toolbar", Toolbar);
        appVue.component("Skeleton", Skeleton);
        appVue.component("Menu", Menu);
        appVue.component("Dialog", Dialog);
        appVue.component("Stepper", Stepper);
        appVue.component("StepList", StepList);
        appVue.component("StepPanels", StepPanels);
        appVue.component("StepItem", StepItem);
        appVue.component("Step", Step);
        appVue.component("StepPanel", StepPanel);
        appVue.directive("ripple", Ripple);

        if (import.meta.env.DEV) {
            appVue.config.devtools = true;
        }

        appVue.mount(el);
    },
    progress: {
        color: '#29d',
        showSpinner: true,
    },
});
