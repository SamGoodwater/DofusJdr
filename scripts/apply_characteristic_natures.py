#!/usr/bin/env python3
"""Apply nature / intrinsic_key and related characteristic-definition updates."""

from __future__ import annotations

import copy
import json
import re
import sys
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
DEF_ROOT = ROOT / "database/seeders/data/characteristic-definitions"

SKILLS = [
    "acrobatics",
    "animal_handling",
    "arcana",
    "athletics",
    "deception",
    "history",
    "insight",
    "intimidation",
    "investigation",
    "medicine",
    "nature",
    "perception",
    "performance",
    "persuasion",
    "religion",
    "sleight_of_hand",
    "stealth",
    "survival",
]

SAVES = [
    "save_strength",
    "save_intelligence",
    "save_agility",
    "save_chance",
    "save_vitality",
    "save_wisdom",
]

SKILL_MODIFIER = {
    "athletics": "modifier_strength_creature",
    "acrobatics": "modifier_agility_creature",
    "sleight_of_hand": "modifier_agility_creature",
    "stealth": "modifier_agility_creature",
    "arcana": "modifier_intelligence_creature",
    "history": "modifier_intelligence_creature",
    "investigation": "modifier_intelligence_creature",
    "nature": "modifier_intelligence_creature",
    "religion": "modifier_intelligence_creature",
    "animal_handling": "modifier_wisdom_creature",
    "insight": "modifier_wisdom_creature",
    "medicine": "modifier_wisdom_creature",
    "perception": "modifier_wisdom_creature",
    "survival": "modifier_wisdom_creature",
    "deception": "modifier_chance_creature",
    "intimidation": "modifier_chance_creature",
    "performance": "modifier_chance_creature",
    "persuasion": "modifier_chance_creature",
}

SAVE_MODIFIER = {
    "save_strength": "modifier_strength_creature",
    "save_intelligence": "modifier_intelligence_creature",
    "save_agility": "modifier_agility_creature",
    "save_chance": "modifier_chance_creature",
    "save_vitality": "modifier_vitality_creature",
    "save_wisdom": "modifier_wisdom_creature",
}

OBJECT_DESCRIPTIF = {"name", "description", "price", "weight", "rarity"}
OBJECT_INTRINSIC = {"level"}
OBJECT_DELETE = {"life_points_restore", "shield_points", "temporary_life_points"}

OBJECT_INTRINSIC_KEY_OVERRIDE = {
    "wakfu_recharge": "wakfu_reserve_creature",
    "life_points_max": "life_points_creature",
    "resistance_percent_tier_neutral": "resistance_neutral_creature",
    "resistance_percent_tier_earth": "resistance_earth_creature",
    "resistance_percent_tier_fire": "resistance_fire_creature",
    "resistance_percent_tier_water": "resistance_water_creature",
    "resistance_percent_tier_air": "resistance_air_creature",
}

SPELL_DESCRIPTIF = {
    "area",
    "casting_time",
    "duration",
    "spell_type",
    "target_type",
    "resolution_mode",
    "save_success_note",
    "save_dc_formula",
    "attack_characteristic_key",
    "save_characteristic_key",
}

SPELL_INTRINSIC = {
    "is_passive",
    "level",
    "category",
    "element",
    "auto_success_if_willing_target",
    "allows_reaction",
    "action_points",
    "spell_range_min",
    "spell_range_max",
    "range_editable",
    "sight_line",
    "cast_in_line",
    "cast_in_diagonal",
    "cast_per_turn",
    "cast_per_target",
    "number_between_two_cast",
    "max_stack",
    "global_cooldown",
    "ritual_available",
    "is_magic",
}

SPELL_ACTION_VALUE = {
    "dommages",
    "soin",
    "vol_vie",
    "bouclier",
    "pv_temporaires",
    "movement_distance",
    "jump_distance",
    "teleport_distance",
    "pull_distance",
    "push_distance",
}

SPELL_INTRINSIC_KEY_OVERRIDE = {
    "action_points_variation": "action_points_creature",
    "strong": "strength_creature",
    "intel": "intelligence_creature",
    "agi": "agility_creature",
    "sagesse": "wisdom_creature",
    "critical": "critical_hit_creature",
    "fixed_resistance_eau": "fixed_resistance_water_creature",
    "fixed_resistance_feu": "fixed_resistance_fire_creature",
    "fixed_resistance_terre": "fixed_resistance_earth_creature",
    "fixed_resistance_neutre": "fixed_resistance_neutral_creature",
    "res_air": "resistance_air_creature",
    "res_eau": "resistance_water_creature",
    "res_feu": "resistance_fire_creature",
    "res_neutre": "resistance_neutral_creature",
    "res_terre": "resistance_earth_creature",
    "do_fixe_multiple": "fixed_damage_multiple_creature",
    "hostility": "hostility_creature",
}

CREATURE_BASE_OBJECT_STEMS = {
    "action_points",
    "movement_points",
    "range",
    "summoning",
    "hit_bonus",
    "dodge",
    "tackle",
    "heal_bonus",
    "strength",
    "intelligence",
    "agility",
    "chance",
    "vitality",
    "wisdom",
    "fixed_damage_neutral",
    "fixed_damage_earth",
    "fixed_damage_fire",
    "fixed_damage_water",
    "fixed_damage_air",
    "fixed_damage_multiple",
    "fixed_resistance_neutral",
    "fixed_resistance_earth",
    "fixed_resistance_fire",
    "fixed_resistance_water",
    "fixed_resistance_air",
}

RESISTANCE_TIER_OBJECT = {
    "resistance_neutral": "resistance_percent_tier_neutral_object",
    "resistance_earth": "resistance_percent_tier_earth_object",
    "resistance_fire": "resistance_percent_tier_fire_object",
    "resistance_water": "resistance_percent_tier_water_object",
    "resistance_air": "resistance_percent_tier_air_object",
}

MASTERY_DELETE_GLOB = "*_mastery-creature-definition.json"

counts = {"updated": 0, "created": 0, "deleted": 0, "unchanged": 0}


def load_json(path: Path) -> dict:
    with path.open(encoding="utf-8") as f:
        return json.load(f)


def write_json(path: Path, data: dict) -> None:
    path.parent.mkdir(parents=True, exist_ok=True)
    text = json.dumps(data, indent=4, ensure_ascii=False) + "\n"
    with path.open("w", encoding="utf-8") as f:
        f.write(text)


def stem_from_path(path: Path, group: str) -> str:
    suffix = f"-{group}-definition.json"
    name = path.name
    if not name.endswith(suffix):
        raise ValueError(name)
    return name[: -len(suffix)]


def set_nature(char: dict, nature: str, intrinsic_key: str | None = ...) -> None:
    char["nature"] = nature
    if intrinsic_key is ...:
        return
    if intrinsic_key is None:
        char.pop("intrinsic_key", None)
        char["intrinsic_key"] = None
    else:
        char["intrinsic_key"] = intrinsic_key


def write_if_changed(path: Path, data: dict, original_text: str | None) -> None:
    new_text = json.dumps(data, indent=4, ensure_ascii=False) + "\n"
    if original_text == new_text:
        counts["unchanged"] += 1
        return
    write_json(path, data)
    if original_text is None:
        counts["created"] += 1
    else:
        counts["updated"] += 1


def critical_norms_level_row() -> list[int]:
    row = []
    for level in range(1, 21):
        if level <= 2:
            row.append(1)
        elif level <= 6:
            row.append(2)
        elif level <= 11:
            row.append(3)
        elif level <= 16:
            row.append(4)
        else:
            row.append(5)
    return row


def unsigned_conversion(formula: str | None) -> str | None:
    if not formula or not isinstance(formula, str):
        return formula
    if "max(abs([d])" in formula or "/ max(abs" in formula:
        return "round(2 * pow(max(1, [d]), 0.5))"
    return formula


def process_creature_file(path: Path, data: dict, original_text: str) -> None:
    char = data["characteristic"]
    stem = stem_from_path(path, "creature")
    set_nature(char, "intrinsic")
    char.pop("intrinsic_key", None)

    entities = data.setdefault("entities", {})
    star = entities.get("*")
    if isinstance(star, dict):
        if stem in CREATURE_BASE_OBJECT_STEMS:
            star["formula"] = f"[base]+[{stem}_object]"
            star["formula_display"] = star["formula"]
        elif stem == "initiative":
            star["formula"] = "[modifier_intelligence_creature]+[initiative_object]"
            star["formula_display"] = star["formula"]
        elif stem == "armor_class":
            star["formula"] = "10+[modifier_vitality_creature]+[armor_class_object]"
            star["formula_display"] = star["formula"]
        elif stem == "dodge_action_points":
            star["formula"] = "8+[modifier_wisdom_creature]+[dodge_action_points_object]"
            star["formula_display"] = star["formula"]
        elif stem == "dodge_movement_points":
            star["formula"] = "8+[modifier_wisdom_creature]+[dodge_movement_points_object]"
            star["formula_display"] = star["formula"]
        elif stem == "critical_hit":
            star["formula"] = "[critical_hit_object]"
            star["formula_display"] = star["formula"]
            star["min"] = "-3"
            star["max"] = "3"
        elif stem == "wakfu_reserve":
            star["formula"] = "[mastery_bonus_creature]+min([wakfu_recharge_object],3)"
            star["formula_display"] = star["formula"]
        elif stem == "life_points":
            formula = star.get("formula") or ""
            if "life_points_max_object" not in formula:
                star["formula"] = formula + "+[life_points_max_object]"
            star["formula_display"] = (
                "[hit_dice_creature] + [modifier_vitality_creature]×[level_creature] + "
                "([level_creature]-1)×(round([hit_dice_creature]/2)) + [life_points_max_object]"
            )
        elif stem in SKILL_MODIFIER:
            mod = SKILL_MODIFIER[stem]
            star["formula"] = (
                f"[{mod}]+[mastery_bonus_creature]*[{stem}_mastery_profile]+[{stem}_object]"
            )
            star["formula_display"] = star["formula"]
        elif stem.endswith("_passive"):
            skill = stem[: -len("_passive")]
            star["formula"] = f"10+[{skill}_creature]+[{skill}_passive_object]"
            star["formula_display"] = star["formula"]
        elif stem in SAVE_MODIFIER:
            mod = SAVE_MODIFIER[stem]
            star["formula"] = (
                f"[{mod}]+[mastery_bonus_creature]*[{stem}_mastery_profile]+[{stem}_object]"
            )
            star["formula_display"] = star["formula"]
        elif stem in RESISTANCE_TIER_OBJECT:
            obj = RESISTANCE_TIER_OBJECT[stem]
            star["formula"] = f"[base]+[{obj}]*50"
            star["formula_display"] = star["formula"]

    if stem == "life_dice":
        if "*" not in entities:
            entities["*"] = copy.deepcopy(entities.get("class", {})) if isinstance(entities.get("class"), dict) else {}
        life_star = entities["*"]
        life_star["formula"] = "floor([level_creature]/2)"
        life_star["formula_display"] = life_star["formula"]
        life_star["min"] = "1"
        life_star["max"] = "10"

    write_if_changed(path, data, original_text)


def object_intrinsic_key(stem: str) -> str:
    if stem in OBJECT_INTRINSIC_KEY_OVERRIDE:
        return OBJECT_INTRINSIC_KEY_OVERRIDE[stem]
    if stem.endswith("_passive"):
        base = stem[: -len("_passive")]
        return f"{base}_passive_creature"
    return f"{stem}_creature"


def process_object_file(path: Path, data: dict, original_text: str) -> None:
    stem = stem_from_path(path, "object")
    if stem in OBJECT_DELETE:
        path.unlink()
        counts["deleted"] += 1
        return

    char = data["characteristic"]
    if stem in OBJECT_DESCRIPTIF:
        set_nature(char, "descriptif", None)
    elif stem in OBJECT_INTRINSIC:
        set_nature(char, "intrinsic")
        char.pop("intrinsic_key", None)
    else:
        set_nature(char, "bonus_malus", object_intrinsic_key(stem))

    entities = data.get("entities", {})
    if stem == "weight" and "resource" in entities:
        del entities["resource"]

    write_if_changed(path, data, original_text)


def spell_intrinsic_key(stem: str, creature_keys: set[str]) -> str | None:
    if stem in SPELL_INTRINSIC_KEY_OVERRIDE:
        return SPELL_INTRINSIC_KEY_OVERRIDE[stem]
    candidate = f"{stem}_creature"
    if candidate in creature_keys:
        return candidate
    if stem.endswith("_passive"):
        base = stem[: -len("_passive")]
        cand = f"{base}_passive_creature"
        if cand in creature_keys:
            return cand
    return None


def classify_spell(stem: str) -> str:
    if stem in SPELL_DESCRIPTIF:
        return "descriptif"
    if stem in SPELL_INTRINSIC:
        return "intrinsic"
    if stem in SPELL_ACTION_VALUE:
        return "action_value"
    return "buff_debuff"


def process_spell_file(path: Path, data: dict, original_text: str, creature_keys: set[str]) -> None:
    stem = stem_from_path(path, "spell")
    if stem == "critical_damage_reduction":
        path.unlink()
        counts["deleted"] += 1
        return

    char = data["characteristic"]
    nature = classify_spell(stem)
    if nature == "descriptif":
        set_nature(char, "descriptif", None)
    elif nature == "intrinsic":
        set_nature(char, "intrinsic")
        char.pop("intrinsic_key", None)
    elif nature == "action_value":
        set_nature(char, "action_value")
        char.pop("intrinsic_key", None)
    else:
        ik = spell_intrinsic_key(stem, creature_keys)
        set_nature(char, "buff_debuff", ik)

    if stem == "dommages" and char.get("name") == "Dommages":
        char["name"] = "Dégâts"
    if stem == "spell_range_min" and char.get("name") == "Portée min":
        char["name"] = "Portée du sort (min)"
    if stem == "spell_range_max" and char.get("name") == "Portée max":
        char["name"] = "Portée du sort (max)"
    if stem == "range" and "Portée" in (char.get("name") or ""):
        char["name"] = "Portée (bonus de PO)"
    if stem in SKILLS and isinstance(char.get("name"), str):
        name = char["name"]
        for prefix in ("Bonus temporaire de ", "Bonus temporaire d'", "Bonus temporaire "):
            if name.startswith(prefix):
                name = name[len(prefix) :]
                break
        char["name"] = name

    star = data.get("entities", {}).get("*")
    if isinstance(star, dict):
        if stem == "critical":
            star["min"] = "-5"
            star["max"] = "5"
            row = critical_norms_level_row()
            star["norms_grid"] = {
                "very_weak": row,
                "weak": row,
                "neutral": row,
                "strong": row,
                "very_strong": row,
            }
            star["conversion_formula"] = (
                '{"-100":-5,"-14":-4,"-9":-3,"-4":-2,"0":0,"5":1,"10":2,"15":3,"20":4,"25":5,"characteristic":"d"}'
            )
            char["descriptions"] = (
                "Bonus ou malus temporaire appliqué au seuil du jet critique, de −5 à +5."
            )
        if stem == "initiative":
            star["min"] = "-20"
        if nature == "action_value":
            star["min"] = "0"
            if stem == "push_distance":
                star["max"] = "8"
            conv = star.get("conversion_formula")
            star["conversion_formula"] = unsigned_conversion(conv)
            if stem == "pull_distance":
                star["max"] = "8"
                star["min"] = "0"

    write_if_changed(path, data, original_text)


def create_push_distance_spell(pull_path: Path) -> None:
    out = DEF_ROOT / "spell" / "push_distance-spell-definition.json"
    if out.exists():
        return
    pull = load_json(pull_path)
    data = copy.deepcopy(pull)
    c = data["characteristic"]
    c["key"] = "push_distance_spell"
    c["name"] = "Distance de repoussement"
    c["short_name"] = "Repousse"
    c["helper"] = "Nombre de cases de repoussement."
    c["descriptions"] = (
        "Repoussement : déplacement forcé à l’opposé de l’origine. Valeurs basses, équivalentes à l’attirance. "
        "1 case = 1,5 m."
    )
    c["nature"] = "action_value"
    c.pop("intrinsic_key", None)
    star = data["entities"]["*"]
    star["min"] = "0"
    star["max"] = "8"
    star["conversion_formula"] = unsigned_conversion(star.get("conversion_formula"))
    write_json(out, data)
    counts["created"] += 1


def create_hostility_spell(hostility_creature_path: Path) -> None:
    out = DEF_ROOT / "spell" / "hostility-spell-definition.json"
    creature = load_json(hostility_creature_path)
    cc = creature["characteristic"]
    data = {
        "_schema_version": creature.get("_schema_version", "1"),
        "characteristic": {
            "key": "hostility_spell",
            "name": cc["name"],
            "short_name": cc.get("short_name"),
            "helper": "Hostilité modifiée temporairement par un effet de sort.",
            "descriptions": cc.get("descriptions"),
            "icon": cc.get("icon"),
            "icon_false": cc.get("icon_false"),
            "color": cc.get("color"),
            "value_overrides": copy.deepcopy(cc.get("value_overrides")),
            "hide_when_empty": cc.get("hide_when_empty", False),
            "hide_when_false": cc.get("hide_when_false", False),
            "unit": cc.get("unit"),
            "type": cc.get("type", "int"),
            "status": cc.get("status", "validee"),
            "sort_order": 210,
            "group": "spell",
            "linked_to_key": None,
            "nature": "buff_debuff",
            "intrinsic_key": "hostility_creature",
        },
        "entities": {
            "*": {
                "dofusdb_characteristic_id": None,
                "db_column": None,
                "min": "0",
                "max": "4",
                "formula": None,
                "formula_display": None,
                "default_value": "0",
                "conversion_formula": "[d]",
                "conversion_function": None,
                "conversion_dofus_sample": None,
                "conversion_krosmoz_sample": None,
                "conversion_sample_rows": None,
                "norms_grid": None,
                "norms_formula": None,
                "norms_conditions": None,
                "norms_description": None,
                "norms_help_section_id": None,
                "value_available": None,
            }
        },
        "relations": None,
    }
    original = out.read_text(encoding="utf-8") if out.exists() else None
    write_if_changed(out, data, original)


def load_mastery_creature_cache() -> dict[str, dict]:
    cache: dict[str, dict] = {}
    creature_dir = DEF_ROOT / "creature"
    for path in creature_dir.glob(MASTERY_DELETE_GLOB):
        stem = stem_from_path(path, "creature").replace("_mastery", "")
        cache[stem] = load_json(path)
    return cache


def create_profile_definitions(mastery_cache: dict[str, dict]) -> None:
    profile_dir = DEF_ROOT / "profile"
    profile_dir.mkdir(parents=True, exist_ok=True)

    def make_profile(skill_or_save: str, is_save: bool) -> None:
        column = f"{skill_or_save}_mastery"
        key = f"{column}_profile"
        out = profile_dir / f"{column}-profile-definition.json"
        mastery_stem = skill_or_save if is_save else skill_or_save
        mastery_file_stem = f"{mastery_stem}_mastery" if not is_save else skill_or_save
        source = mastery_cache.get(mastery_stem)
        skill_creature_path = DEF_ROOT / "creature" / f"{skill_or_save}-creature-definition.json"
        skill_src = load_json(skill_creature_path) if skill_creature_path.exists() else None
        icon = color = None
        value_overrides = None
        name = short_name = helper = descriptions = None
        sort_order = 300
        if source:
            sc = source["characteristic"]
            icon = sc.get("icon")
            color = sc.get("color")
            value_overrides = copy.deepcopy(sc.get("value_overrides"))
            name = sc.get("name", "").replace("(palier maîtrise)", "(profil)").replace("palier ", "")
            short_name = sc.get("short_name")
            helper = sc.get("helper")
            descriptions = sc.get("descriptions")
            sort_order = sc.get("sort_order", sort_order)
        elif skill_src:
            sc = skill_src["characteristic"]
            icon = sc.get("icon")
            color = sc.get("color")
        if not name:
            name = f"{skill_or_save.replace('_', ' ').title()} (maîtrise profil)"
        if is_save and value_overrides:
            value_overrides = [vo for vo in value_overrides if vo.get("value") in (0, 1)]
        max_val = "1" if is_save else "2"
        if not is_save and value_overrides is None:
            value_overrides = [
                {"value": 0, "subtitle": "Non formé — palier 0 : pas de bonus de maîtrise."},
                {"value": 1, "subtitle": "Maîtrise — palier 1."},
                {"value": 2, "subtitle": "Expertise — palier 2."},
            ]
        if is_save and value_overrides is None:
            value_overrides = [
                {"value": 0, "subtitle": "Pas de maîtrise de sauvegarde."},
                {"value": 1, "subtitle": "Maîtrise de sauvegarde."},
            ]
        data = {
            "_schema_version": "1",
            "characteristic": {
                "key": key,
                "name": name,
                "short_name": short_name,
                "helper": helper,
                "descriptions": descriptions,
                "icon": icon,
                "icon_false": None,
                "color": color,
                "value_overrides": value_overrides,
                "hide_when_empty": False,
                "hide_when_false": False,
                "unit": None,
                "type": "int",
                "status": "validee",
                "sort_order": sort_order,
                "group": "profile",
                "linked_to_key": None,
                "nature": "bonus_malus",
                "intrinsic_key": f"{skill_or_save}_creature",
            },
            "entities": {
                "*": {
                    "dofusdb_characteristic_id": None,
                    "db_column": column,
                    "min": "0",
                    "max": max_val,
                    "formula": None,
                    "formula_display": None,
                    "default_value": "0",
                    "conversion_formula": None,
                    "conversion_function": None,
                    "conversion_dofus_sample": None,
                    "conversion_krosmoz_sample": None,
                    "conversion_sample_rows": None,
                    "norms_grid": source["entities"]["*"].get("norms_grid") if source else None,
                    "norms_formula": None,
                    "norms_conditions": None,
                    "norms_description": source["entities"]["*"].get("norms_description") if source else None,
                    "norms_help_section_id": None,
                    "labels": None,
                    "validation": None,
                }
            },
            "relations": None,
        }
        original = out.read_text(encoding="utf-8") if out.exists() else None
        write_if_changed(out, data, original)

    for skill in SKILLS:
        make_profile(skill, False)
    for save in SAVES:
        make_profile(save, True)


def consumable_from_spell(spell_path: Path, data: dict) -> dict | None:
    stem = stem_from_path(spell_path, "spell")
    char = data["characteristic"]
    if char.get("nature") != "buff_debuff":
        return None
    ik = char.get("intrinsic_key")
    if not ik:
        return None
    out = copy.deepcopy(data)
    oc = out["characteristic"]
    oc["key"] = f"{stem}_consumable"
    oc["group"] = "consumable"
    oc["nature"] = "buff_debuff"
    oc["intrinsic_key"] = ik
    oc["name"] = (oc.get("name") or stem).replace("sort", "consommable")
    if oc.get("helper"):
        oc["helper"] = oc["helper"].replace("sort", "consommable")
    return out


def create_consumables(spell_dir: Path) -> None:
    consumable_dir = DEF_ROOT / "consumable"
    consumable_dir.mkdir(parents=True, exist_ok=True)

    for spell_path in sorted(spell_dir.glob("*-spell-definition.json")):
        data = load_json(spell_path)
        mirrored = consumable_from_spell(spell_path, data)
        if not mirrored:
            continue
        stem = stem_from_path(spell_path, "spell")
        out = consumable_dir / f"{stem}-consumable-definition.json"
        original = out.read_text(encoding="utf-8") if out.exists() else None
        write_if_changed(out, mirrored, original)

    migrations = [
        ("life_points_restore", "soin", "action_value"),
        ("shield_points", "bouclier", "action_value"),
        ("temporary_life_points", "pv_temporaires", "action_value"),
    ]
    for old_stem, new_stem, nature in migrations:
        old_path = DEF_ROOT / "object" / f"{old_stem}-object-definition.json"
        if not old_path.exists():
            spell_path = DEF_ROOT / "spell" / f"{new_stem}-spell-definition.json"
            if spell_path.exists():
                base = load_json(spell_path)
            else:
                continue
        else:
            base = load_json(old_path)
        out = consumable_dir / f"{new_stem}-consumable-definition.json"
        data = copy.deepcopy(base)
        c = data["characteristic"]
        c["key"] = f"{new_stem}_consumable"
        c["group"] = "consumable"
        c["nature"] = nature
        c.pop("intrinsic_key", None)
        star = data.get("entities", {}).get("*")
        if isinstance(star, dict):
            star["min"] = "0"
        original = out.read_text(encoding="utf-8") if out.exists() else None
        write_if_changed(out, data, original)

    for obj_stem, nature in [
        ("name", "descriptif"),
        ("description", "descriptif"),
        ("price", "descriptif"),
        ("rarity", "descriptif"),
        ("level", "intrinsic"),
    ]:
        obj_path = DEF_ROOT / "object" / f"{obj_stem}-object-definition.json"
        if not obj_path.exists():
            continue
        data = copy.deepcopy(load_json(obj_path))
        c = data["characteristic"]
        c["key"] = f"{obj_stem}_consumable"
        c["group"] = "consumable"
        c["nature"] = nature
        if nature == "descriptif":
            c["intrinsic_key"] = None
        else:
            c.pop("intrinsic_key", None)
        out = consumable_dir / f"{obj_stem}-consumable-definition.json"
        original = out.read_text(encoding="utf-8") if out.exists() else None
        write_if_changed(out, data, original)

    duration_spell = DEF_ROOT / "spell" / "duration-spell-definition.json"
    if duration_spell.exists():
        data = copy.deepcopy(load_json(duration_spell))
        c = data["characteristic"]
        c["key"] = "duration_consumable"
        c["group"] = "consumable"
        c["nature"] = "intrinsic"
        c.pop("intrinsic_key", None)
        out = consumable_dir / "duration-consumable-definition.json"
        original = out.read_text(encoding="utf-8") if out.exists() else None
        write_if_changed(out, data, original)

    crit_spell = DEF_ROOT / "spell" / "critical-spell-definition.json"
    if crit_spell.exists():
        data = copy.deepcopy(load_json(crit_spell))
        c = data["characteristic"]
        c["key"] = "critical_consumable"
        c["group"] = "consumable"
        c["nature"] = "buff_debuff"
        c["intrinsic_key"] = "critical_hit_creature"
        out = consumable_dir / "critical-consumable-definition.json"
        original = out.read_text(encoding="utf-8") if out.exists() else None
        write_if_changed(out, data, original)

    hostility_spell = DEF_ROOT / "spell" / "hostility-spell-definition.json"
    if hostility_spell.exists():
        data = copy.deepcopy(load_json(hostility_spell))
        c = data["characteristic"]
        c["key"] = "hostility_consumable"
        c["group"] = "consumable"
        c["nature"] = "buff_debuff"
        c["intrinsic_key"] = "hostility_creature"
        out = consumable_dir / "hostility-consumable-definition.json"
        original = out.read_text(encoding="utf-8") if out.exists() else None
        write_if_changed(out, data, original)


def delete_mastery_creature_files() -> None:
    for path in (DEF_ROOT / "creature").glob(MASTERY_DELETE_GLOB):
        path.unlink()
        counts["deleted"] += 1


def validate_all_json() -> list[str]:
    errors: list[str] = []
    for path in DEF_ROOT.rglob("*-definition.json"):
        try:
            load_json(path)
        except json.JSONDecodeError as exc:
            errors.append(f"{path}: {exc}")
    return errors


def main() -> int:
    mastery_cache = load_mastery_creature_cache()
    creature_keys: set[str] = set()
    for path in (DEF_ROOT / "creature").glob("*-creature-definition.json"):
        try:
            data = load_json(path)
            key = data.get("characteristic", {}).get("key")
            if isinstance(key, str):
                creature_keys.add(key)
        except json.JSONDecodeError:
            pass

    for path in sorted((DEF_ROOT / "creature").glob("*-creature-definition.json")):
        if path.match(MASTERY_DELETE_GLOB):
            continue
        text = path.read_text(encoding="utf-8")
        process_creature_file(path, load_json(path), text)

    delete_mastery_creature_files()

    for path in sorted((DEF_ROOT / "object").glob("*-object-definition.json")):
        text = path.read_text(encoding="utf-8")
        process_object_file(path, load_json(path), text)

    pull = DEF_ROOT / "spell" / "pull_distance-spell-definition.json"
    if pull.exists():
        create_push_distance_spell(pull)

    hostility_creature = DEF_ROOT / "creature" / "hostility-creature-definition.json"
    if hostility_creature.exists():
        create_hostility_spell(hostility_creature)

    for path in sorted((DEF_ROOT / "spell").glob("*-spell-definition.json")):
        text = path.read_text(encoding="utf-8")
        process_spell_file(path, load_json(path), text, creature_keys)

    create_profile_definitions(mastery_cache)
    create_consumables(DEF_ROOT / "spell")

    errors = validate_all_json()
    print("=== apply_characteristic_natures ===")
    print(f"updated:  {counts['updated']}")
    print(f"created:  {counts['created']}")
    print(f"deleted:  {counts['deleted']}")
    print(f"unchanged (skipped write): {counts['unchanged']}")
    if errors:
        print("JSON errors:")
        for e in errors:
            print(" ", e)
        return 1
    return 0


if __name__ == "__main__":
    sys.exit(main())
