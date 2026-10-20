/**
 * Charge une page Inertia en JSON (sans naviguer) pour l’embarquer en modal.
 *
 * @param {string} url - URL relative ou absolue de la page
 * @param {string|null|undefined} inertiaVersion - `usePage().version`
 * @returns {Promise<{ component: string, props: Record<string, unknown>, url: string, version?: string }>}
 */
import axios from "axios";

export async function fetchInertiaPage(url, inertiaVersion = null) {
    if (!url) {
        throw new Error("URL Inertia manquante.");
    }
    const headers = {
        "X-Requested-With": "XMLHttpRequest",
        "X-Inertia": "true",
        Accept: "text/html, application/xhtml+xml",
    };
    if (inertiaVersion != null && inertiaVersion !== "") {
        headers["X-Inertia-Version"] = String(inertiaVersion);
    }

    const { data, status } = await axios.get(url, {
        headers,
        validateStatus: (s) => (s >= 200 && s < 300) || s === 409,
    });

    if (status === 409) {
        const err = new Error("Version Inertia obsolète — rechargement requis.");
        err.code = "INERTIA_VERSION_MISMATCH";
        throw err;
    }

    if (!data?.component) {
        throw new Error("Réponse Inertia invalide (composant manquant).");
    }

    return data;
}
