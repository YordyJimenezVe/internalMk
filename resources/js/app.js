import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';

/* import the fontawesome core */
import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import {
    faUser, faRightFromBracket, faSun, faMoon, faHouse,
    faBox, faListCheck, faUsers, faShieldHalved, faBook,
    faFileInvoiceDollar, faChartPie, faBarcode, faChevronLeft,
    faChevronRight, faWrench, faCube, faHandHoldingDollar,
    faBoxesStacked, faScrewdriverWrench
} from '@fortawesome/free-solid-svg-icons'

library.add(
    faUser, faRightFromBracket, faSun, faMoon, faHouse,
    faBox, faListCheck, faUsers, faShieldHalved, faBook,
    faFileInvoiceDollar, faChartPie, faBarcode, faChevronLeft,
    faChevronRight, faWrench, faCube, faHandHoldingDollar,
    faBoxesStacked, faScrewdriverWrench
)

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .component('font-awesome-icon', FontAwesomeIcon)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
