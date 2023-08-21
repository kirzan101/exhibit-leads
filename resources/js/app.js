import Vue from "vue";
import { createInertiaApp } from "@inertiajs/vue2";
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue'
import vueDebounce from 'vue-debounce'
import Layout from '@/Pages/Layout.vue';


// Import Bootstrap and BootstrapVue CSS files (order is important)
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'

// Make BootstrapVue available throughout your project
Vue.use(BootstrapVue)
// Optionally install the BootstrapVue icon components plugin
Vue.use(IconsPlugin)
Vue.use(vueDebounce)

createInertiaApp({
    resolve: (name) => {
        const pages = import.meta.glob("./Pages/**/*.vue", { eager: true });
        const page = pages[`./Pages/${name}.vue`]

        // only pages that has no layout defined in the component will render the layout
        if(page.default.layout === undefined) {
            page.default.layout = Layout;
        }

        return page
    },
    setup({ el, App, props, plugin }) {
        Vue.use(plugin);

        new Vue({
            render: (h) => h(App, props),
        }).$mount(el);
    },
});
