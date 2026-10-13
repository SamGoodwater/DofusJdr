import { describe, it, expect } from "vitest";
import {
    humanizeCharacteristicFormulaText,
    humanizeCharacteristicPlaceholder,
    normalizeCharacteristicIcon,
} from "@/Utils/Entity/characteristicTooltipLabels";

describe("characteristicTooltipLabels", () => {
    it("préfixe les icônes fichier seul", () => {
        expect(normalizeCharacteristicIcon("saveLuck.webp")).toBe(
            "icons/caracteristics/saveLuck.webp",
        );
        expect(normalizeCharacteristicIcon("icons/caracteristics/x.webp")).toBe(
            "icons/caracteristics/x.webp",
        );
        expect(normalizeCharacteristicIcon("fa-solid fa-star")).toBe("fa-solid fa-star");
    });

    it("humanise les formules sans crochets", () => {
        const out = humanizeCharacteristicFormulaText(
            "modifier_chance_creature + mastery_bonus_creature × save_chance_mastery + save_chance_bonus",
        );
        expect(out.toLowerCase()).not.toContain("modifier_chance_creature");
        expect(out).toMatch(/[×+]/);
    });

    it("humanise les placeholders [id]", () => {
        const out = humanizeCharacteristicFormulaText(
            "[modifier_chance_creature]+[mastery_bonus_creature]*[save_chance_mastery]",
        );
        expect(out).not.toContain("[modifier_chance_creature]");
    });

    it("fallback libellé pour *_mastery", () => {
        expect(humanizeCharacteristicPlaceholder("save_chance_mastery")).toMatch(/Maîtrise/i);
    });

    it("libellés courts pour les couches maîtrise / équipement / conso / sort", () => {
        expect(humanizeCharacteristicPlaceholder("acrobatics_mastery_profile")).toBe("acrobatics (maîtrise)");
        expect(humanizeCharacteristicPlaceholder("initiative_object")).toBe("initiative (équip.)");
        expect(humanizeCharacteristicPlaceholder("initiative_consumable")).toBe("initiative (conso.)");
        expect(humanizeCharacteristicPlaceholder("initiative_spell")).toBe("initiative (sort)");
    });
});
