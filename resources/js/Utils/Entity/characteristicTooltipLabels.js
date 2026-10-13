import {
    getByCharacteristicKey,
    getByComputedKey,
    getByDbColumn,
} from "@/Composables/store/useCharacteristicsStore";

/**
 * Préfixe le chemin d’icône caractéristique si seul le nom de fichier est fourni.
 *
 * @param {unknown} icon
 * @returns {string}
 *
 * @example
 * normalizeCharacteristicIcon('saveLuck.webp') // 'icons/caracteristics/saveLuck.webp'
 */
export function normalizeCharacteristicIcon(icon) {
    if (icon == null || typeof icon !== "string") return "";
    const s = icon.trim();
    if (!s) return "";
    if (s.startsWith("fa-") || s.includes("/") || s.startsWith("http")) return s;
    return `icons/caracteristics/${s}`;
}

/**
 * Libellé joueur pour un identifiant de formule / placeholder.
 *
 * @param {string} id
 * @param {string} [group='creature']
 * @returns {string}
 */
export function humanizeCharacteristicPlaceholder(id, group = "creature") {
    if (!id || typeof id !== "string") return "";
    const key = id.trim();
    if (!key) return "";

    const def =
        getByComputedKey(group, key) ||
        getByCharacteristicKey(group, key) ||
        getByDbColumn(group, key) ||
        (key.endsWith("_creature")
            ? null
            : getByComputedKey(group, `${key}_creature`) || getByCharacteristicKey(group, `${key}_creature`));

    const short = def?.short_name || def?.shortName;
    const name = def?.name;
    if (short && String(short).trim()) return String(short).trim();
    if (name && String(name).trim()) return String(name).trim();

    const layered = key.match(/^(.+?)_(mastery_profile|object|consumable|spell)$/);
    if (layered) {
        const [, stem, layer] = layered;
        const stemDef =
            getByComputedKey(group, `${stem}_creature`) || getByCharacteristicKey(group, `${stem}_creature`);
        const stemLabel =
            String(stemDef?.short_name || stemDef?.shortName || stemDef?.name || "").trim()
            || stem.replace(/_/g, " ");
        const layerLabel = {
            mastery_profile: "maîtrise",
            object: "équip.",
            consumable: "conso.",
            spell: "sort",
        }[layer];
        return `${stemLabel} (${layerLabel})`;
    }

    // Colonnes maîtrise / bonus FR
    if (key.endsWith("_mastery")) {
        const stem = key.replace(/_mastery$/, "").replace(/_/g, " ");
        return `Maîtrise (${stem})`;
    }
    if (key.endsWith("_bonus")) {
        const stem = key.replace(/_bonus$/, "").replace(/_/g, " ");
        return `Bonus (${stem})`;
    }

    return key
        .replace(/_creature$/i, "")
        .replace(/_object$/i, " (équipement)")
        .replace(/_/g, " ");
}

/**
 * Remplace les identifiants techniques d’une formule d’affichage / résolution par des libellés.
 *
 * @param {unknown} text
 * @param {string} [group='creature']
 * @returns {string}
 */
export function humanizeCharacteristicFormulaText(text, group = "creature") {
    if (text == null || text === "") return "";
    let s = String(text);

    // [identifier]
    s = s.replace(/\[([a-zA-Z_][a-zA-Z0-9_]*)\]/g, (_, id) => humanizeCharacteristicPlaceholder(id, group));

    // Identifiants nus type modifier_chance_creature (évite min/max/floor)
    s = s.replace(/\b([a-z]+(?:_[a-z0-9]+)+)\b/g, (id) => {
        if (
            /^(min|max|floor|ceil|round|abs|sqrt|pow|log|exp|cos|sin|tan)$/i.test(id)
        ) {
            return id;
        }
        const label = humanizeCharacteristicPlaceholder(id, group);
        return label || id;
    });

    return s.replace(/\s+/g, " ").trim();
}

/**
 * Enrichit les placeholders runtime avec un libellé joueur.
 *
 * @param {Array<{id?: string, value?: unknown}>|null|undefined} placeholders
 * @param {string} [group='creature']
 * @returns {Array<{ id: string, label: string, value: unknown }>}
 */
export function mapPlaceholdersForPlayer(placeholders, group = "creature") {
    if (!Array.isArray(placeholders)) return [];
    return placeholders
        .filter((ph) => ph && ph.id)
        .map((ph) => ({
            id: String(ph.id),
            label: humanizeCharacteristicPlaceholder(String(ph.id), group),
            value: ph.value,
        }));
}
