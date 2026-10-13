#!/usr/bin/env python3
"""
Formules créature complètes, limites absolues et formules affichées (JSON de définitions).

- Ajoute à chaque formule créature les termes équipement / consommable / sort qui pointent
  l'intrinsèque via `intrinsic_key` (les termes buff_debuff sont éphémères au runtime).
- Sort les plafonds des modificateurs et du bonus de maîtrise des formules vers min/max
  et marque les limites absolues (`limit_absolute`).
- Réécrit `formula_display` en texte joueur abrégé, sans consommable ni sort.

Idempotent. Usage : python3 scripts/apply_characteristic_absolute_limits.py
"""
from __future__ import annotations

import json
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1] / "database/seeders/data/characteristic-definitions"

ABILITIES = {
    "agility": "Agi",
    "strength": "For",
    "intelligence": "Int",
    "chance": "Cha",
    "wisdom": "Sag",
    "vitality": "Vit",
}

SKILLS = [
    "acrobatics", "animal_handling", "arcana", "athletics", "deception", "history",
    "insight", "intimidation", "investigation", "medicine", "nature", "perception",
    "performance", "persuasion", "religion", "sleight_of_hand", "stealth", "survival",
]
SAVES = [f"save_{a}" for a in ABILITIES]

# Limites absolues : (min, max) PJ/PNJ, (min, max) monstre.
SKILL_LIMITS = (("-15", "15"), ("-20", "20"))
MOD_LIMITS = (("-2", "min(floor([level_creature]/4)+2, 7)"), ("-2", "10"))

DISPLAY = {
    "action_points_creature": "base + équip.",
    "movement_points_creature": "base + équip.",
    "range_creature": "base + équip.",
    "dodge_creature": "base + équip.",
    "tackle_creature": "base + équip.",
    "heal_bonus_creature": "base + équip.",
    "hit_bonus_creature": "base + équip.",
    "critical_hit_creature": "équip.",
    "armor_class_creature": "10 + mod. Vit + équip.",
    "initiative_creature": "mod. Int + équip.",
    "dodge_action_points_creature": "8 + mod. Sag + équip.",
    "dodge_movement_points_creature": "8 + mod. Sag + équip.",
    "life_dice_creature": "⌊niv. / 2⌋",
    "life_points_creature": "dé de vie + mod. Vit × niv. + (niv. − 1) × ½ dé de vie + équip.",
    "mastery_bonus_creature": "1 + ⌊niv. / 4⌋ (max 6)",
    "wakfu_reserve_creature": "bonus maîtrise + recharge équip. (max 3)",
    "summoning_creature": "base + équip. (max bonus maîtrise)",
    "fixed_damage_multiple_creature": "base + équip.",
}
for el in ("air", "earth", "fire", "neutral", "water"):
    DISPLAY[f"fixed_damage_{el}_creature"] = "base + équip."
    DISPLAY[f"fixed_resistance_{el}_creature"] = "base + équip."
    DISPLAY[f"resistance_{el}_creature"] = "base + palier équip. × 50 %"
for ab in ABILITIES:
    DISPLAY[f"{ab}_creature"] = "base + équip."


def load_all() -> dict[str, tuple[Path, dict]]:
    out = {}
    for path in sorted(ROOT.glob("*/*-definition.json")):
        data = json.loads(path.read_text(encoding="utf-8"))
        out[data["characteristic"]["key"]] = (path, data)
    return out


def save(path: Path, data: dict) -> None:
    path.write_text(json.dumps(data, ensure_ascii=False, indent=4) + "\n", encoding="utf-8")


def is_expression(formula) -> bool:
    return isinstance(formula, str) and formula.strip() != "" and not formula.strip().startswith("{")


def ordered_links(links: list[str]) -> list[str]:
    rank = {"_object": 0, "_consumable": 1, "_spell": 2}
    return sorted(links, key=lambda k: next((r for s, r in rank.items() if k.endswith(s)), 9))


def ensure_overlay(entities: dict, entity: str) -> dict:
    if entity not in entities:
        entities[entity] = {k: None for k in entities["*"].keys()}
    return entities[entity]


def main() -> None:
    defs = load_all()
    links: dict[str, list[str]] = {}
    for key, (_, data) in defs.items():
        target = data["characteristic"].get("intrinsic_key")
        group = data["characteristic"].get("group")
        if target and group in ("object", "consumable", "spell"):
            links.setdefault(target, []).append(key)

    changed = 0
    for key, (path, data) in defs.items():
        char = data["characteristic"]
        entities = data["entities"]
        before = json.dumps(data, sort_keys=True)

        if char.get("group") == "object":
            for row in entities.values():
                if row.get("formula_display") in ("level_object", "level"):
                    row["formula_display"] = None

        if char.get("group") != "creature":
            if json.dumps(data, sort_keys=True) != before:
                save(path, data)
                changed += 1
            continue

        stem = key[: -len("_creature")]

        # Plafonds hors formules : modificateurs et bonus de maîtrise.
        if stem.startswith("modifier_"):
            ability = stem[len("modifier_"):]
            entities["*"]["formula"] = f"floor(([{ability}_creature]-10)/2)"
            entities["*"]["min"], entities["*"]["max"] = MOD_LIMITS[0]
            entities["*"]["limit_absolute"] = True
            monster = ensure_overlay(entities, "monster")
            monster["formula"] = None
            monster["min"], monster["max"] = MOD_LIMITS[1]
            abbr = ABILITIES[ability]
            entities["*"]["formula_display"] = f"⌊(score {abbr} − 10) / 2⌋, entre −2 et ⌊niv. / 4⌋ + 2 (max +7)"
            monster["formula_display"] = f"⌊(score {abbr} − 10) / 2⌋, entre −2 et +10"
        if stem == "mastery_bonus":
            entities["*"]["formula"] = "1+floor([level_creature]/4)"
            entities["*"]["min"], entities["*"]["max"] = "0", "6"
            entities["*"]["limit_absolute"] = True

        # Compétences et sauvegardes : limites absolues.
        if stem in SKILLS or stem in SAVES:
            entities["*"]["min"], entities["*"]["max"] = SKILL_LIMITS[0]
            entities["*"]["limit_absolute"] = True
            monster = ensure_overlay(entities, "monster")
            monster["min"], monster["max"] = SKILL_LIMITS[1]

        # Termes équipement / consommable / sort manquants.
        linked = ordered_links(links.get(key, []))
        for entity, row in entities.items():
            formula = row.get("formula")
            if not is_expression(formula):
                continue
            for term in linked:
                if f"[{term}]" not in formula:
                    formula = f"{formula}+[{term}]"
            row["formula"] = formula

        # Formules affichées (texte joueur, sans consommable ni sort).
        if stem in SKILLS or stem in SAVES:
            ability = stem[len("save_"):] if stem in SAVES else None
            if ability is None:
                mod_key = entities["*"]["formula"].split("]")[0].strip("[")
                ability = mod_key[len("modifier_"):-len("_creature")]
            abbr = ABILITIES.get(ability, "?")
            mod = "mod. Cha (ou For)" if stem == "intimidation" else f"mod. {abbr}"
            entities["*"]["formula_display"] = f"{mod} + bonus maîtrise × maîtrise + équip."
        elif stem.endswith("_passive") and stem[: -len("_passive")] in SKILLS:
            base_key = stem[: -len("_passive")] + "_creature"
            short = defs[base_key][1]["characteristic"].get("short_name") or defs[base_key][1]["characteristic"]["name"]
            entities["*"]["formula_display"] = f"10 + {short} + équip."
        elif key in DISPLAY:
            entities["*"]["formula_display"] = DISPLAY[key]

        for entity, row in entities.items():
            if entity == "*":
                continue
            if row.get("formula") is None and not stem.startswith("modifier_"):
                row["formula_display"] = None
            elif key in DISPLAY and is_expression(row.get("formula")):
                row["formula_display"] = DISPLAY[key]

        if json.dumps(data, sort_keys=True) != before:
            save(path, data)
            changed += 1

    print(f"{changed} fichier(s) modifié(s)")


if __name__ == "__main__":
    main()
