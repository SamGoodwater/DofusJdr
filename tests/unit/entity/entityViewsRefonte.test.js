import { describe, it, expect, beforeEach } from "vitest";
import {
    ENTITY_ACTION_CONTEXT_PRESETS,
    ENTITY_ACTIONS_COMMON,
    MINIMAL_EXPANDED_ACTION_KEYS,
} from "@/Entities/entity-actions-config";
import {
    isEntityPinned,
    listPinnedWindows,
    toggleEntityPin,
    updatePinnedWindowPosition,
} from "@/Composables/entity/usePinnedEntityIds";
import { buildCreatureCharacteristicGroups } from "@/Utils/Entity/buildCreatureCharacteristicGroups";
import {
    buildCreatureCompetenceGroupsByPrimary,
    formatCreatureSkillDisplay,
    resolveCreatureSkillTotal,
} from "@/Utils/Entity/buildCreatureCompetenceGroups";
import {
    CREATURE_CHARACTERISTIC_SUMMARY_KEYS,
    buildCreatureComposableEditSections,
} from "@/Utils/Entity/creatureCharacteristicGroups.manifest";

describe("ENTITY_ACTION_CONTEXT_PRESETS", () => {
    it("ordonne le minimal comme convenu", () => {
        expect(ENTITY_ACTION_CONTEXT_PRESETS.minimalLine).toEqual([
            "state",
            "pin",
            "quick-view",
            "view-dofusdb",
            "refresh",
            "favorite",
            "copy-link",
            "edit",
        ]);
        expect(MINIMAL_EXPANDED_ACTION_KEYS).toEqual(ENTITY_ACTION_CONTEXT_PRESETS.minimalLine);
    });

    it("n’inclut pas quick-edit / quick-view en modal", () => {
        expect(ENTITY_ACTION_CONTEXT_PRESETS.modalDetail).not.toContain("quick-view");
        expect(ENTITY_ACTION_CONTEXT_PRESETS.modalDetail).not.toContain("quick-edit");
        expect(ENTITY_ACTION_CONTEXT_PRESETS.modalDetail).toContain("refresh");
        expect(ENTITY_ACTION_CONTEXT_PRESETS.modalDetail).toContain("delete");
    });

    it("n’inclut pas view (agrandir) en page", () => {
        expect(ENTITY_ACTION_CONTEXT_PRESETS.pageDetail).not.toContain("view");
        expect(ENTITY_ACTION_CONTEXT_PRESETS.pageDetail).toEqual([
            "state",
            "favorite",
            "copy-link",
            "view-dofusdb",
            "edit",
            "refresh",
            "delete",
        ]);
    });

    it("masque view (page) en Minimal / Line ; Agrandir reste en modal", () => {
        expect(ENTITY_ACTIONS_COMMON.view.visibleIf({ inMinimal: true })).toBe(false);
        expect(ENTITY_ACTIONS_COMMON.view.visibleIf({ viewMode: "minimal" })).toBe(false);
        expect(ENTITY_ACTIONS_COMMON.view.visibleIf({ inLine: true })).toBe(false);
        expect(ENTITY_ACTIONS_COMMON.view.visibleIf({ inModal: true })).toBe(true);
        expect(ENTITY_ACTIONS_COMMON["quick-view"].visibleIf({ inMinimal: true })).toBe(true);
        expect(ENTITY_ACTIONS_COMMON["quick-view"].visibleIf({ inModal: true })).toBe(false);
        expect(ENTITY_ACTIONS_COMMON.edit.visibleIf({ inLine: true })).toBe(true);
        expect(ENTITY_ACTIONS_COMMON.edit.visibleIf({ viewMode: "line" })).toBe(true);
        expect(ENTITY_ACTION_CONTEXT_PRESETS.tableDropdown).toContain("edit");
        expect(ENTITY_ACTION_CONTEXT_PRESETS.tableDropdown).not.toContain("quick-edit");
        expect(ENTITY_ACTION_CONTEXT_PRESETS.minimalLine).not.toContain("quick-edit");
    });
});

describe("usePinnedEntityIds fenêtres", () => {
    beforeEach(() => {
        localStorage.clear();
    });

    it("crée une fenêtre avec payload et position", () => {
        const pinned = toggleEntityPin("monsters", 7, {
            entity: { id: 7, name: "Bouftou", creature: { pa: 6 } },
        });
        expect(pinned).toBe(true);
        expect(isEntityPinned("monsters", 7)).toBe(true);
        const wins = listPinnedWindows();
        expect(wins).toHaveLength(1);
        expect(wins[0].entity?.name).toBe("Bouftou");
        expect(wins[0].x).toBeTypeOf("number");
    });

    it("met à jour la position et désépingle", () => {
        toggleEntityPin("spells", 3, { entity: { id: 3, name: "Sort" } });
        updatePinnedWindowPosition("spells", 3, 120, 200);
        expect(listPinnedWindows()[0].x).toBe(120);
        expect(listPinnedWindows()[0].y).toBe(200);
        expect(toggleEntityPin("spells", 3)).toBe(false);
        expect(listPinnedWindows()).toHaveLength(0);
    });
});

describe("buildCreatureCharacteristicGroups", () => {
    const creature = {
        level: 8,
        pa: 6,
        pm: 3,
        po: 1,
        life: 40,
        ini: 12,
        ca: null,
        strong: 14,
        intel: 10,
        agi: 12,
        chance: 10,
        vitality: 12,
        sagesse: 11,
        res_fixe_terre: 3,
        res_terre: 50,
        res_feu: 0,
        res_fixe_feu: 1,
        athletisme_mastery: 1,
    };

    const summaryKeys = [...CREATURE_CHARACTERISTIC_SUMMARY_KEYS];
    const byDbColumn = Object.fromEntries(
        [
            ...summaryKeys,
            "strong",
            "intel",
            "agi",
            "chance",
            "vitality",
            "sagesse",
            "dodge_pa",
            "dodge_pm",
            "fuite",
            "tacle",
            "critical_hit",
            "heal_bonus",
            "res_fixe_terre",
            "res_terre",
            "res_fixe_feu",
            "res_feu",
            "do_fixe_multiple",
            "touch",
        ].map((k) => [
            k,
            {
                key: {
                    ca: "armor_class_creature",
                    ini: "initiative_creature",
                    do_fixe_multiple: "fixed_damage_multiple_creature",
                }[k] || `${k}_creature`,
                name: k,
                short_name: k === "do_fixe_multiple" ? "DO mult." : k,
                db_column: k,
                hide_when_empty: false,
                type: "int",
            },
        ]),
    );
    const runtime = {
        levels: [
            {
                characteristics: {
                    modifier_strength_creature: { total: 2 },
                    modifier_intelligence_creature: { total: 0 },
                    modifier_agility_creature: { total: 1 },
                    modifier_chance_creature: { total: 0 },
                    modifier_vitality_creature: { total: 1 },
                    modifier_wisdom_creature: { total: 0 },
                    save_strength_creature: { total: 2 },
                    save_intelligence_creature: { total: 0 },
                    save_agility_creature: { total: 1 },
                    save_chance_creature: { total: 0 },
                    save_vitality_creature: { total: 1 },
                    save_wisdom_creature: { total: 0 },
                    armor_class_creature: { total: 11 },
                    wakfu_reserve_creature: { total: 3 },
                },
            },
        ],
    };

    it("mode summary = mods + combat (dont CA calculée)", () => {
        const groups = buildCreatureCharacteristicGroups(creature, {
            mode: "summary",
            byDbColumn,
            byComputedKey: {
                modifier_strength_creature: { key: "modifier_strength_creature", short_name: "For" },
                modifier_intelligence_creature: { key: "modifier_intelligence_creature", short_name: "Int" },
                modifier_agility_creature: { key: "modifier_agility_creature", short_name: "Agi" },
                modifier_chance_creature: { key: "modifier_chance_creature", short_name: "Cha" },
                modifier_vitality_creature: { key: "modifier_vitality_creature", short_name: "Vit" },
                modifier_wisdom_creature: { key: "modifier_wisdom_creature", short_name: "Sag" },
            },
            runtime,
        });
        expect(groups.length).toBeGreaterThanOrEqual(2);
        expect(groups[0].kind).toBe("modifiers");
        expect(groups[0].characteristics).toHaveLength(6);
        const combat = groups.find((g) => g.kind === "combatSummary");
        expect(combat).toBeTruthy();
        const keys = combat.characteristics.map(
            (c) => c.def.db_column || c.def.key.replace(/_creature$/, ""),
        );
        expect(keys).toEqual(expect.arrayContaining(["pa", "pm", "life", "ca"]));
        const ca = combat.characteristics.find(
            (c) => (c.def.db_column || c.def.key.replace(/_creature$/, "")) === "ca",
        );
        expect(ca?.value).toBeTruthy();
    });

    it("mode full expose Combat avec réserve de Wakfu calculée", () => {
        const groups = buildCreatureCharacteristicGroups(creature, {
            mode: "full",
            byDbColumn,
            byComputedKey: {
                wakfu_reserve_creature: {
                    key: "wakfu_reserve_creature",
                    name: "Réserve de Wakfu",
                    short_name: "Réserve Wakfu",
                },
            },
            runtime,
        });
        const combat = groups.find((g) => g.title === "Combat");
        expect(combat).toBeTruthy();
        const wakfu = combat.characteristics.find((c) => c.def?.key === "wakfu_reserve_creature");
        expect(wakfu).toBeTruthy();
        expect(wakfu.value).toBe("3");
    });

    it("mode full expose Combat puis Caractéristiques (abilityStack)", () => {
        const groups = buildCreatureCharacteristicGroups(creature, { mode: "full", byDbColumn, runtime });
        expect(groups[0].title).toBe("Combat");
        expect(groups[1].title).toBe("Caractéristiques");
        expect(groups[1].kind).toBe("abilityStack");
        expect(groups[1].characteristics).toHaveLength(6);
        expect(groups[1].characteristics[0].type).toBe("abilityColumn");
        expect(groups[1].characteristics[0].modifier.value).toMatch(/^[+-]?\d+/);
    });

    it("résistances : masque 0 % et libelle les paliers", () => {
        const groups = buildCreatureCharacteristicGroups(creature, { mode: "full", byDbColumn });
        const res = groups.find((g) => g.title === "Résistances");
        expect(res).toBeTruthy();
        const values = res.characteristics.map((c) => c.value);
        expect(values.some((v) => String(v).includes("(R)"))).toBe(true);
        expect(values.some((v) => String(v).includes("(0%") || String(v) === "0%")).toBe(false);
        expect(values).toContain("1");
    });

    it("Dommages : expose DO mult. même à 0", () => {
        const groups = buildCreatureCharacteristicGroups(creature, {
            mode: "full",
            byDbColumn,
            byComputedKey: {
                fixed_damage_multiple_creature: {
                    key: "fixed_damage_multiple_creature",
                    short_name: "DO mult.",
                    hide_when_empty: false,
                    type: "int",
                },
            },
        });
        const dmg = groups.find((g) => g.title === "Dommages");
        expect(dmg).toBeTruthy();
        expect(dmg.spread).toBe(true);
        const keys = dmg.characteristics.map((c) => c.def.db_column || c.def.key);
        expect(keys).not.toContain("do_sagesse");
        expect(keys).not.toContain("do_vitalite");
        const multi = dmg.characteristics.find(
            (c) =>
                c.def.db_column === "do_fixe_multiple" ||
                c.def.key === "fixed_damage_multiple_creature",
        );
        expect(multi).toBeTruthy();
        expect(String(multi.value)).toBe("0");
    });

    it("Dommages : lit DO mult. depuis le runtime", () => {
        const groups = buildCreatureCharacteristicGroups(
            { ...creature, do_fixe_multiple: null },
            {
                mode: "full",
                byDbColumn,
                runtime: {
                    levels: [
                        {
                            characteristics: {
                                fixed_damage_multiple_creature: { total: 3 },
                            },
                        },
                    ],
                },
            },
        );
        const dmg = groups.find((g) => g.title === "Dommages");
        const multi = dmg.characteristics.find(
            (c) => c.def.key === "fixed_damage_multiple_creature",
        );
        expect(String(multi?.value)).toBe("3");
    });
});

describe("buildCreatureCompetenceGroupsByPrimary", () => {
    it("calcule mod + maîtrise×bonus + bonus BDD avec tags M/E", () => {
        const creature = {
            level: 8, // mastery bonus = 3
            strong: 14, // mod = +2
            athletisme_mastery: 2,
            athletisme_bonus: 1,
            acrobatie_mastery: 1,
            acrobatie_bonus: 0,
            agi: 10,
        };
        const runtime = {
            levels: [
                {
                    characteristics: {
                        athletics_creature: { total: 9 },
                        acrobatics_creature: { total: 3 },
                    },
                },
            ],
        };
        const athletics = resolveCreatureSkillTotal(creature, "athletisme_mastery", runtime);
        // mod 2 + 3*2 + 1 = 9, expertise
        expect(athletics).toEqual({ total: 9, tier: 2, tag: "E" });
        expect(formatCreatureSkillDisplay(9, "E")).toBe("+9 (E)");

        const acrobatics = resolveCreatureSkillTotal(creature, "acrobatie_mastery", runtime);
        // mod 0 + 3*1 + 0 = 3, maîtrise
        expect(acrobatics.tag).toBe("M");
        expect(formatCreatureSkillDisplay(acrobatics.total, "M")).toBe("+3 (M)");

        const groups = buildCreatureCompetenceGroupsByPrimary(creature, { includeZero: false, runtime });
        const force = groups.find((g) => g.title === "Force");
        expect(force?.characteristics[0]?.lockSkillDisplay).toBe(true);
        expect(force?.characteristics[0]?.skillName).toBe("Athlétisme");
        expect(force?.characteristics[0]?.skillTag).toBe("E");
        expect(force?.characteristics[0]?.value).toContain("Athlétisme");
        expect(force?.characteristics[0]?.value).toContain("+9 (E)");
    });

    it("Intimidation : regroupe sous Chance si intimidation_ability = chance", () => {
        const creature = {
            level: 8,
            strong: 10,
            chance: 16,
            intimidation_mastery: 1,
            intimidation_bonus: 0,
            intimidation_ability: "chance",
        };
        const groups = buildCreatureCompetenceGroupsByPrimary(creature, { includeZero: false });
        expect(groups.find((g) => g.title === "Force")).toBeUndefined();
        const luck = groups.find((g) => g.title === "Chance");
        expect(luck?.characteristics[0]?.skillName).toBe("Intimidation");
    });
});

describe("buildCreatureComposableEditSections", () => {
    it("inclut les colonnes combat éditables (ex. ca, life)", () => {
        const sections = buildCreatureComposableEditSections();
        const combat = sections.find((s) => s.id === "combat");
        expect(combat?.dbColumns).toContain("ca");
        expect(combat?.dbColumns).toContain("life");
    });
});
