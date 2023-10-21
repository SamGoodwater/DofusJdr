<?php

use Dompdf\Css\Color;

class Creature extends Content
{

    const FILES = [
        "logo" => [
            "type" => FileManager::FORMAT_IMG,
            "default" => "medias/modules/npc/default.svg",
            "dir" => "medias/modules/npc/",
            "naming" => "[uniqid]"
        ]
    ];

    const CARACTERISTICS = [
        "life" => [
            "name" => "Points de Vie maximum",
            "icon" => "life.png",
            "color" => "life",
            "price" => 50
        ],
        "pa" => [
            "name" => "PA",
            "icon" => "pa.png",
            "color" => "pa",
            "price" => 1300
        ],
        "pm" => [
            "name" => "PM",
            "icon" => "pm.png",
            "color" => "pm",
            "price" => 1000
        ],
        "po" => [
            "name" => "PO",
            "icon" => "po.png",
            "color" => "po",
            "price" => 800
        ],
        "ini" => [
            "name" => "Initiative",
            "icon" => "ini.png",
            "color" => "ini",
            "price" => 70
        ],
        "invocation" => [
            "name" => "Invocation",
            "icon" => "invocation.png",
            "color" => "invocation",
            "price" => 800
        ],
        "touch" => [
            "name" => "Touche",
            "icon" => "touch.png",
            "color" => "touch",
            "price" => 1200
        ],
        "ca" => [
            "name" => "CA",
            "icon" => "ca.png",
            "color" => "ca",
            "price" => 1100
        ],
        "dodge_pa" => [
            "name" => "Esquive PA",
            "icon" => "dodge_pa.png",
            "color" => "dodge_pa",
            "price" => 300
        ],
        "dodge_pm" => [
            "name" => "Esquive PM",
            "icon" => "dodge_pm.png",
            "color" => "dodge_pm",
            "price" => 300
        ],
        "fuite" => [
            "name" => "Fuite",
            "icon" => "fuite.png",
            "color" => "fuite",
            "price" => 300
        ],
        "tacle" => [
            "name" => "Tacle",
            "icon" => "tacle.png",
            "color" => "tacle",
            "price" => 300
        ],
        "vitality" => [
            "name" => "Vitalité",
            "icon" => "vitality.png",
            "color" => "vitality",
            "price" => 1200
        ],
        "sagesse" => [
            "name" => "Sagesse",
            "icon" => "sagesse.png",
            "color" => "sagesse",
            "price" => 1200
        ],
        "force" => [
            "name" => "Force",
            "icon" => "force.png",
            "color" => "force",
            "price" => 1000
        ],
        "intel" => [
            "name" => "Intelligence",
            "icon" => "intel.png",
            "color" => "intel",
            "price" => 1000
        ],
        "agi" => [
            "name" => "Agilité",
            "icon" => "agi.png",
            "color" => "agi",
            "price" => 1000
        ],
        "chance" => [
            "name" => "Chance",
            "icon" => "chance.png",
            "color" => "chance",
            "price" => 1000
        ],
        "do_fixe_neutre" => [
            "name" => "Dommage fixe neutre",
            "icon" => "do_fixe_neutre.png",
            "color" => "neutre",
            "price" => 700
        ],
        "do_fixe_terre" => [
            "name" => "Dommage fixe terre",
            "icon" => "do_fixe_terre.png",
            "color" => "terre",
            "price" => 700
        ],
        "do_fixe_feu" => [
            "name" => "Dommage fixe feu",
            "icon" => "do_fixe_feu.png",
            "color" => "feu",
            "price" => 700
        ],
        "do_fixe_air" => [
            "name" => "Dommage fixe air",
            "icon" => "do_fixe_air.png",
            "color" => "air",
            "price" => 700
        ],
        "do_fixe_eau" => [
            "name" => "Dommage fixe eau",
            "icon" => "do_fixe_eau.png",
            "color" => "eau",
            "price" => 700
        ],
        "do_fixe_multiple" => [
            "name" => "Dommage fixe multiple",
            "icon" => "do_fixe_multiple.png",
            "color" => "purple",
            "price" => 900
        ],
        "res_neutre" => [
            "name" => "Résistance neutre",
            "icon" => "res_neutre.png",
            "color" => "neutre",
            "price" => 700
        ],
        "res_terre" => [
            "name" => "Résistance terre",
            "icon" => "res_terre.png",
            "color" => "terre",
            "price" => 700
        ],
        "res_feu" => [
            "name" => "Résistance feu",
            "icon" => "res_feu.png",
            "color" => "feu",
            "price" => 700
        ],
        "res_air" => [
            "name" => "Résistance air",
            "icon" => "res_air.png",
            "color" => "air",
            "price" => 700
        ],
        "res_eau" => [
            "name" => "Résistance eau",
            "icon" => "res_eau.png",
            "color" => "eau",
            "price" => 700
        ],
        "wakfu_recharge" => [
            "name" => "Recharge de Wakfu",
            "icon" => "wakfu.png",
            "color" => "wakfu",
            "price" => 1500
        ],
        "skill_agi_of_choice" => [
            "name" => "Compétence de agi au choix",
            "icon" => "skill_agi",
            "color" => "agi",
            "price" => 300
        ],
        "acrobaties" => [
            "name" => "Acrobaties",
            "icon" => "skill_agi",
            "color" => "grey",
            "price" => 300
        ],
        "discretion" => [
            "name" => "Discrétion",
            "icon" => "skill_agi",
            "color" => "agi",
            "price" => 300
        ],
        "escamotage" => [
            "name" => "Escamotage",
            "icon" => "skill_agi",
            "color" => "agi",
            "price" => 300
        ],
        "skill_force_of_choice" => [
            "name" => "Compétence de force au choix",
            "icon" => "skill_force",
            "color" => "force",
            "price" => 300
        ],
        "athletisme" => [
            "name" => "Athlétisme",
            "icon" => "skill_force",
            "color" => "force",
            "price" => 300
        ],
        "intimidation" => [
            "name" => "Intimidation",
            "icon" => "skill_force",
            "color" => "force",
            "price" => 300
        ],
        "skill_intel_of_choice" => [
            "name" => "Compétence de intel au choix",
            "icon" => "skill_intel",
            "color" => "intel",
            "price" => 300
        ],
        "arcanes" => [
            "name" => "Arcanes",
            "icon" => "skill_intel",
            "color" => "intel",
            "price" => 300
        ],
        "histoire" => [
            "name" => "Histoire",
            "icon" => "skill_intel",
            "color" => "intel",
            "price" => 300
        ],
        "investigation" => [
            "name" => "Investigation",
            "icon" => "skill_intel",
            "color" => "intel",
            "price" => 300
        ],
        "nature" => [
            "name" => "Nature",
            "icon" => "skill_intel",
            "color" => "intel",
            "price" => 300
        ],
        "religion" => [
            "name" => "Religion",
            "icon" => "skill_intel",
            "color" => "intel",
            "price" => 300
        ],
        "skill_sagesse_of_choice" => [
            "name" => "Compétence de sagesse au choix",
            "icon" => "skill_sagesse",
            "color" => "sagesse",
            "price" => 300
        ],
        "dressage" => [
            "name" => "Dressage",
            "icon" => "skill_sagesse",
            "color" => "sagesse",
            "price" => 300
        ],
        "medecine" => [
            "name" => "Médecine",
            "icon" => "skill_sagesse",
            "color" => "sagesse",
            "price" => 300
        ],
        "perception" => [
            "name" => "Perception",
            "icon" => "skill_sagesse",
            "color" => "sagesse",
            "price" => 300
        ],
        "perspicacite" => [
            "name" => "Perspicacité",
            "icon" => "skill_sagesse",
            "color" => "sagesse",
            "price" => 300
        ],
        "survie" => [
            "name" => "Survie",
            "icon" => "skill_sagesse",
            "color" => "sagesse",
            "price" => 300
        ],
        "skill_chance_of_choice" => [
            "name" => "Compétence de chance au choix",
            "icon" => "skill_chance",
            "color" => "chance",
            "price" => 300
        ],
        "persuasion" => [
            "name" => "Persuasion",
            "icon" => "skill_chance",
            "color" => "chance",
            "price" => 300
        ],
        "representation" => [
            "name" => "Représentation",
            "icon" => "skill_chance",
            "color" => "chance",
            "price" => 300
        ],
        "supercherie" => [
            "name" => "Supercherie",
            "icon" => "skill_chance",
            "color" => "chance",
            "price" => 300
        ]
    ];

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ ATTRIBUTS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        private $_name='';
        private $_level=1;

        private $_life=30;
        private $_pa=6;
        private $_pm=3;
        private $_po=0;
        private $_ini=0;
        private $_invocation=0;
        private $_touch=0;
        private $_ca=0;
        private $_dodge_pa=0;
        private $_dodge_pm=0;
        private $_fuite=0;
        private $_tacle=0;
        private $_vitality=0;
        private $_sagesse=0;
        private $_strong=0;
        private $_intel=0;
        private $_agi=0;
        private $_chance=0;
        private $_do_fixe_neutre=0;
        private $_do_fixe_terre=0;
        private $_do_fixe_feu=0;
        private $_do_fixe_air=0;
        private $_do_fixe_eau=0;
        private $_do_fixe_multiple=0;
        private $_res_neutre=0;
        private $_res_terre=0;
        private $_res_feu=0;
        private $_res_air=0;
        private $_res_eau=0;
        private $_skill_agi_of_choice=0;
        private $_acrobatie=0;
        private $_discretion=0;
        private $_escamotage=0;
        private $_skill_force_of_choice=0;
        private $_athletisme=0;
        private $_intimidation=0;
        private $_skill_intel_of_choice=0;
        private $_arcane=0;
        private $_histoire=0;
        private $_investigation=0;
        private $_nature=0;
        private $_religion=0;
        private $_skill_sagesse_of_choice=0;
        private $_dressage=0;
        private $_medecine=0;
        private $_perception=0;
        private $_perspicacite=0;
        private $_survie=0;
        private $_skill_chance_of_choice=0;
        private $_persuasion=0;
        private $_representation=0;
        private $_supercherie=0;

        private $_other_item='';
        private $_other_consumable='';
        private $_other_spell='';

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ GETTERS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥


    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ SETTERS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
}