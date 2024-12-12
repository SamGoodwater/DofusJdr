import "../css/app.css";
import "./bootstrap";

import { createInertiaApp, Head, Link } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { createApp, h } from "vue";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";
import DefaultLayout from "/resources/js/Layouts/main.vue";
import { Icons } from "./Utils/Icons";

const appName = import.meta.env.VITE_APP_NAME || "KrosmozJDR";
const appDescription = import.meta.env.VITE_APP_DESCRIPTION;
const appVersion = import.meta.env.VITE_APP_VERSION;
const appStability = import.meta.env.VITE_APP_STABILITY;
const convertStability = {
    alpha: "α",
    beta: "β",
    rc: "rc",
    stable: "",
};
const appStabilitySymbol = convertStability[appStability] || appStability;

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue"),
        ).then((module) => {
            const page = module.default;
            page.layout = page.layout || DefaultLayout;
            return module;
        }),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });
        app.config.globalProperties.$appName = appName; // Définir la propriété globale
        app.config.globalProperties.$appDescription = appDescription;
        app.config.globalProperties.$appVersion = appVersion;
        app.config.globalProperties.$appStability = appStability;
        app.config.globalProperties.$appStabilitySymbol = appStabilitySymbol;
        return app
            .use(plugin)
            .use(ZiggyVue)
            .component("Head", Head)
            .component("Link", Link)
            .mount(el);
    },
    progress: {
        // The color of the progress bar...
        color: "#155e75", // Cyan 800
        // Whether to include the default NProgress styles...
        includeCSS: true,
        // Whether the NProgress spinner will be shown...
        showSpinner: false,
    },
});

// Charger les icônes au démarrage de l'application
Icons.loadIcons()
    .then(() => {
        console.log("Icônes chargées avec succès");
    })
    .catch((error) => {
        console.error("Erreur lors du chargement des icônes:", error);
    });
