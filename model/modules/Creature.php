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
            "price" => 1
        ],
        "pa" => [
            "name" => "PA",
            "icon" => "pa.png",
            "color" => "pa",
            "price" => 1
        ],
        "pm" => [
            "name" => "PM",
            "icon" => "pm.png",
            "color" => "pm",
            "price" => 1
        ],
        "po" => [
            "name" => "PO",
            "icon" => "po.png",
            "color" => "po",
            "price" => 1
        ],
        "ini" => [
            "name" => "Initiative",
            "icon" => "ini.png",
            "color" => "ini",
            "price" => 1
        ],
        "invocation" => [
            "name" => "Invocation",
            "icon" => "invocation.png",
            "color" => "invocation",
            "price" => 1
        ],
        "touch" => [
            "name" => "Touche",
            "icon" => "touch.png",
            "color" => "touch",
            "price" => 1
        ],
        "ca" => [
            "name" => "CA",
            "icon" => "ca.png",
            "color" => "ca",
            "price" => 1
        ],
        "dodge_pa" => [
            "name" => "Esquive PA",
            "icon" => "dodge_pa.png",
            "color" => "dodge_pa",
            "price" => 1
        ],
        "dodge_pm" => [
            "name" => "Esquive PM",
            "icon" => "dodge_pm.png",
            "color" => "dodge_pm",
            "price" => 1
        ],
        "fuite" => [
            "name" => "Fuite",
            "icon" => "fuite.png",
            "color" => "fuite",
            "price" => 1
        ],
        "tacle" => [
            "name" => "Tacle",
            "icon" => "tacle.png",
            "color" => "tacle",
            "price" => 1
        ],
        "vitality" => [
            "name" => "Vitalité",
            "icon" => "vitality.png",
            "color" => "vitality",
            "price" => 1
        ],
        "sagesse" => [
            "name" => "Sagesse",
            "icon" => "sagesse.png",
            "color" => "sagesse",
            "price" => 1
        ],
        "force" => [
            "name" => "Force",
            "icon" => "force.png",
            "color" => "force",
            "price" => 1
        ],
        "intel" => [
            "name" => "Intelligence",
            "icon" => "intel.png",
            "color" => "intel",
            "price" => 1
        ],
        "agi" => [
            "name" => "Agilité",
            "icon" => "agi.png",
            "color" => "agi",
            "price" => 1
        ],
        "chance" => [
            "name" => "Chance",
            "icon" => "chance.png",
            "color" => "chance",
            "price" => 1
        ],
        "res_neutre" => [
            "name" => "Résistance neutre",
            "icon" => "res_neutre.png",
            "color" => "neutre",
            "price" => 1
        ],
        "res_terre" => [
            "name" => "Résistance terre",
            "icon" => "res_terre.png",
            "color" => "terre",
            "price" => 1
        ],
        "res_feu" => [
            "name" => "Résistance feu",
            "icon" => "res_feu.png",
            "color" => "feu",
            "price" => 1
        ],
        "res_air" => [
            "name" => "Résistance air",
            "icon" => "res_air.png",
            "color" => "air",
            "price" => 1
        ],
        "res_eau" => [
            "name" => "Résistance eau",
            "icon" => "res_eau.png",
            "color" => "eau",
            "price" => 1
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
        private $_res_neutre=0;
        private $_res_terre=0;
        private $_res_feu=0;
        private $_res_air=0;
        private $_res_eau=0;
        private $_acrobatie=0;
        private $_discretion=0;
        private $_escamotage=0;
        private $_athletisme=0;
        private $_intimidation=0;
        private $_arcane=0;
        private $_histoire=0;
        private $_investigation=0;
        private $_nature=0;
        private $_religion=0;
        private $_dressage=0;
        private $_medecine=0;
        private $_perception=0;
        private $_perspicacite=0;
        private $_survie=0;
        private $_persuasion=0;
        private $_representation=0;
        private $_supercherie=0;

        private $_other_item='';
        private $_other_consumable='';
        private $_other_spell='';

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ GETTERS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥


    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ SETTERS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
}