/**
 * Résout un composant de page Inertia par son nom (`Pages/entity/spell/Edit`).
 * Sans layout Main (embarqué dans EntitySurfaceHost).
 *
 * @param {string} componentName
 * @returns {Promise<object>}
 */
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";

const pageModules = import.meta.glob("../../Pages/**/*.vue");

export async function loadInertiaPageComponent(componentName) {
    const name = String(componentName || "").replace(/\.vue$/i, "");
    const mod = await resolvePageComponent(`../../Pages/${name}.vue`, pageModules);
    const page = mod?.default ?? mod;
    if (page && typeof page === "object") {
        page.layout = undefined;
    }
    return mod;
}
