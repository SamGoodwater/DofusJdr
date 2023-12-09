<?php

use Dompdf\Css\Color;

abstract class Creature extends Content
{
    protected const VERBAL_NAME_OF_CLASSE = "de la créature";

    const FILES = [];

    const SKILL_NONE = 0;
    const SKILL_MASTERY = 1;
    const SKILL_EXPERTISE = 2;
    const SKILL_MATERY_LIST = [
        "Non maîtrisé" => self::SKILL_NONE,
        "Maîtrise" => self::SKILL_MASTERY,
        "Expertise" => self::SKILL_EXPERTISE
    ];

    const BALANCE_CARACTERISTICS_MAIN = [
        "classe" => [
            "max_item" => 10,
            "max_base" => 6,
            "min" => 0,
            "base" => 0,
            "expression_item" => "0,263157895 * (level - 1)",
            "expression_base" => "0",
            "bonus_item_max" => 4
        ],
        "mob" => [
            "max" => 12,
            "min" => -2,
            "base" => 0,
            "expression" => "0,263157895 * (level - 1)",
            "bonus" => 12
        ]
    ];

    const CARACTERISTICS = [
        "life" => [
            "name" => "Points de Vie maximum",
            "icon" => "life.png",
            "color" => "life",
            "price" => 50,
            'balance' => [
                "classe" => [
                    "max_item" => 30,
                    "max_base" => 20,
                    "min" => 8,
                    "base" => 9,
                    "expression_item" => "9 + 0,842105263 * (level - 1)",
                    "expression_base" => "9 + 0,578947368 * (level)",
                    "bonus_item_max" => 5
                ],
                "mob" => [
                    "max" => 30,
                    "min" => 5,
                    "base" => 8,
                    "expression" => "9 + 0,842105263 * (level - 1)",
                    "bonus" => 10
                ]
            ]
        ],
        "pa" => [
            "name" => "PA",
            "icon" => "pa.png",
            "color" => "pa",
            "price" => 1300,
            'balance' => [
                "classe" => [
                    "max_item" => 12,
                    "max_base" => 6,
                    "min" => 0,
                    "base" => 6,
                    "expression_item" => "6 + 0,315789474 * (level - 1)",
                    "expression_base" => "6",
                    "bonus_item_max" => 6
                ],
                "mob" => [
                    "max" => 16,
                    "min" => 0,
                    "base" => 4,
                    "expression" => "6 + 0,315789474 * (level - 1)",
                    "bonus" => 10,
                ]
            ]
        ],
        "pm" => [
            "name" => "PM",
            "icon" => "pm.png",
            "color" => "pm",
            "price" => 1000,
            'balance' => [
                "classe" => [
                    "max_item" => 12,
                    "max_base" => 6,
                    "min" => 0,
                    "base" => 6,
                    "expression_item" => "6 + 0,315789474 * (level - 1)",
                    "expression_base" => "6",
                    "bonus_item_max" => 6
                ],
                "mob" => [
                    "max" => 16,
                    "min" => 0,
                    "base" => 4,
                    "expression" => "6 + 0,315789474 * (level - 1)",
                    "bonus" => 10,
                ]
            ]
        ],
        "po" => [
            "name" => "PO",
            "icon" => "po.png",
            "color" => "po",
            "price" => 800,
            'balance' => [
                "classe" => [
                    "max_item" => 6,
                    "max_base" => 0,
                    "min" => 0,
                    "base" => 0,
                    "expression_item" => "0,315789474 * (level - 1)",
                    "expression_base" => "0",
                    "bonus_item_max" => 6
                ],
                "mob" => [
                    "max" => 8,
                    "min" => 0,
                    "base" => 0,
                    "expression" => "0,421052632 * (level - 1)",
                    "bonus" => 8
                ]
            ]
        ],
        "ini" => [
            "name" => "Initiative",
            "icon" => "ini.png",
            "color" => "ini",
            "price" => 70,
            'balance' => [
                "classe" => [
                    "max_item" => 30,
                    "max_base" => 10,
                    "min" => -2,
                    "base" => -1,
                    "expression_item" => "0,842105263 * (level - 1)",
                    "expression_base" => "-1 + (level-3)/2 + 1/level",
                    "bonus_item_max" => 20
                ],
                "mob" => [
                    "max" => 30,
                    "min" => -5,
                    "base" => -1,
                    "expression" => "0,842105263 * (level - 1)",
                    "bonus" => 20
                ]
            ]
        ],
        "invocation" => [
            "name" => "Invocation",
            "icon" => "invocation.png",
            "color" => "invocation",
            "price" => 800,
            'balance' => [
                "classe" => [
                    "max_item" => 6,
                    "max_base" => 1,
                    "min" => 1,
                    "base" => 1,
                    "expression_item" => "1 + 0,263157895 * (level - 1)",
                    "expression_base" => "0",
                    "bonus_item_max" => 5
                ],
                "mob" => [
                    "max" => 6,
                    "min" => 0,
                    "base" => 0,
                    "expression" => "0,263157895 * (level - 1)",
                    "bonus" => 6
                ]
            ]
        ],
        "touch" => [
            "name" => "Touche",
            "icon" => "touch.png",
            "color" => "touch",
            "price" => 1200,
            'balance' => [
                "classe" => [
                    "max_item" => 7,
                    "max_base" => 0,
                    "min" => 0,
                    "base" => 0,
                    "expression_item" => "0,368421053 * (level - 1)",
                    "expression_base" => "0",
                    "bonus_item_max" => 7
                ],
                "mob" => [
                    "max" => 8,
                    "min" => 0,
                    "base" => 0,
                    "expression" => "0,421052632 * (level - 1)",
                    "bonus" => 8
                ]
            ]
        ],
        "ca" => [
            "name" => "CA",
            "icon" => "ca.png",
            "color" => "ca",
            "price" => 1100,
            'balance' => [
                "classe" => [
                    "max_item" => 25,
                    "max_base" => 20,
                    "min" => 8,
                    "base" => 9,
                    "expression_item" => "9 + 0,842105263 * (level - 1)",
                    "expression_base" => "0,1 * (level -1)",
                    "bonus_item_max" => 5
                ],
                "mob" => [
                    "max" => 30,
                    "min" => 5,
                    "base" => 8,
                    "expression" => "9 + 0,842105263 * (level - 1)",
                    "bonus" => 10
                ]
            ]
        ],
        "dodge_pa" => [
            "name" => "Esquive PA",
            "icon" => "dodge_pa.png",
            "color" => "dodge_pa",
            "price" => 300,
            'balance' => [
                "classe" => [
                    "max_item" => 25,
                    "max_base" => 20,
                    "min" => 8,
                    "base" => 9,
                    "expression_item" => "9 + 0,842105263 * (level - 1)",
                    "expression_base" => "9 + 0,578947368 * (level)",
                    "bonus_item_max" => 5
                ],
                "mob" => [
                    "max" => 30,
                    "min" => 5,
                    "base" => 8,
                    "expression" => "9 + 0,842105263 * (level - 1)",
                    "bonus" => 10
                ]
            ]
            
        ],
        "dodge_pm" => [
            "name" => "Esquive PM",
            "icon" => "dodge_pm.png",
            "color" => "dodge_pm",
            "price" => 300,
            'balance' => [
                "classe" => [
                    "max_item" => 25,
                    "max_base" => 20,
                    "min" => 8,
                    "base" => 9,
                    "expression_item" => "9 + 0,842105263 * (level - 1)",
                    "expression_base" => "9 + 0,578947368 * (level)",
                    "bonus_item_max" => 5
                ],
                "mob" => [
                    "max" => 30,
                    "min" => 5,
                    "base" => 8,
                    "expression" => "9 + 0,842105263 * (level - 1)",
                    "bonus" => 10
                ]
            ]
        ],
        "fuite" => [
            "name" => "Fuite",
            "icon" => "fuite.png",
            "color" => "fuite",
            "price" => 300,
            'balance' => [
                "classe" => [
                    "max_item" => 25,
                    "max_base" => 20,
                    "min" => 8,
                    "base" => 9,
                    "expression_item" => "9 + 0,842105263 * (level - 1)",
                    "expression_base" => "9 + 0,578947368 * (level)",
                    "bonus_item_max" => 5
                ],
                "mob" => [
                    "max" => 30,
                    "min" => 5,
                    "base" => 8,
                    "expression" => "9 + 0,842105263 * (level - 1)",
                    "bonus" => 10
                ]
            ]
        ],
        "tacle" => [
            "name" => "Tacle",
            "icon" => "tacle.png",
            "color" => "tacle",
            "price" => 300,
            'balance' => [
                "classe" => [
                    "max_item" => 15,
                    "max_base" => 10,
                    "min" => -2,
                    "base" => -1,
                    "expression_item" => "-1 + 0,842105263 * (level - 1)",
                    "expression_base" => "(level-2)/2.4",
                    "bonus_item_max" => 5
                ],
                "mob" => [
                    "max" => 15,
                    "min" => -5,
                    "base" => -1,
                    "expression" => "-1 + 0,842105263 * (level - 1)",
                    "bonus" => 10
                ]
            ]
        ],
        "vitality" => [
            "name" => "Vitalité",
            "icon" => "vitality.png",
            "color" => "vitality",
            "price" => 1200,
            'balance' => self::BALANCE_CARACTERISTICS_MAIN
        ],
        "sagesse" => [
            "name" => "Sagesse",
            "icon" => "sagesse.png",
            "color" => "sagesse",
            "price" => 1200,
            'balance' => self::BALANCE_CARACTERISTICS_MAIN
        ],
        "force" => [
            "name" => "Force",
            "icon" => "force.png",
            "color" => "force",
            "price" => 1000,
            'balance' => self::BALANCE_CARACTERISTICS_MAIN
        ],
        "intel" => [
            "name" => "Intelligence",
            "icon" => "intel.png",
            "color" => "intel",
            "price" => 1000,
            'balance' => self::BALANCE_CARACTERISTICS_MAIN
        ],
        "agi" => [
            "name" => "Agilité",
            "icon" => "agi.png",
            "color" => "agi",
            "price" => 1000,
            'balance' => self::BALANCE_CARACTERISTICS_MAIN
        ],
        "chance" => [
            "name" => "Chance",
            "icon" => "chance.png",
            "color" => "chance",
            "price" => 1000,
            'balance' => self::BALANCE_CARACTERISTICS_MAIN
        ],
        "do_fixe_neutre" => [
            "name" => "Dommage fixe neutre",
            "icon" => "do_fixe_neutre.png",
            "color" => "neutre",
            "price" => 700,
            'balance' => [
                "classe" => [
                    "max_item" => 6,
                    "max_base" => 0,
                    "min" => 0,
                    "base" => 0,
                    "expression_item" => "0,315789474 * (level - 1)",
                    "expression_base" => "0",
                    "bonus_item_max" => 6
                ],
                "mob" => [
                    "max" => 8,
                    "min" => 0,
                    "base" => 0,
                    "expression" => "0,421052632 * (level - 1)",
                    "bonus" => 8
                ]
            ]
        ],
        "do_fixe_terre" => [
            "name" => "Dommage fixe terre",
            "icon" => "do_fixe_terre.png",
            "color" => "terre",
            "price" => 700,
            'balance' => [
                "classe" => [
                    "max_item" => 6,
                    "max_base" => 0,
                    "min" => 0,
                    "base" => 0,
                    "expression_item" => "0,315789474 * (level - 1)",
                    "expression_base" => "0",
                    "bonus_item_max" => 6
                ],
                "mob" => [
                    "max" => 8,
                    "min" => 0,
                    "base" => 0,
                    "expression" => "0,421052632 * (level - 1)",
                    "bonus" => 8
                ]
            ]
        ],
        "do_fixe_feu" => [
            "name" => "Dommage fixe feu",
            "icon" => "do_fixe_feu.png",
            "color" => "feu",
            "price" => 700,
            'balance' => [
                "classe" => [
                    "max_item" => 6,
                    "max_base" => 0,
                    "min" => 0,
                    "base" => 0,
                    "expression_item" => "0,315789474 * (level - 1)",
                    "expression_base" => "0",
                    "bonus_item_max" => 6
                ],
                "mob" => [
                    "max" => 8,
                    "min" => 0,
                    "base" => 0,
                    "expression" => "0,421052632 * (level - 1)",
                    "bonus" => 8
                ]
            ]
        ],
        "do_fixe_air" => [
            "name" => "Dommage fixe air",
            "icon" => "do_fixe_air.png",
            "color" => "air",
            "price" => 700,
            'balance' => [
                "classe" => [
                    "max_item" => 6,
                    "max_base" => 0,
                    "min" => 0,
                    "base" => 0,
                    "expression_item" => "0,315789474 * (level - 1)",
                    "expression_base" => "0",
                    "bonus_item_max" => 6
                ],
                "mob" => [
                    "max" => 8,
                    "min" => 0,
                    "base" => 0,
                    "expression" => "0,421052632 * (level - 1)",
                    "bonus" => 8
                ]
            ]
        ],
        "do_fixe_eau" => [
            "name" => "Dommage fixe eau",
            "icon" => "do_fixe_eau.png",
            "color" => "eau",
            "price" => 700,
            'balance' => [
                "classe" => [
                    "max_item" => 6,
                    "max_base" => 0,
                    "min" => 0,
                    "base" => 0,
                    "expression_item" => "0,315789474 * (level - 1)",
                    "expression_base" => "0",
                    "bonus_item_max" => 6
                ],
                "mob" => [
                    "max" => 8,
                    "min" => 0,
                    "base" => 0,
                    "expression" => "0,421052632 * (level - 1)",
                    "bonus" => 8
                ]
            ]
        ],
        "do_fixe_multiple" => [
            "name" => "Dommage fixe multiple",
            "icon" => "do_fixe_multiple.png",
            "color" => "purple",
            "price" => 900,
            'balance' => [
                "classe" => [
                    "max_item" => 6,
                    "max_base" => 3,
                    "min" => 0,
                    "base" => 3,
                    "expression_item" => "3 + 0,157894737 * (level - 1)",
                    "expression_base" => "3",
                    "bonus_item_max" => 3
                ],
                "mob" => [
                    "max" => 10,
                    "min" => 0,
                    "base" => 3,
                    "expression" => "3 + 0,2 * (level - 1)",
                    "bonus" => 5
                ]
            ]
        ],
        "res_neutre" => [
            "name" => "Résistance neutre",
            "icon" => "res_neutre.png",
            "color" => "neutre",
            "price" => 700,
            'balance' => [
                "classe" => [
                    "max_item" => 6,
                    "max_base" => 3,
                    "min" => 0,
                    "base" => 3,
                    "expression_item" => "3 + 0,157894737 * (level - 1)",
                    "expression_base" => "3",
                    "bonus_item_max" => 3
                ],
                "mob" => [
                    "max" => 10,
                    "min" => 0,
                    "base" => 3,
                    "expression" => "3 + 0,2 * (level - 1)",
                    "bonus" => 5
                ]
            ]
        ],
        "res_terre" => [
            "name" => "Résistance terre",
            "icon" => "res_terre.png",
            "color" => "terre",
            "price" => 700,
            'balance' => [
                "classe" => [
                    "max_item" => 6,
                    "max_base" => 3,
                    "min" => 0,
                    "base" => 3,
                    "expression_item" => "3 + 0,157894737 * (level - 1)",
                    "expression_base" => "3",
                    "bonus_item_max" => 3
                ],
                "mob" => [
                    "max" => 10,
                    "min" => 0,
                    "base" => 3,
                    "expression" => "3 + 0,2 * (level - 1)",
                    "bonus" => 5
                ]
            ]
        ],
        "res_feu" => [
            "name" => "Résistance feu",
            "icon" => "res_feu.png",
            "color" => "feu",
            "price" => 700,
            'balance' => [
                "classe" => [
                    "max_item" => 6,
                    "max_base" => 3,
                    "min" => 0,
                    "base" => 3,
                    "expression_item" => "3 + 0,157894737 * (level - 1)",
                    "expression_base" => "3",
                    "bonus_item_max" => 3
                ],
                "mob" => [
                    "max" => 10,
                    "min" => 0,
                    "base" => 3,
                    "expression" => "3 + 0,2 * (level - 1)",
                    "bonus" => 5
                ]
            ]
        ],
        "res_air" => [
            "name" => "Résistance air",
            "icon" => "res_air.png",
            "color" => "air",
            "price" => 700,
            'balance' => [
                "classe" => [
                    "max_item" => 6,
                    "max_base" => 3,
                    "min" => 0,
                    "base" => 3,
                    "expression_item" => "3 + 0,157894737 * (level - 1)",
                    "expression_base" => "3",
                    "bonus_item_max" => 3
                ],
                "mob" => [
                    "max" => 10,
                    "min" => 0,
                    "base" => 3,
                    "expression" => "3 + 0,2 * (level - 1)",
                    "bonus" => 5
                ]
            ]
        ],
        "res_eau" => [
            "name" => "Résistance eau",
            "icon" => "res_eau.png",
            "color" => "eau",
            "price" => 700,
            'balance' => [
                "classe" => [
                    "max_item" => 6,
                    "max_base" => 3,
                    "min" => 0,
                    "base" => 3,
                    "expression_item" => "3 + 0,157894737 * (level - 1)",
                    "expression_base" => "3",
                    "bonus_item_max" => 3
                ],
                "mob" => [
                    "max" => 10,
                    "min" => 0,
                    "base" => 3,
                    "expression" => "3 + 0,2 * (level - 1)",
                    "bonus" => 5
                ]
            ]
        ],
        "wakfu_recharge" => [
            "name" => "Recharge de Wakfu",
            "icon" => "wakfu.png",
            "color" => "wakfu",
            "price" => 1500,
            'balance' => [
                "classe" => [
                    "max_item" => 3,
                    "max_base" => 0,
                    "min" => 0,
                    "base" => 0,
                    "expression_item" => "0,157894737 * (level - 1)",
                    "expression_base" => "0",
                    "bonus_item_max" => 3
                ],
                "mob" => [
                    "max" => 5,
                    "min" => 0,
                    "base" => 0,
                    "expression" => "0,210526316 * (level - 1)",
                    "bonus" => 5
                ]
            ]
        ],
        "skill_agi_of_choice" => [
            "name" => "Compétence de agi au choix",
            "icon" => "skill_agi",
            "color" => "agi",
            "price" => 300,    
            "balance" => [
                "classe" => [
                    "max_item" => 18,
                    "max_base" => 10,
                    "min" => -2,
                    "base" => -1,
                    "expression_item" => "-1 + 0,9 * (level - 1)",
                    "expression_base" => "-1 + 0,421052632 * ((level - 1) - (0,4 * level / 24))",
                    "bonus_item_max" => 8
                ],
                "mob" => [
                    "max" => 20,
                    "min" => -5,
                    "base" => -1,
                    "expression" => "-1 + 0,9 * (level - 1)",
                    "bonus" => 10
                ]
            ]        
        ],
        "acrobatie" => [
            "name" => "Acrobatie",
            "icon" => "skill_agi",
            "color" => "grey",
            "price" => 300,
            "balance" => [
                "classe" => [
                    "max_item" => 18,
                    "max_base" => 10,
                    "min" => -2,
                    "base" => -1,
                    "expression_item" => "-1 + 0,9 * (level - 1)",
                    "expression_base" => "-1 + 0,421052632 * ((level - 1) - (0,4 * level / 24))",
                    "bonus_item_max" => 8
                ],
                "mob" => [
                    "max" => 20,
                    "min" => -5,
                    "base" => -1,
                    "expression" => "-1 + 0,9 * (level - 1)",
                    "bonus" => 10
                ]
            ]
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
            "price" => 300,
            "balance" => [
                "classe" => [
                    "max_item" => 18,
                    "max_base" => 10,
                    "min" => -2,
                    "base" => -1,
                    "expression_item" => "-1 + 0,9 * (level - 1)",
                    "expression_base" => "-1 + 0,421052632 * ((level - 1) - (0,4 * level / 24))",
                    "bonus_item_max" => 8
                ],
                "mob" => [
                    "max" => 20,
                    "min" => -5,
                    "base" => -1,
                    "expression" => "-1 + 0,9 * (level - 1)",
                    "bonus" => 10
                ]
            ]
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
            "price" => 300,
            "balance" => [
                "classe" => [
                    "max_item" => 18,
                    "max_base" => 10,
                    "min" => -2,
                    "base" => -1,
                    "expression_item" => "-1 + 0,9 * (level - 1)",
                    "expression_base" => "-1 + 0,421052632 * ((level - 1) - (0,4 * level / 24))",
                    "bonus_item_max" => 8
                ],
                "mob" => [
                    "max" => 20,
                    "min" => -5,
                    "base" => -1,
                    "expression" => "-1 + 0,9 * (level - 1)",
                    "bonus" => 10
                ]
            ]
        ],
        "arcane" => [
            "name" => "Arcane",
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
            "price" => 300,
            "balance" => [
                "classe" => [
                    "max_item" => 18,
                    "max_base" => 10,
                    "min" => -2,
                    "base" => -1,
                    "expression_item" => "-1 + 0,9 * (level - 1)",
                    "expression_base" => "-1 + 0,421052632 * ((level - 1) - (0,4 * level / 24))",
                    "bonus_item_max" => 8
                ],
                "mob" => [
                    "max" => 20,
                    "min" => -5,
                    "base" => -1,
                    "expression" => "-1 + 0,9 * (level - 1)",
                    "bonus" => 10
                ]
            ]
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
            "price" => 300,
            "balance" => [
                "classe" => [
                    "max_item" => 18,
                    "max_base" => 10,
                    "min" => -2,
                    "base" => -1,
                    "expression_item" => "-1 + 0,9 * (level - 1)",
                    "expression_base" => "-1 + 0,421052632 * ((level - 1) - (0,4 * level / 24))",
                    "bonus_item_max" => 8
                ],
                "mob" => [
                    "max" => 20,
                    "min" => -5,
                    "base" => -1,
                    "expression" => "-1 + 0,9 * (level - 1)",
                    "bonus" => 10
                ]
            ]
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

    public function __construct(array $data){
        parent::__construct($data);

        $this->_bonus_array = [];
        $items = $this->getItem(Content::FORMAT_ARRAY);
        if(!empty($items)){
            foreach ($items as $item) {
                $bonus_list = $item['obj']->getBonus(Content::FORMAT_ARRAY);
                $multi = 1;
                if($item['quantity'] > 1) { $multi = $item['quantity'];}
                if(!empty($bonus_list)){
                    foreach ($bonus_list as $name => $bonus) {
                        if(isset($this->_bonus_array[$name])){
                            $this->_bonus_array[$name] += $bonus['value'] * $multi;
                        } else {
                            $this->_bonus_array[$name] = $bonus['value'] * $multi;
                        }
                    }
                }
            }
        }
    }

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ ATTRIBUTS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        protected $_name='';
        protected $_description='';
        protected $_trait='';
        protected $_level=1;
        protected $_other_info='';
        protected $_location='';

        protected $_life=30;
        protected $_pa=6;
        protected $_pm=3;
        protected $_po=0;
        protected $_ini=0;
        protected $_invocation=0;
        protected $_touch=0;
        protected $_ca=0;
        protected $_dodge_pa=0;
        protected $_dodge_pm=0;
        protected $_fuite=0;
        protected $_tacle=0;
        protected $_vitality=0;
        protected $_sagesse=0;
        protected $_strong=0;
        protected $_intel=0;
        protected $_agi=0;
        protected $_chance=0;
        protected $_do_fixe_neutre=0;
        protected $_do_fixe_terre=0;
        protected $_do_fixe_feu=0;
        protected $_do_fixe_air=0;
        protected $_do_fixe_eau=0;
        protected $_do_fixe_multiple=0;
        protected $_res_neutre=0;
        protected $_res_terre=0;
        protected $_res_feu=0;
        protected $_res_air=0;
        protected $_res_eau=0;

        // COMPETENCES
        protected $_skill_agi_of_choice=0;
        protected $_acrobatie_bonus=0;
        protected $_discretion_bonus=0;
        protected $_escamotage_bonus=0;
        protected $_skill_force_of_choice=0;
        protected $_athletisme_bonus=0;
        protected $_intimidation_bonus=0;
        protected $_skill_intel_of_choice=0;
        protected $_arcane_bonus=0;
        protected $_histoire_bonus=0;
        protected $_investigation_bonus=0;
        protected $_nature_bonus=0;
        protected $_religion_bonus=0;
        protected $_skill_sagesse_of_choice=0;
        protected $_dressage_bonus=0;
        protected $_medecine_bonus=0;
        protected $_perception_bonus=0;
        protected $_perspicacite_bonus=0;
        protected $_survie_bonus=0;
        protected $_skill_chance_of_choice=0;
        protected $_persuasion_bonus=0;
        protected $_representation_bonus=0;
        protected $_supercherie_bonus=0;
        // 0 = rien, 1 = maitrise, 2 = expertise
        protected $_acrobatie_mastery=self::SKILL_NONE;
        protected $_discretion_mastery=self::SKILL_NONE;
        protected $_escamotage_mastery=self::SKILL_NONE;
        protected $_athletisme_mastery=self::SKILL_NONE;
        protected $_intimidation_mastery=self::SKILL_NONE;
        protected $_arcane_mastery=self::SKILL_NONE;
        protected $_histoire_mastery=self::SKILL_NONE;
        protected $_investigation_mastery=self::SKILL_NONE;
        protected $_nature_mastery=self::SKILL_NONE;
        protected $_religion_mastery=self::SKILL_NONE;
        protected $_dressage_mastery=self::SKILL_NONE;
        protected $_medecine_mastery=self::SKILL_NONE;
        protected $_perception_mastery=self::SKILL_NONE;
        protected $_perspicacite_mastery=self::SKILL_NONE;
        protected $_survie_mastery=self::SKILL_NONE;
        protected $_persuasion_mastery=self::SKILL_NONE;
        protected $_representation_mastery=self::SKILL_NONE;
        protected $_supercherie_mastery=self::SKILL_NONE;

        protected $_other_item='';
        protected $_other_consumable='';
        protected $_other_spell='';

        protected $_kamas='';
        protected $_drop_='';

        protected $_bonus_array = [];

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ GETTERS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        public function getName(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::DISPLAY_EDITABLE:  
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => ucfirst(get_class($this)),
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "name",
                            "label" => "Nom",
                            "placeholder" => "Nom " . self::VERBAL_NAME_OF_CLASSE,
                            "value" => $this->_name,
                            "style" => Style::INPUT_FLOATING
                        ], 
                        write: false);
                
                default:
                    return $this->_name;
            }
        }
        public function getDescription(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::DISPLAY_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/ckeditor",
                        data : [
                            "class_name" => ucfirst(get_class($this)),
                            "id" => "description".$this->getUniqid(),
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "description",
                            "label" => "Description",
                            "value" => $this->_description
                        ], 
                        write: false);
                
                default:
                    if(empty($this->_description)){return "";}
                    return html_entity_decode($this->_description);
            }
        }
        public function getTrait(int $format = Content::FORMAT_BRUT){
            $view = new View();
            $traits = array();
            if(!empty($this->_trait)){
                $traits = explode(",", $this->_trait);
            }

            switch ($format) {
                case Content::DISPLAY_EDITABLE:
                    ob_start();
                        $view->dispatch(
                            template_name : "input/textarea",
                            data : [
                                "class_name" => ucfirst(get_class($this)),
                                "uniqid" => $this->getUniqid(),
                                "input_name" => "trait",
                                "label" => "Traits",
                                "value" => $this->_trait,
                                "placeholder" => "Traits",
                                "style" => Style::INPUT_FLOATING,
                                "comment" => "Séparer les différents traits par des virgules."
                            ], 
                            write: true);
                    return ob_get_clean();

                case Content::FORMAT_BADGE:
                    ob_start(); ?>
                        <div class="d-flex flex-row justify-content-around"> <?php
                            foreach ($traits as $trait) { 
                                $view->dispatch(
                                    template_name : "badge",
                                    data : [
                                        "color" => Style::getColorFromLetter($trait) . "-d-1",
                                        "content" => $trait,
                                        "style" => Style::STYLE_BACK,
                                        "tooltip" => "Trait ".$trait,
                                        "tooltip_placement" => "top"
                                    ], 
                                    write: true);
                                ?>
                                <?php } ?>                            
                        </div>
                    <?php return ob_get_clean();

                case Content::FORMAT_VIEW:
                    return $this->getTrait(Content::FORMAT_BADGE);

                default:
                    return $this->_trait;
            }

        }
        public function getLevel(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::DISPLAY_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => ucfirst(get_class($this)),
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "level",
                            "label" => "Niveau",
                            "placeholder" => "Niveau",
                            "tooltip" => "Niveau " . self::VERBAL_NAME_OF_CLASSE,
                            "value" => $this->_level,
                            "color" => Style::getColorFromLetter($this->_level, true) . "-d-3"
                        ], 
                        write: false);

                case Content::FORMAT_LIST:
                    if(preg_match("/\[.*\]/", $this->_level)){
                        ob_start(); ?>
                            <div class="dropdown">
                                <a class="btn btn-text-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"><?=$this->getLevel(Content::FORMAT_BADGE)?></a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" onclick="Mob.updateDisplayCaracteristics('<?=$this->getUniqid()?>', 0)"><span class='badge back-<?=Style::getColorFromLetter($this->_level, true)?>-d-3' data-bs-toggle='tooltip' data-bs-placement='bottom' title="Niveau <?=self::VERBAL_NAME_OF_CLASSE?>">Niveau <?=$this->_level?></span></a></li>
                                    <?php $level = Content::getMinMaxFromFormule($this->_level);
                                    if($level['same']){ ?>
                                        <li><a class="dropdown-item" onclick="Mob.updateDisplayCaracteristics('<?=$this->getUniqid()?>', '<?=$level['max']?>')"><span class='badge back-<?=Style::getColorFromLetter($level['max'], true)?>-d-3' data-bs-toggle='tooltip' data-bs-placement='bottom' title="Générer une créature de niveau <?=$level["max"]?>">Niveau <?=$level["max"]?></span></a></li>
                                    <?php } else {
                                        if($level['min'] == 0){$level['min'] = 1;}
                                        if($level['max'] == 0){$level['max'] = 1;}
                                        for($i=$level['min']; $i<=$level['max']; $i++){ ?>
                                            <li><a class="dropdown-item" onclick="Mob.updateDisplayCaracteristics('<?=$this->getUniqid()?>', '<?=$i?>')"><span class='badge back-<?=Style::getColorFromLetter($i, true)?>-d-3' data-bs-toggle='tooltip' data-bs-placement='bottom' title="Générer une créature de niveau <?=$i?>">Niveau <?=$i?></span></a></li>
                                        <?php }
                                    } ?>
                                </ul>
                            </div>
                        <?php return ob_get_clean();
                    } else {
                        return $this->getLevel(Content::FORMAT_BADGE);
                    }
                
                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "Niveau {$this->_level}",
                            "color" => Style::getColorFromLetter($this->_level, true) . "-d-3",
                            "tooltip" => "Niveau " . self::VERBAL_NAME_OF_CLASSE,
                            "style" => Style::STYLE_OUTLINE
                        ], 
                        write: false);
                   
                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => $this->_level,
                            "color" => "",
                            "tooltip" => "Niveau " . self::VERBAL_NAME_OF_CLASSE,
                            "style" => Style::STYLE_NONE,
                            "class" => "text-".Style::getColorFromLetter($this->_level, true) . "-d-3"
                        ], 
                        write: false);
                
                default:
                    return $this->_level;
            }
        }
        public function getOther_info(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::DISPLAY_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/ckeditor",
                        data : [
                            "class_name" => ucfirst(get_class($this)),
                            "id" => "other_info".$this->getUniqid(),
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "other_info",
                            "label" => "Caractères et autres informations",
                            "value" => $this->_other_info
                        ], 
                        write: false);
                
                default:
                    if(empty($this->_other_info)){return "";}
                    return html_entity_decode($this->_other_info);
            }
        }
        public function getLocation(int $format = Content::FORMAT_BRUT){
            $view = new View();

            switch ($format) {
                case Content::DISPLAY_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/textarea",
                        data : [
                            "class_name" => ucfirst(get_class($this)),
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "location",
                            "label" => "Localisation",
                            "value" => $this->_location,
                            "placeholder" => "Lieu de vie " . self::VERBAL_NAME_OF_CLASSE,
                            "style" => Style::INPUT_FLOATING
                        ], 
                        write: false);

                case Content::FORMAT_VIEW:
                    ob_start(); ?>
                        <div>
                            <p class="size-0-7 text-grey-d-2">Localisation :</p>
                            <?=$this->_location?>
                        </div>
                    <?php return ob_get_clean();

                default:
                    return $this->_location;
            }

        }
      
        public function getLife(int $format = Content::FORMAT_BRUT){
            $view = new View();

            $bonus_item = 0;
            if(is_numeric($this->getBonus_caract_by_item("life"))){
                $bonus_item = $this->getBonus_caract_by_item("life");
            }
            $total = "";
            $total_int = 0;
            $total_str = "";
            if(is_numeric($this->_life)){
                $total_int += $this->_life + $bonus_item;
                $total = $total_int;
            } else {
                $total_int += $bonus_item;
                $total_str = $this->_life;
                $total = $total_int . " + " . $total_str;
            }
            $data = ""; $detail_on_level = "";
            if(!empty($total_str)){
                $level = Content::getMinMaxFromFormule($this->getLevel());$data = "data-formule='".$total."' data-text='pts de vie max'";
                if($level['same'] != true){
                    for($i=$level['min']; $i<=$level['max']; $i++){
                        $total_int_on_level = $total_int;
                        $total_int_on_level += Content::getValueFromFormule($total_str, $i);
                        $detail_on_level .= "<p class='size-0-7 text-grey-d-2'>lvl ".$i." : <span class='bold'>".$total_int_on_level."</span></p>";
                        $data .= " data-level".$i."='".$total_int_on_level."' ";
                    }
                }
            }

            switch ($format) {
                case Content::DISPLAY_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => ucfirst(get_class($this)),
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "life",
                            "label" => "Points de vie",
                            "placeholder" => "Points de vie " . self::VERBAL_NAME_OF_CLASSE,
                            "tooltip" => "Calcul des points de vie",
                            "value" => $this->_life,
                            "color" => "life-d-2",
                            "style" => Style::INPUT_ICON,
                            "size" => Style::SIZE_SM,
                            "icon" => "life.png",
                            "comment" => "Dés de classe" . " + ". $bonus_item ." (Bonus d'équipement)",
                            "style_icon" => Style::ICON_MEDIA
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$total} Points de vie",
                            "color" => "life-d-2",
                            "tooltip" => "Calcul des points de vie",
                            "style" => Style::STYLE_BACK,
                            "data" => $data,
                            "id" => "life"
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "life.png",
                            "color" => "life-d-2",
                            "tooltip" => "Calcul des points de vie",
                            "content" => $total,
                            "content_placement" => Style::POSITION_RIGHT
                        ], 
                        write: false); 

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "modules/characteristic",
                        data : [
                            "name" => "Points de vie max",
                            "value" => $total,
                            "color" => "life",
                            "icon" => "life.png",
                            "size" => Style::SIZE_XL,
                            "data" => $data,
                            "detail" => "Dès de classe + Vitalité x niveau + ".$this->_life." (bonus) + ".$bonus_item." (équipement)",
                            "detail_on_level" => $detail_on_level
                        ], 
                        write: false);
                   
                default:
                    return $this->_life;
            }
        }
        public function getPa(int $format = Content::FORMAT_BRUT){
            $view = new View();

            $bonus_item = 0;
            if(is_numeric($this->getBonus_caract_by_item("pa"))){
                $bonus_item = $this->getBonus_caract_by_item("pa");
            }
            $total = "";
            $total_int = 0;
            $total_str = "";
            if(is_numeric($this->_pa)){
                $total_int += $this->_pa + $bonus_item;
                $total = $total_int;
            } else {
                $total_int += $bonus_item;
                $total_str = $this->_pa;
                $total = $total_int . " + " . $total_str;
            }
            $data = ""; $detail_on_level = "";
            if(!empty($total_str)){
                $level = Content::getMinMaxFromFormule($this->getLevel());$data = "data-formule='".$total."' data-text='pa'";
                if($level['same'] != true){
                    for($i=$level['min']; $i<=$level['max']; $i++){
                        $total_int_on_level = $total_int;
                        $total_int_on_level += Content::getValueFromFormule($total_str, $i);
             $detail_on_level .= "<p class='size-0-7 text-grey-d-2'>lvl ".$i." : <span class='bold'>".$total_int_on_level."</span></p>";
                        $data .= " data-level".$i."='".$total_int_on_level."' ";
                    }
                }
            }

            switch ($format) {
                case Content::DISPLAY_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => ucfirst(get_class($this)),
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "pa",
                            "label" => "PA",
                            "placeholder" => "Points d'action " . self::VERBAL_NAME_OF_CLASSE,
                            "tooltip" => "PA",
                            "value" => $this->_pa,
                            "color" => "pa",
                            "style" => Style::INPUT_ICON,
                            "size" => Style::SIZE_SM,
                            "icon" => "pa.png",
                            "comment" => "Bonus" . " + ". $bonus_item ." (équipement)",
                            "style_icon" => Style::ICON_MEDIA
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$total} PA",
                            "color" => "pa",
                            "tooltip" => "PA",
                            "style" => Style::STYLE_BACK,
                            "data" => $data,
                            "id" => "pa"
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "pa.png",
                            "color" => "pa",
                            "tooltip" => "PA",
                            "size" => 20,
                            "content" => $total,
                            "content_placement" => Style::POSITION_RIGHT
                        ], 
                        write: false); 
                
                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "modules/characteristic",
                        data : [
                            "name" => "PA",
                            "value" => $total,
                            "color" => "pa",
                            "icon" => "pa.png",
                            "size" => Style::SIZE_XL,
                            "data" => $data,
                            "tooltips" => "Points d'action",
                            "detail" => $this->_pa . " (Bonus) + ".$bonus_item." (équipement)",
                            "detail_on_level" => $detail_on_level
                        ], 
                        write: false);

                default:
                    return $this->_pa;
            }
        }
        public function getPm(int $format = Content::FORMAT_BRUT){
            $view = new View();

            $bonus_item = 0;
            if(is_numeric($this->getBonus_caract_by_item("pm"))){
                $bonus_item = $this->getBonus_caract_by_item("pm");
            }
            $total = "";
            $total_int = 0;
            $total_str = "";
            if(is_numeric($this->_pm)){
                $total_int += $this->_pm + $bonus_item;
                $total = $total_int;
            } else {
                $total_int += $bonus_item;
                $total_str = $this->_pm;
                $total = $total_int . " + " . $total_str;
            }
            $data = ""; $detail_on_level = "";
            if(!empty($total_str)){
                $level = Content::getMinMaxFromFormule($this->getLevel());$data = "data-formule='".$total."' data-text='pm'";
                if($level['same'] != true){
                    for($i=$level['min']; $i<=$level['max']; $i++){
                        $total_int_on_level = $total_int;
                        $total_int_on_level += Content::getValueFromFormule($total_str, $i);
             $detail_on_level .= "<p class='size-0-7 text-grey-d-2'>lvl ".$i." : <span class='bold'>".$total_int_on_level."</span></p>";
                        $data .= " data-level".$i."='".$total_int_on_level."' ";
                    }
                }
            }

            switch ($format) {
                case Content::DISPLAY_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => ucfirst(get_class($this)),
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "pm",
                            "label" => "PM",
                            "placeholder" => "Points de mouvement " . self::VERBAL_NAME_OF_CLASSE,
                            "tooltip" => "PM",
                            "value" => $this->_pm,
                            "color" => "pm",
                            "style" => Style::INPUT_ICON,
                            "size" => Style::SIZE_SM,
                            "icon" => "pm.png",
                            "comment" => "Bonus" . " + ". $bonus_item ." (équipement)",
                            "style_icon" => Style::ICON_MEDIA
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$total} PM",
                            "color" => "pm",
                            "tooltip" => "PM",
                            "style" => Style::STYLE_BACK,
                            "data" => $data,
                            "id" => "pm"
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "pm.png",
                            "color" => "pm",
                            "tooltip" => "PM",
                            "content" => $this->_pm,
                            "content_placement" => Style::POSITION_RIGHT
                        ], 
                        write: false); 

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "modules/characteristic",
                        data : [
                            "name" => "PM",
                            "value" => $total,
                            "color" => "pm",
                            "icon" => "pm.png",
                            "size" => Style::SIZE_XL,
                            "data" => $data,
                            "tooltips" => "Points de mouvement",
                            "detail" => $this->_pm ." (Bonus) + ".$bonus_item." (équipement)",
                            "detail_on_level" => $detail_on_level
                        ], 
                        write: false);

                default:
                    return $this->_pm;
            }
        }
        public function getPo(int $format = Content::FORMAT_BRUT){
            $view = new View();

            $bonus_item = 0;
            if(is_numeric($this->getBonus_caract_by_item("po"))){
                $bonus_item = $this->getBonus_caract_by_item("po");
            }
            $total = "";
            $total_int = 0;
            $total_str = "";
            if(is_numeric($this->_po)){
                $total_int += $this->_po + $bonus_item;
                $total = $total_int;
            } else {
                $total_int += $bonus_item;
                $total_str = $this->_po;
                $total = $total_int . " + " . $total_str;
            }
            $data = ""; $detail_on_level = "";
            if(!empty($total_str)){
                $level = Content::getMinMaxFromFormule($this->getLevel());$data = "data-formule='".$total."' data-text='po'";
                if($level['same'] != true){
                    for($i=$level['min']; $i<=$level['max']; $i++){
                        $total_int_on_level = $total_int;
                        $total_int_on_level += Content::getValueFromFormule($total_str, $i);
             $detail_on_level .= "<p class='size-0-7 text-grey-d-2'>lvl ".$i." : <span class='bold'>".$total_int_on_level."</span></p>";
                        $data .= " data-level".$i."='".$total_int_on_level."' ";
                    }
                }
            }
            
            switch ($format) {
                case Content::DISPLAY_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => ucfirst(get_class($this)),
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "po",
                            "label" => "PO",
                            "placeholder" => "Portée " . self::VERBAL_NAME_OF_CLASSE,
                            "tooltip" => "PO",
                            "value" => $this->_po,
                            "color" => "po",
                            "style" => Style::INPUT_ICON,
                            "size" => Style::SIZE_SM,
                            "icon" => "po.png",
                            "comment" => "Bonus" . " + ". $bonus_item ." (équipement)",
                            "style_icon" => Style::ICON_MEDIA
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$total} PO",
                            "color" => "po",
                            "tooltip" => "PO",
                            "style" => Style::STYLE_BACK,
                            "data" => $data,
                            "id" => "po"
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "po.png",
                            "color" => "po",
                            "tooltip" => "PO",
                            "content" => $total,
                            "content_placement" => Style::POSITION_RIGHT
                        ], 
                        write: false); 

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "modules/characteristic",
                        data : [
                            "name" => "PO",
                            "value" => $total,
                            "color" => "po",
                            "icon" => "po.png",
                            "size" => Style::SIZE_MD,
                            "data" => $data,
                            "tooltips" => "Portée",
                            "detail" => $this->_po." (Bonus) + ".$bonus_item." (équipement)",
                            "detail_on_level" => $detail_on_level
                        ], 
                        write: false);

                default:
                    return $this->_po;
            }
        }
        public function getIni(int $format = Content::FORMAT_BRUT){
            $view = new View();

            $bonus_item = 0;
            if(is_numeric($this->getBonus_caract_by_item("ini"))){
                $bonus_item = $this->getBonus_caract_by_item("ini");
            }

            $total = "";
            $total_int = 0;
            $total_str = ""; $intel_str = ""; $ini_str = "";
            if(is_numeric($this->_ini) && is_numeric($this->getIntel())){
                $total_int += $this->_ini + $this->getIntel() + $bonus_item;
                $total = $total_int;
            } elseif(is_numeric($this->_ini) && !is_numeric($this->getIntel())){
                $total_int += $this->_ini + $bonus_item;
                $total_str = $this->getIntel();
                $intel_str = $this->getIntel();
                $total = $total_int . " + " . $intel_str;
            } elseif(!is_numeric($this->_ini) && is_numeric($this->getIntel())){
                $total_int += $this->getIntel() + $bonus_item;
                $total_str = $this->_ini;
                $ini_str = $this->_ini;
                $total = $total_int . " + " . $ini_str;
            } else {
                $total_int += $bonus_item;
                $total_str = $this->_ini . " + " . $this->getIntel();
                $intel_str = $this->getIntel();
                $ini_str = $this->_ini;
                $total = $total_int . " + " . $total_str;
            }
            
            $data = ""; $detail_on_level = "";
            if(!empty($total_str)){
                $level = Content::getMinMaxFromFormule($this->getLevel());$data = "data-formule='".$total."' data-text='ini'";
                if($level['same'] != true){
                    for($i=$level['min']; $i<=$level['max']; $i++){
                        $total_int_on_level = $total_int;
                        if(!empty($intel_str)){
                            $total_int_on_level += Content::getValueFromFormule($intel_str, $i);
                        }
                        if(!empty($ini_str)){
                            $total_int_on_level += Content::getValueFromFormule($ini_str, $i);
                        }
             $detail_on_level .= "<p class='size-0-7 text-grey-d-2'>lvl ".$i." : <span class='bold'>".$total_int_on_level."</span></p>";
                        $data .= " data-level".$i."='".$total_int_on_level."' ";
                    }
                }
            }

            switch ($format) {
                case Content::DISPLAY_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => ucfirst(get_class($this)),
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "ini",
                            "label" => "Initiative",
                            "placeholder" => "Initiative " . self::VERBAL_NAME_OF_CLASSE,
                            "tooltip" => "Initiative",
                            "value" => $this->_ini,
                            "color" => "ini",
                            "comment" => $this->getIntel(Content::FORMAT_BADGE) . " + ". $bonus_item ." (Bonus d'équipement)",
                            "style" => Style::INPUT_ICON,
                            "size" => Style::SIZE_SM,
                            "icon" => "ini.png",
                            "style_icon" => Style::ICON_MEDIA
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$total} Initiative",
                            "color" => "ini",
                            "tooltip" => "Bonus d'Initiative",
                            "style" => Style::STYLE_BACK,
                            "data" => $data,
                            "id" => "ini"
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "ini.png",
                            "color" => "ini",
                            "tooltip" => "Bonus d'Initiative",
                            "content" => $total,
                            "content_placement" => Style::POSITION_RIGHT
                        ], 
                        write: false); 

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "modules/characteristic",
                        data : [
                            "name" => "Initiative",
                            "value" => $total,
                            "color" => "ini",
                            "icon" => "ini.png",
                            "size" => Style::SIZE_MD,
                            "data" => $data,
                            "tooltips" => "Initiative",
                            "detail" => $this->getIntel(Content::FORMAT_BADGE) . " + " . $this->_ini . " (Bonus) + ".$bonus_item." (équipement)",
                            "detail_on_level" => $detail_on_level
                        ], 
                        write: false);

                default:
                    return $this->_ini;
            }
        }
        public function getInvocation(int $format = Content::FORMAT_BRUT){
            $view = new View();

            $bonus_item = 0;
            if(is_numeric($this->getBonus_caract_by_item("invocation"))){
                $bonus_item = $this->getBonus_caract_by_item("invocation");
            }
            $total = "";
            $total_int = 1;
            $total_str = "";
            if(is_numeric($this->_invocation)){
                $total_int += $this->_invocation + $bonus_item;
                $total = $total_int;
            } else {
                $total_int += $bonus_item;
                $total_str = $this->_invocation;
                $total = $total_int . " + " . $total_str;
            }
            $data = ""; $detail_on_level = "";
            if(!empty($total_str)){
                $level = Content::getMinMaxFromFormule($this->getLevel());$data = "data-formule='".$total."' data-text='invocation'";
                if($level['same'] != true){
                    for($i=$level['min']; $i<=$level['max']; $i++){
                        $total_int_on_level = $total_int;
                        $total_int_on_level += Content::getValueFromFormule($total_str, $i);
             $detail_on_level .= "<p class='size-0-7 text-grey-d-2'>lvl ".$i." : <span class='bold'>".$total_int_on_level."</span></p>";
                        $data .= " data-level".$i."='".$total_int_on_level."' ";
                    }
                }
            }
            
            switch ($format) {
                case Content::DISPLAY_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => ucfirst(get_class($this)),
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "invocation",
                            "label" => "Nb d'invocation",
                            "placeholder" => "Portée " . self::VERBAL_NAME_OF_CLASSE,
                            "tooltip" => "Nb d'invocation",
                            "value" => $this->_invocation,
                            "color" => "invocation",
                            "comment" => "1 + Bonus" . " + ". $bonus_item ." (équipement)",
                            "style" => Style::INPUT_ICON,
                            "size" => Style::SIZE_SM,
                            "icon" => "invocation.png",
                            "style_icon" => Style::ICON_MEDIA
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$total} Invocations",
                            "color" => "invocation",
                            "tooltip" => "Bonus d'Invocations",
                            "style" => Style::STYLE_BACK,
                            "data" => $data,
                            "id" => "invocation"
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "invocation.png",
                            "color" => "invocation",
                            "tooltip" => "Bonus d'Invocations",
                            "content" => $total,
                            "content_placement" => Style::POSITION_RIGHT
                        ], 
                        write: false); 

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "modules/characteristic",
                        data : [
                            "name" => "Invocation",
                            "value" => $total,
                            "color" => "invocation",
                            "icon" => "invocation.png",
                            "size" => Style::SIZE_MD,
                            "data" => $data,
                            "tooltips" => "Nombre de créature invocation maximal",
                            "detail" => "1 + " . $this->_invocation . " (Bonus) + ".$bonus_item." (équipement)",
                            "detail_on_level" => $detail_on_level
                        ], 
                        write: false);

                 default:
                    return $this->_invocation;
            }
        }
        public function getTouch(int $format = Content::FORMAT_BRUT){
            $view = new View();

            $bonus_item = 0;
            if(is_numeric($this->getBonus_caract_by_item("touch"))){
                $bonus_item = $this->getBonus_caract_by_item("touch");
            }
            $total = "";
            $total_int = 0;
            $total_str = "";
            if(is_numeric($this->_touch)){
                $total_int += $this->_touch + $bonus_item;
                $total = $total_int;
            } else {
                $total_int += $bonus_item;
                $total_str = $this->_touch;
                $total = $total_int . " + " . $total_str;
            }
            $data = ""; $detail_on_level = "";
            if(!empty($total_str)){
                $level = Content::getMinMaxFromFormule($this->getLevel());$data = "data-formule='".$total."' data-text='bonus de touche'";
                if($level['same'] != true){
                    for($i=$level['min']; $i<=$level['max']; $i++){
                        $total_int_on_level = $total_int;
                        $total_int_on_level += Content::getValueFromFormule($total_str, $i);
             $detail_on_level .= "<p class='size-0-7 text-grey-d-2'>lvl ".$i." : <span class='bold'>".$total_int_on_level."</span></p>";
                        $data .= " data-level".$i."='".$total_int_on_level."' ";
                    }
                }
            }

            switch ($format) {
                case Content::DISPLAY_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => ucfirst(get_class($this)),
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "touch",
                            "label" => "Bonus de touche",
                            "placeholder" => "Bonus de touche " . self::VERBAL_NAME_OF_CLASSE,
                            "tooltip" => "Bonus de touche",
                            "value" => $this->_touch,
                            "color" => "touch",
                            "comment" => "Bonus" . " + ". $bonus_item ." (équipement)",
                            "style" => Style::INPUT_ICON,
                            "size" => Style::SIZE_SM,
                            "icon" => "touch.png",
                            "style_icon" => Style::ICON_MEDIA
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$total} Touche",
                            "color" => "touch",
                            "tooltip" => "Bonus de touche",
                            "style" => Style::STYLE_BACK,
                            "data" => $data,
                            "id" => "touch"
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "touch.png",
                            "color" => "touch",
                            "tooltip" => "Bonus de touche",
                            "content" => $total,
                            "content_placement" => Style::POSITION_RIGHT
                        ], 
                        write: false); 

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "modules/characteristic",
                        data : [
                            "name" => "Bonus de touche",
                            "value" => $total,
                            "color" => "touch",
                            "icon" => "touch.png",
                            "size" => Style::SIZE_LG,
                            "data" => $data,
                            "tooltips" => "Bonus de Touche",
                            "detail" => $this->_touch . " (Bonus) + ".$bonus_item." (équipement)",
                            "detail_on_level" => $detail_on_level
                        ], 
                        write: false);

                default:
                    return $this->_touch;
            }
        }
        public function getCa(int $format = Content::FORMAT_BRUT){
            $view = new View();

            $bonus_item = 0;
            if(is_numeric($this->getBonus_caract_by_item("ca"))){
                $bonus_item = $this->getBonus_caract_by_item("ca");
            }

            $total = "";
            $total_int = 10;
            $total_str = ""; $vitality_str = ""; $ca_str = "";
            if(is_numeric($this->_ca) && is_numeric($this->getVitality())){
                $total_int += $this->_ca + $this->getVitality() + $bonus_item;
                $total = $total_int;
            } elseif(is_numeric($this->_ca) && !is_numeric($this->getVitality())){
                $total_int += $this->_ca + $bonus_item;
                $total_str = $this->getVitality();
                $vitality_str = $this->getVitality();
                $total = $total_int . " + " . $vitality_str;
            } elseif(!is_numeric($this->_ca) && is_numeric($this->getVitality())){
                $total_int += $this->getVitality() + $bonus_item;
                $total_str = $this->_ca;
                $ca_str = $this->_ca;
                $total = $total_int . " + " . $ca_str;
            } else {
                $total_int += $bonus_item;
                $total_str = $this->_ca . " + " . $this->getVitality();
                $vitality_str = $this->getVitality();
                $ca_str = $this->_ca;
                $total = $total_int . " + " . $total_str;
            }
            
            $data = ""; $detail_on_level = "";
            if(!empty($total_str)){
                $level = Content::getMinMaxFromFormule($this->getLevel());$data = "data-formule='".$total."' data-text='ca'";
                if($level['same'] != true){
                    for($i=$level['min']; $i<=$level['max']; $i++){
                        $total_int_on_level = $total_int;
                        if(!empty($vitality_str)){
                            $total_int_on_level += Content::getValueFromFormule($vitality_str, $i);
                        }
                        if(!empty($ca_str)){
                            $total_int_on_level += Content::getValueFromFormule($ca_str, $i);
                        }
             $detail_on_level .= "<p class='size-0-7 text-grey-d-2'>lvl ".$i." : <span class='bold'>".$total_int_on_level."</span></p>";
                        $data .= " data-level".$i."='".$total_int_on_level."' ";
                    }
                }
            }
            switch ($format) {
                case Content::DISPLAY_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => ucfirst(get_class($this)),
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "ca",
                            "label" => "Bonus de CA",
                            "placeholder" => "Classe d'armure " . self::VERBAL_NAME_OF_CLASSE,
                            "tooltip" => "Bonus de Classe d'armure",
                            "value" => $this->_ca,
                            "color" => "ca-d-4",
                            "comment" => "10 + " . $this->getVitality(Content::FORMAT_BADGE) . " + Bonus + ". $bonus_item ." (équipement)",
                            "style" => Style::INPUT_ICON,
                            "size" => Style::SIZE_SM,
                            "icon" => "ca.png",
                            "style_icon" => Style::ICON_MEDIA
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$total} CA",
                            "color" => "ca-d-4",
                            "tooltip" => "Bonus de Classe d'armure",
                            "style" => Style::STYLE_BACK,
                            "data" => $data,
                            "id" => "ca"
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "ca.png",
                            "color" => "ca-d-4",
                            "tooltip" => "Bonus de Classe d'armure",
                            "content" => $total,
                            "content_placement" => Style::POSITION_RIGHT
                        ], 
                        write: false); 

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "modules/characteristic",
                        data : [
                            "name" => "Classe d'armure",
                            "value" => $total,
                            "color" => "ca",
                            "icon" => "ca.png",
                            "size" => Style::SIZE_MD,
                            "data" => $data,
                            "tooltips" => "Classe d'armure",
                            "detail" => "10 + " . $this->getVitality(Content::FORMAT_BADGE) . " + " . $this->_ca . " (Bonus) + ".$bonus_item." (équipement)",
                            "detail_on_level" => $detail_on_level
                        ], 
                        write: false);
                
                default:
                    return $this->_ca;
            }
        }
        public function getDodge_pa(int $format = Content::FORMAT_BRUT){
            $view = new View();

            $bonus_item = 0;
            if(is_numeric($this->getBonus_caract_by_item("dodge_pa"))){
                $bonus_item = $this->getBonus_caract_by_item("dodge_pa");
            }

            $total = "";
            $total_int = 10;
            $total_str = ""; $sagesse_str = ""; $dodge_pa_str = "";
            if(is_numeric($this->_dodge_pa) && is_numeric($this->getSagesse())){
                $total_int += $this->_dodge_pa + $this->getSagesse() + $bonus_item;
                $total = $total_int;
            } elseif(is_numeric($this->_dodge_pa) && !is_numeric($this->getSagesse())){
                $total_int += $this->_dodge_pa + $bonus_item;
                $total_str = $this->getSagesse();
                $sagesse_str = $this->getSagesse();
                $total = $total_int . " + " . $sagesse_str;
            } elseif(!is_numeric($this->_dodge_pa) && is_numeric($this->getSagesse())){
                $total_int += $this->getSagesse() + $bonus_item;
                $total_str = $this->_dodge_pa;
                $dodge_pa_str = $this->_dodge_pa;
                $total = $total_int . " + " . $dodge_pa_str;
            } else {
                $total_int += $bonus_item;
                $total_str = $this->_dodge_pa . " + " . $this->getSagesse();
                $sagesse_str = $this->getSagesse();
                $dodge_pa_str = $this->_dodge_pa;
                $total = $total_int . " + " . $total_str;
            }
            
            $data = ""; $detail_on_level = "";
            if(!empty($total_str)){
                $level = Content::getMinMaxFromFormule($this->getLevel());$data = "data-formule='".$total."' data-text='esquive pa'";
                if($level['same'] != true){
                    for($i=$level['min']; $i<=$level['max']; $i++){
                        $total_int_on_level = $total_int;
                        if(!empty($sagesse_str)){
                            $total_int_on_level += Content::getValueFromFormule($sagesse_str, $i);
                        }
                        if(!empty($dodge_pa_str)){
                            $total_int_on_level += Content::getValueFromFormule($dodge_pa_str, $i);
                        }
             $detail_on_level .= "<p class='size-0-7 text-grey-d-2'>lvl ".$i." : <span class='bold'>".$total_int_on_level."</span></p>";
                        $data .= " data-level".$i."='".$total_int_on_level."' ";
                    }
                }
            }
            switch ($format) {
                case Content::DISPLAY_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => ucfirst(get_class($this)),
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "dodge_pa",
                            "label" => "Bonus d'esquive PA",
                            "placeholder" => "Bonus d'esquive PA " . self::VERBAL_NAME_OF_CLASSE,
                            "tooltip" => "Bonus d'Esquive PA",
                            "value" => $this->_ca,
                            "color" => "pa",
                            "comment" => "10 + " . $this->getSagesse(Content::FORMAT_BADGE) . " + Bonus + ". $bonus_item ." (équipement)",
                            "style" => Style::INPUT_ICON,
                            "size" => Style::SIZE_SM,
                            "icon" => "dodge_pa.png",
                            "style_icon" => Style::ICON_MEDIA
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$total} Esquive PA",
                            "color" => "pa",
                            "tooltip" => "Bonus d'Esquive PA",
                            "style" => Style::STYLE_BACK,
                            "data" => $data,
                            "id" => "dodge_pa"
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "dodge_pa.png",
                            "color" => "pa",
                            "tooltip" => "Bonus d'Esquive PA",
                            "content" => $total,
                            "content_placement" => Style::POSITION_RIGHT
                        ], 
                        write: false); 

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "modules/characteristic",
                        data : [
                            "name" => "Esquive PA",
                            "value" => $total,
                            "color" => "pa",
                            "icon" => "dodge_pa.png",
                            "size" => Style::SIZE_MD,
                            "data" => $data,
                            "tooltips" => "Esquive du retrait de point d'action",
                            "detail" => "10 + " . $this->getSagesse(Content::FORMAT_BADGE) . " + " . $this->_dodge_pa . " (Bonus) + ".$bonus_item." (équipement)",
                            "detail_on_level" => $detail_on_level
                        ], 
                        write: false);

                default:
                    return $this->_dodge_pa;
            }
        }
        public function getDodge_pm(int $format = Content::FORMAT_BRUT){
            $view = new View();

            $bonus_item = 0;
            if(is_numeric($this->getBonus_caract_by_item("dodge_pm"))){
                $bonus_item = $this->getBonus_caract_by_item("dodge_pm");
            }

            $total = "";
            $total_int = 10;
            $total_str = ""; $sagesse_str = ""; $dodge_pm_str = "";
            if(is_numeric($this->_dodge_pm) && is_numeric($this->getSagesse())){
                $total_int += $this->_dodge_pm + $this->getSagesse() + $bonus_item;
                $total = $total_int;
            } elseif(is_numeric($this->_dodge_pm) && !is_numeric($this->getSagesse())){
                $total_int += $this->_dodge_pm + $bonus_item;
                $total_str = $this->getSagesse();
                $sagesse_str = $this->getSagesse();
                $total = $total_int . " + " . $sagesse_str;
            } elseif(!is_numeric($this->_dodge_pm) && is_numeric($this->getSagesse())){
                $total_int += $this->getSagesse() + $bonus_item;
                $total_str = $this->_dodge_pm;
                $dodge_pm_str = $this->_dodge_pm;
                $total = $total_int . " + " . $dodge_pm_str;
            } else {
                $total_int += $bonus_item;
                $total_str = $this->_dodge_pm . " + " . $this->getSagesse();
                $sagesse_str = $this->getSagesse();
                $dodge_pm_str = $this->_dodge_pm;
                $total = $total_int . " + " . $total_str;
            }
            
            $data = ""; $detail_on_level = "";
            if(!empty($total_str)){
                $level = Content::getMinMaxFromFormule($this->getLevel());$data = "data-formule='".$total."' data-text='esquive pm'";
                if($level['same'] != true){
                    for($i=$level['min']; $i<=$level['max']; $i++){
                        $total_int_on_level = $total_int;
                        if(!empty($sagesse_str)){
                            $total_int_on_level += Content::getValueFromFormule($sagesse_str, $i);
                        }
                        if(!empty($dodge_pm_str)){
                            $total_int_on_level += Content::getValueFromFormule($dodge_pm_str, $i);
                        }
             $detail_on_level .= "<p class='size-0-7 text-grey-d-2'>lvl ".$i." : <span class='bold'>".$total_int_on_level."</span></p>";
                        $data .= " data-level".$i."='".$total_int_on_level."' ";
                    }
                }
            }
            switch ($format) {
                case Content::DISPLAY_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => ucfirst(get_class($this)),
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "dodge_pm",
                            "label" => "Bonus d'esquive PM",
                            "placeholder" => "Bonus d'esquive PM " . self::VERBAL_NAME_OF_CLASSE,
                            "tooltip" => "Bonus d'Esquive PM",
                            "value" => $this->_ca,
                            "color" => "pm",
                            "comment" => "10 + " . $this->getSagesse(Content::FORMAT_BADGE) . " + Bonus + ". $bonus_item ." (équipement)",
                            "style" => Style::INPUT_ICON,
                            "size" => Style::SIZE_SM,
                            "icon" => "dodge_pm.png",
                            "style_icon" => Style::ICON_MEDIA
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$total} Esquive PM",
                            "color" => "pm",
                            "tooltip" => "Bonus d'Esquive PM",
                            "style" => Style::STYLE_BACK,
                            "data" => $data,
                            "id" => "dodge_pm"
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "dodge_pm.png",
                            "color" => "pm",
                            "tooltip" => "Bonus d'Esquive PM",
                            "content" => $total,
                            "content_placement" => Style::POSITION_RIGHT
                        ], 
                        write: false); 

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "modules/characteristic",
                        data : [
                            "name" => "Esquive PM",
                            "value" => $total,
                            "color" => "pm",
                            "icon" => "dodge_pm.png",
                            "size" => Style::SIZE_MD,
                            "data" => $data,
                            "tooltips" => "Esquive du retrait de point de mouvement",
                            "detail" => "10 + " . $this->getSagesse(Content::FORMAT_BADGE) . " + " . $this->_dodge_pm . " (Bonus) + ".$bonus_item." (équipement)",
                            "detail_on_level" => $detail_on_level
                        ], 
                        write: false);

                default:
                    return $this->_dodge_pm;
            }
        }
        public function getFuite(int $format = Content::FORMAT_BRUT){
            $view = new View();

            $bonus_item = 0;
            if(is_numeric($this->getBonus_caract_by_item("fuite"))){
                $bonus_item = $this->getBonus_caract_by_item("fuite");
            }

            $total = "";
            $total_int = 0;
            $total_str = ""; $agi_str = ""; $fuite_str = "";
            if(is_numeric($this->_fuite) && is_numeric($this->getAgi())){
                $total_int += $this->_fuite + $this->getAgi() + $bonus_item;
                $total = $total_int;
            } elseif(is_numeric($this->_fuite) && !is_numeric($this->getAgi())){
                $total_int += $this->_fuite + $bonus_item;
                $total_str = $this->getAgi();
                $agi_str = $this->getAgi();
                $total = $total_int . " + " . $agi_str;
            } elseif(!is_numeric($this->_fuite) && is_numeric($this->getAgi())){
                $total_int += $this->getAgi() + $bonus_item;
                $total_str = $this->_fuite;
                $fuite_str = $this->_fuite;
                $total = $total_int . " + " . $fuite_str;
            } else {
                $total_int += $bonus_item;
                $total_str = $this->_fuite . " + " . $this->getAgi();
                $agi_str = $this->getAgi();
                $fuite_str = $this->_fuite;
                $total = $total_int . " + " . $total_str;
            }
            
            $data = ""; $detail_on_level = "";
            if(!empty($total_str)){
                $level = Content::getMinMaxFromFormule($this->getLevel());$data = "data-formule='".$total."' data-text='fuite'";
                if($level['same'] != true){
                    for($i=$level['min']; $i<=$level['max']; $i++){
                        $total_int_on_level = $total_int;
                        if(!empty($agi_str)){
                            $total_int_on_level += Content::getValueFromFormule($agi_str, $i);
                        }
                        if(!empty($fuite_str)){
                            $total_int_on_level += Content::getValueFromFormule($fuite_str, $i);
                        }
             $detail_on_level .= "<p class='size-0-7 text-grey-d-2'>lvl ".$i." : <span class='bold'>".$total_int_on_level."</span></p>";
                        $data .= " data-level".$i."='".$total_int_on_level."' ";
                    }
                }
            }
            switch ($format) {
                case Content::DISPLAY_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => ucfirst(get_class($this)),
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "fuite",
                            "label" => "Bonus de Fuite",
                            "placeholder" => "Bonus de Fuite " . self::VERBAL_NAME_OF_CLASSE,
                            "tooltip" => "Bonus de Fuite",
                            "value" => $this->_fuite,
                            "color" => "fuite",
                            "comment" => $this->getAgi(Content::FORMAT_BADGE) . " + Bonus + ". $bonus_item ." (équipement)",
                            "style" => Style::INPUT_ICON,
                            "size" => Style::SIZE_SM,
                            "icon" => "fuite.png",
                            "style_icon" => Style::ICON_MEDIA
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$total} Fuite",
                            "color" => "fuite",
                            "tooltip" => "Bonus de Fuite",
                            "style" => Style::STYLE_BACK,
                            "data" => $data,
                            "id" => "fuite"
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "fuite.png",
                            "color" => "fuite",
                            "tooltip" => "Bonus de Fuite",
                            "content" => $total,
                            "content_placement" => Style::POSITION_RIGHT
                        ], 
                        write: false); 

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "modules/characteristic",
                        data : [
                            "name" => "Fuite",
                            "value" => $total,
                            "color" => "fuite",
                            "icon" => "fuite.png",
                            "size" => Style::SIZE_MD,
                            "data" => $data,
                            "detail" => $this->getAgi(Content::FORMAT_BADGE) . " + " . $this->_fuite . " (Bonus) + ".$bonus_item." (équipement)",
                            "detail_on_level" => $detail_on_level
                        ], 
                        write: false);
                
                default:
                    return $this->_fuite;
            }
        }
        public function getTacle(int $format = Content::FORMAT_BRUT){
            $view = new View();

            $bonus_item = 0;
            if(is_numeric($this->getBonus_caract_by_item("tacle"))){
                $bonus_item = $this->getBonus_caract_by_item("tacle");
            }

            $total = "";
            $total_int = 0;
            $total_str = ""; $chance_str = ""; $tacle_str = "";
            if(is_numeric($this->_tacle) && is_numeric($this->getChance())){
                $total_int += $this->_tacle + $this->getChance() + $bonus_item;
                $total = $total_int;
            } elseif(is_numeric($this->_tacle) && !is_numeric($this->getChance())){
                $total_int += $this->_tacle + $bonus_item;
                $total_str = $this->getChance();
                $chance_str = $this->getChance();
                $total = $total_int . " + " . $chance_str;
            } elseif(!is_numeric($this->_tacle) && is_numeric($this->getChance())){
                $total_int += $this->getChance() + $bonus_item;
                $total_str = $this->_tacle;
                $tacle_str = $this->_tacle;
                $total = $total_int . " + " . $tacle_str;
            } else {
                $total_int += $bonus_item;
                $total_str = $this->_tacle . " + " . $this->getChance();
                $chance_str = $this->getChance();
                $tacle_str = $this->_tacle;
                $total = $total_int . " + " . $total_str;
            }
            
            $data = ""; $detail_on_level = "";
            if(!empty($total_str)){
                $level = Content::getMinMaxFromFormule($this->getLevel());$data = "data-formule='".$total."' data-text='tacle'";
                if($level['same'] != true){
                    for($i=$level['min']; $i<=$level['max']; $i++){
                        $total_int_on_level = $total_int;
                        if(!empty($chance_str)){
                            $total_int_on_level += Content::getValueFromFormule($chance_str, $i);
                        }
                        if(!empty($tacle_str)){
                            $total_int_on_level += Content::getValueFromFormule($tacle_str, $i);
                        }
             $detail_on_level .= "<p class='size-0-7 text-grey-d-2'>lvl ".$i." : <span class='bold'>".$total_int_on_level."</span></p>";
                        $data .= " data-level".$i."='".$total_int_on_level."' ";
                    }
                }
            }
            switch ($format) {
                case Content::DISPLAY_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => ucfirst(get_class($this)),
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "tacle",
                            "label" => "Bonus de Tacle",
                            "placeholder" => "Bonus de Tacle " . self::VERBAL_NAME_OF_CLASSE,
                            "tooltip" => "Bonus de Tacle",
                            "value" => $this->_tacle,
                            "color" => "tacle",
                            "comment" => $this->getChance(Content::FORMAT_BADGE) . " + Bonus + ". $bonus_item ." (équipement)",
                            "style" => Style::INPUT_ICON,
                            "size" => Style::SIZE_SM,
                            "icon" => "tacle.png",
                            "style_icon" => Style::ICON_MEDIA
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$total} Tacle",
                            "color" => "tacle",
                            "tooltip" => "Bonus de Tacle",
                            "style" => Style::STYLE_BACK,
                            "data" => $data,
                            "id" => "tacle"
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "tacle.png",
                            "color" => "tacle",
                            "tooltip" => "Bonus de Tacle",
                            "content" => $total,
                            "content_placement" => Style::POSITION_RIGHT
                        ], 
                        write: false); 

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "modules/characteristic",
                        data : [
                            "name" => "Tacle",
                            "value" => $total,
                            "color" => "tacle",
                            "icon" => "tacle.png",
                            "size" => Style::SIZE_MD,
                            "data" => $data,
                            "detail" => $this->getChance(Content::FORMAT_BADGE) . " + " . $this->_tacle . " (Bonus) + ".$bonus_item." (équipement)",
                            "detail_on_level" => $detail_on_level
                        ], 
                        write: false);
                
                default:
                    return $this->_tacle;
            }
        }
        public function getVitality(int $format = Content::FORMAT_BRUT){
            $view = new View();

            $bonus_item = 0;
            if(is_numeric($this->getBonus_caract_by_item("vitality"))){
                $bonus_item = $this->getBonus_caract_by_item("vitality");
            }
            $total = "";
            $total_int = 0;
            $total_str = "";
            if(is_numeric($this->_vitality)){
                $total_int += $this->_vitality + $bonus_item;
                $total = $total_int;
            } else {
                $total_int += $bonus_item;
                $total_str = $this->_vitality;
                $total = $total_int . " + " . $total_str;
            }
            $data = ""; $detail_on_level = "";
            if(!empty($total_str)){
                $level = Content::getMinMaxFromFormule($this->getLevel());$data = "data-formule='".$total."' data-text='vitalité'";
                if($level['same'] != true){
                    for($i=$level['min']; $i<=$level['max']; $i++){
                        $total_int_on_level = $total_int;
                        $total_int_on_level += Content::getValueFromFormule($total_str, $i);
             $detail_on_level .= "<p class='size-0-7 text-grey-d-2'>lvl ".$i." : <span class='bold'>".$total_int_on_level."</span></p>";
                        $data .= " data-level".$i."='".$total_int_on_level."' ";
                    }
                }
            }

            switch ($format) {
                case Content::DISPLAY_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => ucfirst(get_class($this)),
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "vitality",
                            "label" => "Vitalité",
                            "placeholder" => "Vitalité " . self::VERBAL_NAME_OF_CLASSE,
                            "tooltip" => "Vitalité",
                            "value" => $this->_vitality,
                            "color" => "vitality",
                            "comment" => "Bonus de classe" . " + ". $bonus_item ." (équipement)",
                            "style" => Style::INPUT_ICON,
                            "size" => Style::SIZE_SM,
                            "icon" => "vitality.png",
                            "style_icon" => Style::ICON_MEDIA
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$total} Vitalité",
                            "color" => "vitality",
                            "tooltip" => "Vitalité",
                            "style" => Style::STYLE_BACK,
                            "data" => $data,
                            "id" => "vitality"
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "vitality.png",
                            "color" => "vitality",
                            "tooltip" => "Vitalité",
                            "content" => $total,
                            "content_placement" => Style::POSITION_RIGHT
                        ], 
                        write: false); 

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "modules/characteristic",
                        data : [
                            "name" => "Vitalité",
                            "value" => $total,
                            "color" => "vitality",
                            "icon" => "vitality.png",
                            "size" => Style::SIZE_LG,
                            "data" => $data,
                            "detail" => $this->_vitality . " (Bonus de classe) + ".$bonus_item." (équipement)",
                            "detail_on_level" => $detail_on_level
                        ], 
                        write: false);

                default:
                    return $this->_vitality;
            }
        }
        public function getSagesse(int $format = Content::FORMAT_BRUT){
            $view = new View();

            $bonus_item = 0;
            if(is_numeric($this->getBonus_caract_by_item("sagesse"))){
                $bonus_item = $this->getBonus_caract_by_item("sagesse");
            }
            $total = "";
            $total_int = 0;
            $total_str = "";
            if(is_numeric($this->_sagesse)){
                $total_int += $this->_sagesse + $bonus_item;
                $total = $total_int;
            } else {
                $total_int += $bonus_item;
                $total_str = $this->_sagesse;
                $total = $total_int . " + " . $total_str;
            }
            $data = ""; $detail_on_level = "";
            if(!empty($total_str)){
                $level = Content::getMinMaxFromFormule($this->getLevel());$data = "data-formule='".$total."' data-text='sagesse'";
                if($level['same'] != true){
                    for($i=$level['min']; $i<=$level['max']; $i++){
                        $total_int_on_level = $total_int;
                        $total_int_on_level += Content::getValueFromFormule($total_str, $i);
             $detail_on_level .= "<p class='size-0-7 text-grey-d-2'>lvl ".$i." : <span class='bold'>".$total_int_on_level."</span></p>";
                        $data .= " data-level".$i."='".$total_int_on_level."' ";
                    }
                }
            }

            switch ($format) {
                case Content::DISPLAY_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => ucfirst(get_class($this)),
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "sagesse",
                            "label" => "Sagesse",
                            "placeholder" => "Sagesse " . self::VERBAL_NAME_OF_CLASSE,
                            "tooltip" => "Sagesse",
                            "value" => $this->_sagesse,
                            "color" => "sagesse",
                            "comment" => "Bonus de classe" . " + ". $bonus_item ." (équipement)",
                            "style" => Style::INPUT_ICON,
                            "size" => Style::SIZE_SM,
                            "icon" => "sagesse.png",
                            "style_icon" => Style::ICON_MEDIA
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$total} Sagesse",
                            "color" => "sagesse",
                            "tooltip" => "Sagesse",
                            "style" => Style::STYLE_BACK,
                            "data" => $data,
                            "id" => "sagesse"
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "sagesse.png",
                            "color" => "sagesse",
                            "tooltip" => "Sagesse",
                            "content" => $total,
                            "content_placement" => Style::POSITION_RIGHT
                        ], 
                        write: false); 

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "modules/characteristic",
                        data : [
                            "name" => "Sagesse",
                            "value" => $total,
                            "color" => "sagesse",
                            "icon" => "sagesse.png",
                            "size" => Style::SIZE_LG,
                            "data" => $data,
                            "detail" => $this->_sagesse ." (Bonus de classe) + ".$bonus_item." (équipement)",
                            "detail_on_level" => $detail_on_level
                        ], 
                        write: false);

                default:
                    return $this->_sagesse;
            }
        }
        public function getStrong(int $format = Content::FORMAT_BRUT){
            $view = new View();

            $bonus_item = 0;
            if(is_numeric($this->getBonus_caract_by_item("force"))){
                $bonus_item = $this->getBonus_caract_by_item("force");
            }
            $total = "";
            $total_int = 0;
            $total_str = "";
            if(is_numeric($this->_strong)){
                $total_int += $this->_strong + $bonus_item;
                $total = $total_int;
            } else {
                $total_int += $bonus_item;
                $total_str = $this->_strong;
                $total = $total_int . " + " . $total_str;
            }
            $data = ""; $detail_on_level = "";
            if(!empty($total_str)){
                $level = Content::getMinMaxFromFormule($this->getLevel());$data = "data-formule='".$total."' data-text='force'";
                if($level['same'] != true){
                    for($i=$level['min']; $i<=$level['max']; $i++){
                        $total_int_on_level = $total_int;
                        $total_int_on_level += Content::getValueFromFormule($total_str, $i);
             $detail_on_level .= "<p class='size-0-7 text-grey-d-2'>lvl ".$i." : <span class='bold'>".$total_int_on_level."</span></p>";
                        $data .= " data-level".$i."='".$total_int_on_level."' ";
                    }
                }
            }

            switch ($format) {
                case Content::DISPLAY_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => ucfirst(get_class($this)),
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "strong",
                            "label" => "Force",
                            "placeholder" => "Force " . self::VERBAL_NAME_OF_CLASSE,
                            "tooltip" => "Force",
                            "value" => $this->_strong,
                            "color" => "strong",
                            "comment" => "Bonus de classe" . " + ". $bonus_item ." (équipement)",
                            "style" => Style::INPUT_ICON,
                            "size" => Style::SIZE_SM,
                            "icon" => "force.png",
                            "style_icon" => Style::ICON_MEDIA
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$total} Force",
                            "color" => "strong",
                            "tooltip" => "Force",
                            "style" => Style::STYLE_BACK,
                            "data" => $data,
                            "id" => "strong"
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "force.png",
                            "color" => "strong",
                            "tooltip" => "Force",
                            "content" => $total,
                            "content_placement" => Style::POSITION_RIGHT
                        ], 
                        write: false); 

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "modules/characteristic",
                        data : [
                            "name" => "Force",
                            "value" => $total,
                            "color" => "strong",
                            "icon" => "force.png",
                            "size" => Style::SIZE_LG,
                            "data" => $data,
                            "detail" => $this->_strong ." (Bonus de classe) + ".$bonus_item." (équipement)",
                            "detail_on_level" => $detail_on_level
                        ], 
                        write: false);

                default:
                    return $this->_strong;
            }
        }
        public function getIntel(int $format = Content::FORMAT_BRUT){
            $view = new View();

            $bonus_item = 0;
            if(is_numeric($this->getBonus_caract_by_item("intel"))){
                $bonus_item = $this->getBonus_caract_by_item("intel");
            }
            $total = "";
            $total_int = 0;
            $total_str = "";
            if(is_numeric($this->_intel)){
                $total_int += $this->_intel + $bonus_item;
                $total = $total_int;
            } else {
                $total_int += $bonus_item;
                $total_str = $this->_intel;
                $total = $total_int . " + " . $total_str;
            }
            $data = ""; $detail_on_level = "";
            if(!empty($total_str)){
                $level = Content::getMinMaxFromFormule($this->getLevel());$data = "data-formule='".$total."' data-text='intel'";
                if($level['same'] != true){
                    for($i=$level['min']; $i<=$level['max']; $i++){
                        $total_int_on_level = $total_int;
                        $total_int_on_level += Content::getValueFromFormule($total_str, $i);
             $detail_on_level .= "<p class='size-0-7 text-grey-d-2'>lvl ".$i." : <span class='bold'>".$total_int_on_level."</span></p>";
                        $data .= " data-level".$i."='".$total_int_on_level."' ";
                    }
                }
            }

            switch ($format) {
                case Content::DISPLAY_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => ucfirst(get_class($this)),
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "intel",
                            "label" => "Intelligence",
                            "placeholder" => "Intelligence " . self::VERBAL_NAME_OF_CLASSE,
                            "tooltip" => "Intelligence",
                            "value" => $this->_intel,
                            "color" => "intel",
                            "comment" => "Bonus de classe" . " + ". $bonus_item ." (équipement)",
                            "style" => Style::INPUT_ICON,
                            "size" => Style::SIZE_SM,
                            "icon" => "intel.png",
                            "style_icon" => Style::ICON_MEDIA
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$total} Intelligence",
                            "color" => "intel",
                            "tooltip" => "Intelligence",
                            "style" => Style::STYLE_BACK,
                            "data" => $data,
                            "id" => "intel"
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "intel.png",
                            "color" => "intel",
                            "tooltip" => "Intelligence",
                            "content" => $total,
                            "content_placement" => Style::POSITION_RIGHT
                        ], 
                        write: false); 

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "modules/characteristic",
                        data : [
                            "name" => "Intelligence",
                            "value" => $total,
                            "color" => "intel",
                            "icon" => "intel.png",
                            "size" => Style::SIZE_LG,
                            "data" => $data,
                            "detail" => $this->_intel." (Bonus de classe) + ".$bonus_item." (équipement)",
                            "detail_on_level" => $detail_on_level
                        ], 
                        write: false);

                default:
                    return $this->_intel;
            }
        }
        public function getAgi(int $format = Content::FORMAT_BRUT){
            $view = new View();

            $bonus_item = 0;
            if(is_numeric($this->getBonus_caract_by_item("agi"))){
                $bonus_item = $this->getBonus_caract_by_item("agi");
            }
            $total = "";
            $total_int = 0;
            $total_str = "";
            if(is_numeric($this->_agi)){
                $total_int += $this->_agi + $bonus_item;
                $total = $total_int;
            } else {
                $total_int += $bonus_item;
                $total_str = $this->_agi;
                $total = $total_int . " + " . $total_str;
            }
            $data = ""; $detail_on_level = "";
            if(!empty($total_str)){
                $level = Content::getMinMaxFromFormule($this->getLevel());$data = "data-formule='".$total."' data-text='agi'";
                if($level['same'] != true){
                    for($i=$level['min']; $i<=$level['max']; $i++){
                        $total_int_on_level = $total_int;
                        $total_int_on_level += Content::getValueFromFormule($total_str, $i);
             $detail_on_level .= "<p class='size-0-7 text-grey-d-2'>lvl ".$i." : <span class='bold'>".$total_int_on_level."</span></p>";
                        $data .= " data-level".$i."='".$total_int_on_level."' ";
                    }
                }
            }

            switch ($format) {
                case Content::DISPLAY_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => ucfirst(get_class($this)),
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "agi",
                            "label" => "Agilité",
                            "placeholder" => "Agilité " . self::VERBAL_NAME_OF_CLASSE,
                            "tooltip" => "Agilité",
                            "value" => $this->_agi,
                            "color" => "agi",
                            "comment" => "Bonus de classe" . " + ". $bonus_item ." (équipement)",
                            "style" => Style::INPUT_ICON,
                            "size" => Style::SIZE_SM,
                            "icon" => "agi.png",
                            "style_icon" => Style::ICON_MEDIA
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$total} Agilité",
                            "color" => "agi",
                            "tooltip" => "Agilité",
                            "style" => Style::STYLE_BACK,
                            "data" => $data,
                            "id" => "agi"
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "agi.png",
                            "color" => "agi",
                            "tooltip" => "Agilité",
                            "content" => $total,
                            "content_placement" => Style::POSITION_RIGHT
                        ], 
                        write: false); 

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "modules/characteristic",
                        data : [
                            "name" => "Agilité",
                            "value" => $total,
                            "color" => "agi",
                            "icon" => "agi.png",
                            "size" => Style::SIZE_LG,
                            "data" => $data,
                            "detail" => $this->_agi." (Bonus de classe) + ".$bonus_item." (équipement)",
                            "detail_on_level" => $detail_on_level
                        ], 
                        write: false);

                default:
                    return $this->_agi;
            }
        }
        public function getChance(int $format = Content::FORMAT_BRUT){
            $view = new View();

            $bonus_item = 0;
            if(is_numeric($this->getBonus_caract_by_item("chance"))){
                $bonus_item = $this->getBonus_caract_by_item("chance");
            }
            $total = "";
            $total_int = 0;
            $total_str = "";
            if(is_numeric($this->_chance)){
                $total_int += $this->_chance + $bonus_item;
                $total = $total_int;
            } else {
                $total_int += $bonus_item;
                $total_str = $this->_chance;
                $total = $total_int . " + " . $total_str;
            }
            $data = ""; $detail_on_level = "";
            if(!empty($total_str)){
                $level = Content::getMinMaxFromFormule($this->getLevel());$data = "data-formule='".$total."' data-text='chance'";
                if($level['same'] != true){
                    for($i=$level['min']; $i<=$level['max']; $i++){
                        $total_int_on_level = $total_int;
                        $total_int_on_level += Content::getValueFromFormule($total_str, $i);
             $detail_on_level .= "<p class='size-0-7 text-grey-d-2'>lvl ".$i." : <span class='bold'>".$total_int_on_level."</span></p>";
                        $data .= " data-level".$i."='".$total_int_on_level."' ";
                    }
                }
            }

            switch ($format) {
                case Content::DISPLAY_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => ucfirst(get_class($this)),
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "chance",
                            "label" => "Chance",
                            "placeholder" => "Chance " . self::VERBAL_NAME_OF_CLASSE,
                            "tooltip" => "Chance",
                            "value" => $this->_chance,
                            "color" => "chance",
                            "comment" => "Bonus de classe" . " + ". $bonus_item ." (équipement)",
                            "style" => Style::INPUT_ICON,
                            "size" => Style::SIZE_SM,
                            "icon" => "chance.png",
                            "style_icon" => Style::ICON_MEDIA
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$total} Chance",
                            "color" => "chance",
                            "tooltip" => "Chance",
                            "style" => Style::STYLE_BACK,
                            "data" => $data,
                            "id" => "chance"
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "chance.png",
                            "color" => "chance",
                            "tooltip" => "Chance",
                            "content" => $total,
                            "content_placement" => Style::POSITION_RIGHT
                        ], 
                        write: false); 

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "modules/characteristic",
                        data : [
                            "name" => "Chance",
                            "value" => $total,
                            "color" => "chance",
                            "icon" => "chance.png",
                            "size" => Style::SIZE_LG,
                            "data" => $data,
                            "detail" => $this->_chance ." (Bonus de classe) + ".$bonus_item." (équipement)",
                            "detail_on_level" => $detail_on_level
                        ], 
                        write: false);

                default:
                    return $this->_chance;
            }
        }  
        public function getDo_fixe_neutre(int $format = Content::FORMAT_BRUT){
            $view = new View();

            $bonus_item = 0;
            if(is_numeric($this->getBonus_caract_by_item("do_fixe_neutre"))){
                $bonus_item = $this->getBonus_caract_by_item("do_fixe_neutre");
            }
            $total = "";
            $total_int = 0;
            $total_str = "";
            if(is_numeric($this->_do_fixe_neutre)){
                $total_int += $this->_do_fixe_neutre + $bonus_item;
                $total = $total_int;
            } else {
                $total_int += $bonus_item;
                $total_str = $this->_do_fixe_neutre;
                $total = $total_int . " + " . $total_str;
            }
            $data = ""; $detail_on_level = "";
            if(!empty($total_str)){
                $level = Content::getMinMaxFromFormule($this->getLevel());$data = "data-formule='".$total."' data-text='dommage fixe neutre'";
                if($level['same'] != true){
                    for($i=$level['min']; $i<=$level['max']; $i++){
                        $total_int_on_level = $total_int;
                        $total_int_on_level += Content::getValueFromFormule($total_str, $i);
             $detail_on_level .= "<p class='size-0-7 text-grey-d-2'>lvl ".$i." : <span class='bold'>".$total_int_on_level."</span></p>";
                        $data .= " data-level".$i."='".$total_int_on_level."' ";
                    }
                }
            }

            switch ($format) {
                case Content::DISPLAY_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => ucfirst(get_class($this)),
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "do_fixe_neutre",
                            "label" => "Dommage fixe neutre",
                            "placeholder" => "Dommage fixe neutre " . self::VERBAL_NAME_OF_CLASSE,
                            "tooltip" => "Dommage fixe neutre",
                            "value" => $this->_do_fixe_neutre,
                            "color" => "neutre-d-2",
                            "comment" => "Bonus" . " + ". $bonus_item ." (équipement)",
                            "style" => Style::INPUT_ICON,
                            "size" => Style::SIZE_SM,
                            "icon" => "do_fixe_neutre.png",
                            "style_icon" => Style::ICON_MEDIA
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$total} Dommage fixe neutre",
                            "color" => "neutre-d-2",
                            "tooltip" => "Dommage fixe neutre",
                            "style" => Style::STYLE_BACK,
                            "data" => $data,
                            "id" => "do_fixe_neutre"
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "do_fixe_neutre.png",
                            "color" => "neutre-d-2",
                            "tooltip" => "Dommage fixe neutre",
                            "content" => $total,
                            "content_placement" => Style::POSITION_RIGHT
                        ], 
                        write: false); 

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "modules/characteristic",
                        data : [
                            "name" => "Dommage fixe neutre",
                            "value" => $total,
                            "color" => "neutre",
                            "icon" => "do_fixe_neutre.png",
                            "size" => Style::SIZE_SM,
                            "data" => $data,
                            "detail" => $this->_do_fixe_neutre . " (Bonus) + ".$bonus_item." (équipement)",
                            "detail_on_level" => $detail_on_level
                        ], 
                        write: false);

                default:
                    return $this->_do_fixe_neutre;
            }
        }
        public function getDo_fixe_terre(int $format = Content::FORMAT_BRUT){
            $view = new View();

            $bonus_item = 0;
            if(is_numeric($this->getBonus_caract_by_item("do_fixe_terre"))){
                $bonus_item = $this->getBonus_caract_by_item("do_fixe_terre");
            }
            $total = "";
            $total_int = 0;
            $total_str = "";
            if(is_numeric($this->_do_fixe_terre)){
                $total_int += $this->_do_fixe_terre + $bonus_item;
                $total = $total_int;
            } else {
                $total_int += $bonus_item;
                $total_str = $this->_do_fixe_terre;
                $total = $total_int . " + " . $total_str;
            }
            $data = ""; $detail_on_level = "";
            if(!empty($total_str)){
                $level = Content::getMinMaxFromFormule($this->getLevel());$data = "data-formule='".$total."' data-text='dommage fixe terre'";
                if($level['same'] != true){
                    for($i=$level['min']; $i<=$level['max']; $i++){
                        $total_int_on_level = $total_int;
                        $total_int_on_level += Content::getValueFromFormule($total_str, $i);
             $detail_on_level .= "<p class='size-0-7 text-grey-d-2'>lvl ".$i." : <span class='bold'>".$total_int_on_level."</span></p>";
                        $data .= " data-level".$i."='".$total_int_on_level."' ";
                    }
                }
            }

            switch ($format) {
                case Content::DISPLAY_EDITABLE: 
                    return $view->dispatch(     
                        template_name : "input/text",
                        data : [
                            "class_name" => ucfirst(get_class($this)),
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "do_fixe_terre",
                            "label" => "Dommage fixe terre",
                            "placeholder" => "Dommage fixe terre " . self::VERBAL_NAME_OF_CLASSE,
                            "tooltip" => "Dommage fixe terre",
                            "value" => $this->_do_fixe_terre,
                            "color" => "terre-d-2",
                            "comment" => "Bonus" . " + ". $bonus_item ." (équipement)",
                            "style" => Style::INPUT_ICON,
                            "size" => Style::SIZE_SM,
                            "icon" => "do_fixe_terre.png",
                            "style_icon" => Style::ICON_MEDIA
                        ], 
                        write: false);

                case Content::FORMAT_BADGE: 
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$total} Dommage fixe terre",
                            "color" => "terre-d-2",
                            "tooltip" => "Dommage fixe terre",
                            "style" => Style::STYLE_BACK,
                            "data" => $data,
                            "id" => "do_fixe_terre"
                        ], 
                        write: false);

                case Content::FORMAT_ICON: 
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "do_fixe_terre.png",
                            "color" => "terre-d-2",
                            "tooltip" => "Dommage fixe terre",
                            "content" => $total,
                            "content_placement" => Style::POSITION_RIGHT
                        ], 
                        write: false); 

                case Content::FORMAT_VIEW: 
                    return $view->dispatch(
                        template_name : "modules/characteristic",
                        data : [
                            "name" => "Dommage fixe terre",
                            "value" => $total,
                            "color" => "terre",
                            "icon" => "do_fixe_terre.png",
                            "size" => Style::SIZE_SM,
                            "data" => $data,
                            "detail" => $this->_do_fixe_terre . " (Bonus) + ".$bonus_item." (équipement)",
                            "detail_on_level" => $detail_on_level
                        ], 
                        write: false);

                default:
                    return $this->_do_fixe_terre;
            }
        }
        public function getDo_fixe_feu(int $format = Content::FORMAT_BRUT){
            $view = new View();

            $bonus_item = 0;
            if(is_numeric($this->getBonus_caract_by_item("do_fixe_feu"))){
                $bonus_item = $this->getBonus_caract_by_item("do_fixe_feu");
            }
            $total = "";
            $total_int = 0;
            $total_str = "";
            if(is_numeric($this->_do_fixe_feu)){
                $total_int += $this->_do_fixe_feu + $bonus_item;
                $total = $total_int;
            } else {
                $total_int += $bonus_item;
                $total_str = $this->_do_fixe_feu;
                $total = $total_int . " + " . $total_str;
            }
            $data = ""; $detail_on_level = "";
            if(!empty($total_str)){
                $level = Content::getMinMaxFromFormule($this->getLevel());$data = "data-formule='".$total."' data-text='dommage fixe feu'";
                if($level['same'] != true){
                    for($i=$level['min']; $i<=$level['max']; $i++){
                        $total_int_on_level = $total_int;
                        $total_int_on_level += Content::getValueFromFormule($total_str, $i);
             $detail_on_level .= "<p class='size-0-7 text-grey-d-2'>lvl ".$i." : <span class='bold'>".$total_int_on_level."</span></p>";
                        $data .= " data-level".$i."='".$total_int_on_level."' ";
                    }
                }
            }

            switch ($format) {
                case Content::DISPLAY_EDITABLE:
                    return $view->dispatch(     
                        template_name : "input/text",
                        data : [
                            "class_name" => ucfirst(get_class($this)),
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "do_fixe_feu",
                            "label" => "Dommage fixe feu",
                            "placeholder" => "Dommage fixe feu " . self::VERBAL_NAME_OF_CLASSE,
                            "tooltip" => "Dommage fixe feu",
                            "value" => $this->_do_fixe_feu,
                            "color" => "feu-d-2",
                            "comment" => "Bonus" . " + ". $bonus_item ." (équipement)",
                            "style" => Style::INPUT_ICON,
                            "size" => Style::SIZE_SM,
                            "icon" => "do_fixe_feu.png",
                            "style_icon" => Style::ICON_MEDIA
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$total} Dommage fixe feu",
                            "color" => "feu-d-2",
                            "tooltip" => "Dommage fixe feu",
                            "style" => Style::STYLE_BACK,
                            "data" => $data,
                            "id" => "do_fixe_feu"
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "do_fixe_feu.png",
                            "color" => "feu-d-2",
                            "tooltip" => "Dommage fixe feu",
                            "content" => $total,
                            "content_placement" => Style::POSITION_RIGHT
                        ], 
                        write: false); 

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "modules/characteristic",
                        data : [
                            "name" => "Dommage fixe feu",
                            "value" => $total,
                            "color" => "feu",
                            "icon" => "do_fixe_feu.png",
                            "size" => Style::SIZE_SM,
                            "data" => $data,
                            "detail" => $this->_do_fixe_feu . " (Bonus) + ".$bonus_item." (équipement)",
                            "detail_on_level" => $detail_on_level
                        ], 
                        write: false);

                default:
                    return $this->_do_fixe_feu;
            }
        }
        public function getDo_fixe_air(int $format = Content::FORMAT_BRUT){
            $view = new View();

            $bonus_item = 0;
            if(is_numeric($this->getBonus_caract_by_item("do_fixe_air"))){
                $bonus_item = $this->getBonus_caract_by_item("do_fixe_air");
            }
            $total = "";
            $total_int = 0;
            $total_str = "";
            if(is_numeric($this->_do_fixe_air)){
                $total_int += $this->_do_fixe_air + $bonus_item;
                $total = $total_int;
            } else {
                $total_int += $bonus_item;
                $total_str = $this->_do_fixe_air;
                $total = $total_int . " + " . $total_str;
            }
            $data = ""; $detail_on_level = "";
            if(!empty($total_str)){
                $level = Content::getMinMaxFromFormule($this->getLevel());$data = "data-formule='".$total."' data-text='dommage fixe air'";
                if($level['same'] != true){
                    for($i=$level['min']; $i<=$level['max']; $i++){
                        $total_int_on_level = $total_int;
                        $total_int_on_level += Content::getValueFromFormule($total_str, $i);
             $detail_on_level .= "<p class='size-0-7 text-grey-d-2'>lvl ".$i." : <span class='bold'>".$total_int_on_level."</span></p>";
                        $data .= " data-level".$i."='".$total_int_on_level."' ";
                    }
                }
            }

            switch ($format) {
                case Content::DISPLAY_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => ucfirst(get_class($this)),
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "do_fixe_air",
                            "label" => "Dommage fixe air",
                            "placeholder" => "Dommage fixe air " . self::VERBAL_NAME_OF_CLASSE,
                            "tooltip" => "Dommage fixe air",
                            "value" => $this->_do_fixe_air,
                            "color" => "air-d-2",
                            "comment" => "Bonus" . " + ". $bonus_item ." (équipement)",
                            "style" => Style::INPUT_ICON,
                            "size" => Style::SIZE_SM,
                            "icon" => "do_fixe_air.png",
                            "style_icon" => Style::ICON_MEDIA
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$total} Dommage fixe air",
                            "color" => "air-d-2",
                            "tooltip" => "Dommage fixe air",
                            "style" => Style::STYLE_BACK,
                            "data" => $data,
                            "id" => "do_fixe_air"
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "do_fixe_air.png",
                            "color" => "air-d-2",
                            "tooltip" => "Dommage fixe air",
                            "content" => $total,
                            "content_placement" => Style::POSITION_RIGHT
                        ], 
                        write: false); 

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "modules/characteristic",
                        data : [
                            "name" => "Dommage fixe air",
                            "value" => $total,
                            "color" => "air",
                            "icon" => "do_fixe_air.png",
                            "size" => Style::SIZE_SM,
                            "data" => $data,
                            "detail" => $this->_do_fixe_air . " (Bonus) + ".$bonus_item." (équipement)",
                            "detail_on_level" => $detail_on_level
                        ], 
                        write: false);

                default:
                    return $this->_do_fixe_air;
            }
        }
        public function getDo_fixe_eau(int $format = Content::FORMAT_BRUT){
            $view = new View();

            $bonus_item = 0;
            if(is_numeric($this->getBonus_caract_by_item("do_fixe_eau"))){
                $bonus_item = $this->getBonus_caract_by_item("do_fixe_eau");
            }
            $total = "";
            $total_int = 0;
            $total_str = "";
            if(is_numeric($this->_do_fixe_eau)){
                $total_int += $this->_do_fixe_eau + $bonus_item;
                $total = $total_int;
            } else {
                $total_int += $bonus_item;
                $total_str = $this->_do_fixe_eau;
                $total = $total_int . " + " . $total_str;
            }
            $data = ""; $detail_on_level = "";
            if(!empty($total_str)){
                $level = Content::getMinMaxFromFormule($this->getLevel());$data = "data-formule='".$total."' data-text='dommage fixe eau'";
                if($level['same'] != true){
                    for($i=$level['min']; $i<=$level['max']; $i++){
                        $total_int_on_level = $total_int;
                        $total_int_on_level += Content::getValueFromFormule($total_str, $i);
             $detail_on_level .= "<p class='size-0-7 text-grey-d-2'>lvl ".$i." : <span class='bold'>".$total_int_on_level."</span></p>";
                        $data .= " data-level".$i."='".$total_int_on_level."' ";
                    }
                }
            }

            switch ($format) {
                case Content::DISPLAY_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => ucfirst(get_class($this)),
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "do_fixe_eau",
                            "label" => "Dommage fixe eau",
                            "placeholder" => "Dommage fixe eau " . self::VERBAL_NAME_OF_CLASSE,
                            "tooltip" => "Dommage fixe eau",
                            "value" => $this->_do_fixe_eau,
                            "color" => "eau-d-2",
                            "comment" => "Bonus" . " + ". $bonus_item ." (équipement)",
                            "style" => Style::INPUT_ICON,
                            "size" => Style::SIZE_SM,
                            "icon" => "do_fixe_eau.png",
                            "style_icon" => Style::ICON_MEDIA
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$total} Dommage fixe eau",
                            "color" => "eau-d-2",
                            "tooltip" => "Dommage fixe eau",
                            "style" => Style::STYLE_BACK,
                            "data" => $data,
                            "id" => "do_fixe_eau"
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "do_fixe_eau.png",
                            "color" => "eau-d-2",
                            "tooltip" => "Dommage fixe eau",
                            "content" => $total,
                            "content_placement" => Style::POSITION_RIGHT
                        ], 
                        write: false); 

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "modules/characteristic",
                        data : [
                            "name" => "Dommage fixe eau",
                            "value" => $total,
                            "color" => "eau",
                            "icon" => "do_fixe_eau.png",
                            "size" => Style::SIZE_SM,
                            "data" => $data,
                            "detail" => $this->_do_fixe_eau . " (Bonus) + ".$bonus_item." (équipement)",
                            "detail_on_level" => $detail_on_level
                        ], 
                        write: false);

                default:
                    return $this->_do_fixe_eau;
            }
        }
        public function getDo_fixe_multiple(int $format = Content::FORMAT_BRUT){
            $view = new View();

            $bonus_item = 0;
            if(is_numeric($this->getBonus_caract_by_item("do_fixe_multiple"))){
                $bonus_item = $this->getBonus_caract_by_item("do_fixe_multiple");
            }
            $total = "";
            $total_int = 0;
            $total_str = "";
            if(is_numeric($this->_do_fixe_multiple)){
                $total_int += $this->_do_fixe_multiple + $bonus_item;
                $total = $total_int;
            } else {
                $total_int += $bonus_item;
                $total_str = $this->_do_fixe_multiple;
                $total = $total_int . " + " . $total_str;
            }
            $data = ""; $detail_on_level = "";
            if(!empty($total_str)){
                $level = Content::getMinMaxFromFormule($this->getLevel());$data = "data-formule='".$total."' data-text='dommage fixe multiple'";
                if($level['same'] != true){
                    for($i=$level['min']; $i<=$level['max']; $i++){
                        $total_int_on_level = $total_int;
                        $total_int_on_level += Content::getValueFromFormule($total_str, $i);
             $detail_on_level .= "<p class='size-0-7 text-grey-d-2'>lvl ".$i." : <span class='bold'>".$total_int_on_level."</span></p>";
                        $data .= " data-level".$i."='".$total_int_on_level."' ";
                    }
                }
            }

            switch ($format) {
                case Content::DISPLAY_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => ucfirst(get_class($this)),
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "do_fixe_multiple",
                            "label" => "Dommage fixe multiple",
                            "placeholder" => "Dommage fixe multiple " . self::VERBAL_NAME_OF_CLASSE,
                            "tooltip" => "Dommage fixe multiple",
                            "value" => $this->_do_fixe_multiple,
                            "color" => "terre-feu-air-eau-d-4",
                            "comment" => "Bonus" . " + ". $bonus_item ." (équipement)",
                            "style" => Style::INPUT_ICON,
                            "size" => Style::SIZE_SM,
                            "icon" => "do_fixe_multiple.png",
                            "style_icon" => Style::ICON_MEDIA
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$total} Dommage fixe multiple",
                            "color" => "terre-feu-air-eau-d-2",
                            "tooltip" => "Dommage fixe multiple",
                            "style" => Style::STYLE_BACK,
                            "data" => $data,
                            "id" => "do_fixe_multiple"
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "do_fixe_multiple.png",
                            "color" => "multiple-d-2",
                            "tooltip" => "Dommage fixe multiple",
                            "content" => $total,
                            "content_placement" => Style::POSITION_RIGHT
                        ], 
                        write: false); 

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "modules/characteristic",
                        data : [
                            "name" => "Dommage fixe multiple",
                            "value" => $total,
                            "color" => "terre-feu-air-eau",
                            "icon" => "do_fixe_multiple.png",
                            "size" => Style::SIZE_SM,
                            "data" => $data,
                            "detail" => $this->_do_fixe_multiple . " (Bonus) + ".$bonus_item." (équipement)",
                            "detail_on_level" => $detail_on_level
                        ], 
                        write: false);

                default:
                    return $this->_do_fixe_multiple;
            }
        }
        public function getRes_neutre(int $format = Content::FORMAT_BRUT){
            $view = new View();

            $bonus_item = 0;
            if(is_numeric($this->getBonus_caract_by_item("res_neutre"))){
                $bonus_item = $this->getBonus_caract_by_item("res_neutre");
            }
            $total = "";
            $total_int = 0;
            $total_str = "";
            if(is_numeric($this->_res_neutre)){
                $total_int += $this->_res_neutre + $bonus_item;
                $total = $total_int;
            } else {
                $total_int += $bonus_item;
                $total_str = $this->_res_neutre;
                $total = $total_int . " + " . $total_str;
            }
            $data = ""; $detail_on_level = "";
            if(!empty($total_str)){
                $level = Content::getMinMaxFromFormule($this->getLevel());$data = "data-formule='".$total."' data-text='résistance neutre'";
                if($level['same'] != true){
                    for($i=$level['min']; $i<=$level['max']; $i++){
                        $total_int_on_level = $total_int;
                        $total_int_on_level += Content::getValueFromFormule($total_str, $i);
             $detail_on_level .= "<p class='size-0-7 text-grey-d-2'>lvl ".$i." : <span class='bold'>".$total_int_on_level."</span></p>";
                        $data .= " data-level".$i."='".$total_int_on_level."' ";
                    }
                }
            }

            switch ($format) {
                case Content::DISPLAY_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => ucfirst(get_class($this)),
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "res_neutre",
                            "label" => "Résistance neutre",
                            "placeholder" => "Résistance neutre " . self::VERBAL_NAME_OF_CLASSE,
                            "tooltip" => "Résistance neutre",
                            "value" => $this->_res_neutre,
                            "color" => "neutre-d-2",
                            "comment" => "Bonus" . " + ". $bonus_item ." (équipement)",
                            "style" => Style::INPUT_ICON,
                            "size" => Style::SIZE_SM,
                            "icon" => "res_neutre.png",
                            "style_icon" => Style::ICON_MEDIA
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$total} Résistance neutre",
                            "color" => "neutre-d-2",
                            "tooltip" => "Résistance neutre",
                            "style" => Style::STYLE_BACK,
                            "data" => $data,
                            "id" => "res_neutre"
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "res_neutre.png",
                            "color" => "neutre-d-2",
                            "tooltip" => "Résistance neutre",
                            "content" => $total,
                            "content_placement" => Style::POSITION_RIGHT
                        ], 
                        write: false); 

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "modules/characteristic",
                        data : [
                            "name" => "Résistance neutre",
                            "value" => $total,
                            "color" => "neutre",
                            "icon" => "res_neutre.png",
                            "size" => Style::SIZE_SM,
                            "data" => $data,
                            "detail" => $this->_res_neutre . " (Bonus) + ".$bonus_item." (équipement)",
                            "detail_on_level" => $detail_on_level
                        ], 
                        write: false);

                default:
                    return $this->_res_neutre;
            }
        }
        public function getRes_terre(int $format = Content::FORMAT_BRUT){
            $view = new View();

            $bonus_item = 0;
            if(is_numeric($this->getBonus_caract_by_item("res_terre"))){
                $bonus_item = $this->getBonus_caract_by_item("res_terre");
            }
            $total = "";
            $total_int = 0;
            $total_str = "";
            if(is_numeric($this->_res_terre)){
                $total_int += $this->_res_terre + $bonus_item;
                $total = $total_int;
            } else {
                $total_int += $bonus_item;
                $total_str = $this->_res_terre;
                $total = $total_int . " + " . $total_str;
            }
            $data = ""; $detail_on_level = "";
            if(!empty($total_str)){
                $level = Content::getMinMaxFromFormule($this->getLevel());$data = "data-formule='".$total."' data-text='résistance terre'";
                if($level['same'] != true){
                    for($i=$level['min']; $i<=$level['max']; $i++){
                        $total_int_on_level = $total_int;
                        $total_int_on_level += Content::getValueFromFormule($total_str, $i);
             $detail_on_level .= "<p class='size-0-7 text-grey-d-2'>lvl ".$i." : <span class='bold'>".$total_int_on_level."</span></p>";
                        $data .= " data-level".$i."='".$total_int_on_level."' ";
                    }
                }
            }
            
            switch ($format) {
                case Content::DISPLAY_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => ucfirst(get_class($this)),
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "res_terre",
                            "label" => "Résistance terre",
                            "placeholder" => "Résistance terre " . self::VERBAL_NAME_OF_CLASSE,
                            "tooltip" => "Résistance terre",
                            "value" => $this->_res_terre,
                            "color" => "terre",
                            "comment" => "Bonus" . " + ". $bonus_item ." (équipement)",
                            "style" => Style::INPUT_ICON,
                            "size" => Style::SIZE_SM,
                            "icon" => "res_terre.png",
                            "style_icon" => Style::ICON_MEDIA
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$total} Résistance terre",
                            "color" => "terre",
                            "tooltip" => "Résistance terre",
                            "style" => Style::STYLE_BACK,
                            "data" => $data,
                            "id" => "res_terre"
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "res_terre.png",
                            "color" => "terre",
                            "tooltip" => "Résistance terre",
                            "content" => $total,
                            "content_placement" => Style::POSITION_RIGHT
                        ], 
                        write: false); 

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "modules/characteristic",
                        data : [
                            "name" => "Résistance terre",
                            "value" => $total,
                            "color" => "terre",
                            "icon" => "res_terre.png",
                            "size" => Style::SIZE_SM,
                            "data" => $data,
                            "detail" => $this->_res_terre . " (Bonus) + ".$bonus_item." (équipement)",
                            "detail_on_level" => $detail_on_level
                        ], 
                        write: false);

                default:
                    return $this->_res_terre;
            }
        }
        public function getRes_feu(int $format = Content::FORMAT_BRUT){
            $view = new View();

            $bonus_item = 0;
            if(is_numeric($this->getBonus_caract_by_item("res_feu"))){
                $bonus_item = $this->getBonus_caract_by_item("res_feu");
            }
            $total = "";
            $total_int = 0;
            $total_str = "";
            if(is_numeric($this->_res_feu)){
                $total_int += $this->_res_feu + $bonus_item;
                $total = $total_int;
            } else {
                $total_int += $bonus_item;
                $total_str = $this->_res_feu;
                $total = $total_int . " + " . $total_str;
            }
            $data = ""; $detail_on_level = "";
            if(!empty($total_str)){
                $level = Content::getMinMaxFromFormule($this->getLevel());$data = "data-formule='".$total."' data-text='résistance feu'";
                if($level['same'] != true){
                    for($i=$level['min']; $i<=$level['max']; $i++){
                        $total_int_on_level = $total_int;
                        $total_int_on_level += Content::getValueFromFormule($total_str, $i);
             $detail_on_level .= "<p class='size-0-7 text-grey-d-2'>lvl ".$i." : <span class='bold'>".$total_int_on_level."</span></p>";
                        $data .= " data-level".$i."='".$total_int_on_level."' ";
                    }
                }
            }

            switch ($format) {
                case Content::DISPLAY_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => ucfirst(get_class($this)),
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "res_feu",
                            "label" => "Résistance feu",
                            "placeholder" => "Résistance feu " . self::VERBAL_NAME_OF_CLASSE,
                            "tooltip" => "Résistance feu",
                            "value" => $this->_res_feu,
                            "color" => "feu",
                            "comment" => "Bonus" . " + ". $bonus_item ." (équipement)",
                            "style" => Style::INPUT_ICON,
                            "size" => Style::SIZE_SM,
                            "icon" => "res_feu.png",
                            "style_icon" => Style::ICON_MEDIA
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$total} Résistance feu",
                            "color" => "feu",
                            "tooltip" => "Résistance feu",
                            "style" => Style::STYLE_BACK,
                            "data" => $data,
                            "id" => "res_feu"
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "res_feu.png",
                            "color" => "feu",
                            "tooltip" => "Résistance feu",
                            "content" => $total,
                            "content_placement" => Style::POSITION_RIGHT
                        ], 
                        write: false); 

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "modules/characteristic",
                        data : [
                            "name" => "Résistance feu",
                            "value" => $total,
                            "color" => "feu",
                            "icon" => "res_feu.png",
                            "size" => Style::SIZE_SM,
                            "data" => $data,
                            "detail" => $this->_res_feu . " (Bonus) + ".$bonus_item." (équipement)",
                            "detail_on_level" => $detail_on_level
                        ], 
                        write: false);

                default:
                    return $this->_res_feu;
            }
        }
        public function getRes_air(int $format = Content::FORMAT_BRUT){
            $view = new View();

            $bonus_item = 0;
            if(is_numeric($this->getBonus_caract_by_item("res_air"))){
                $bonus_item = $this->getBonus_caract_by_item("res_air");
            }
            $total = "";
            $total_int = 0;
            $total_str = "";
            if(is_numeric($this->_res_air)){
                $total_int += $this->_res_air + $bonus_item;
                $total = $total_int;
            } else {
                $total_int += $bonus_item;
                $total_str = $this->_res_air;
                $total = $total_int . " + " . $total_str;
            }
            $data = ""; $detail_on_level = "";
            if(!empty($total_str)){
                $level = Content::getMinMaxFromFormule($this->getLevel());$data = "data-formule='".$total."' data-text='résistance air'";
                if($level['same'] != true){
                    for($i=$level['min']; $i<=$level['max']; $i++){
                        $total_int_on_level = $total_int;
                        $total_int_on_level += Content::getValueFromFormule($total_str, $i);
             $detail_on_level .= "<p class='size-0-7 text-grey-d-2'>lvl ".$i." : <span class='bold'>".$total_int_on_level."</span></p>";
                        $data .= " data-level".$i."='".$total_int_on_level."' ";
                    }
                }
            }

            switch ($format) {
                case Content::DISPLAY_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => ucfirst(get_class($this)),
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "res_air",
                            "label" => "Résistance air",
                            "placeholder" => "Résistance air " . self::VERBAL_NAME_OF_CLASSE,
                            "tooltip" => "Résistance air",
                            "value" => $this->_res_air,
                            "color" => "air",
                            "comment" => "Bonus" . " + ". $bonus_item ." (équipement)",
                            "style" => Style::INPUT_ICON,
                            "size" => Style::SIZE_SM,
                            "icon" => "res_air.png",
                            "style_icon" => Style::ICON_MEDIA
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$total} Résistance air",
                            "color" => "air",
                            "tooltip" => "Résistance air",
                            "style" => Style::STYLE_BACK,
                            "data" => $data,
                            "id" => "res_air"
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "res_air.png",
                            "color" => "air",
                            "tooltip" => "Résistance air",
                            "content" => $total,
                            "content_placement" => Style::POSITION_RIGHT
                        ], 
                        write: false); 

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "modules/characteristic",
                        data : [
                            "name" => "Résistance air",
                            "value" => $total,
                            "color" => "air",
                            "icon" => "res_air.png",
                            "size" => Style::SIZE_SM,
                            "data" => $data,
                            "detail" => $this->_res_air . " (Bonus) + ".$bonus_item." (équipement)",
                            "detail_on_level" => $detail_on_level
                        ], 
                        write: false);

                default:
                    return $this->_res_air;
            }
        }
        public function getRes_eau(int $format = Content::FORMAT_BRUT){
            $view = new View();

            $bonus_item = 0;
            if(is_numeric($this->getBonus_caract_by_item("res_eau"))){
                $bonus_item = $this->getBonus_caract_by_item("res_eau");
            }
            $total = "";
            $total_int = 0;
            $total_str = "";
            if(is_numeric($this->_res_eau)){
                $total_int += $this->_res_eau + $bonus_item;
                $total = $total_int;
            } else {
                $total_int += $bonus_item;
                $total_str = $this->_res_eau;
                $total = $total_int . " + " . $total_str;
            }
            $data = ""; $detail_on_level = "";
            if(!empty($total_str)){
                $level = Content::getMinMaxFromFormule($this->getLevel());$data = "data-formule='".$total."' data-text='résistance eau'";
                if($level['same'] != true){
                    for($i=$level['min']; $i<=$level['max']; $i++){
                        $total_int_on_level = $total_int;
                        $total_int_on_level += Content::getValueFromFormule($total_str, $i);
             $detail_on_level .= "<p class='size-0-7 text-grey-d-2'>lvl ".$i." : <span class='bold'>".$total_int_on_level."</span></p>";
                        $data .= " data-level".$i."='".$total_int_on_level."' ";
                    }
                }
            }

            switch ($format) {
                case Content::DISPLAY_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => ucfirst(get_class($this)),
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "res_eau",
                            "label" => "Résistance eau",
                            "placeholder" => "Résistance eau " . self::VERBAL_NAME_OF_CLASSE,
                            "tooltip" => "Résistance eau",
                            "value" => $this->_res_eau,
                            "color" => "eau",
                            "comment" => "Bonus" . " + ". $bonus_item ." (équipement)",
                            "style" => Style::INPUT_ICON,
                            "size" => Style::SIZE_SM,
                            "icon" => "res_eau.png",
                            "style_icon" => Style::ICON_MEDIA
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$total} Résistance eau",
                            "color" => "eau",
                            "tooltip" => "Résistance eau",
                            "style" => Style::STYLE_BACK,
                            "data" => $data,
                            "id" => "res_eau"
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "res_eau.png",
                            "color" => "eau",
                            "tooltip" => "Résistance eau",
                            "content" => $total,
                            "content_placement" => Style::POSITION_RIGHT
                        ], 
                        write: false); 

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "modules/characteristic",
                        data : [
                            "name" => "Résistance eau",
                            "value" => $total,
                            "color" => "eau",
                            "icon" => "res_eau.png",
                            "size" => Style::SIZE_SM,
                            "data" => $data,
                            "detail" => $this->_res_eau . " (Bonus) + ".$bonus_item." (équipement)",
                            "detail_on_level" => $detail_on_level
                        ], 
                        write: false);

                default:
                    return $this->_res_eau;
            }
        }
        public function getAcrobatie(int $format = Content::FORMAT_BRUT){
            $view = new View();
            $bonus_item = 0;
            if(is_numeric($this->getBonus_caract_by_item("acrobatie"))){
                $bonus_item = $this->getBonus_caract_by_item("acrobatie");
            }
            $bonus_mastery = 0;
            if(is_numeric($this->getBonus_skill_mastery($this->getAcrobatie_mastery()))){
                $bonus_mastery = $this->getBonus_skill_mastery($this->getAcrobatie_mastery());
            }

            $total = "";
            $total_int = 0;
            $total_str = ""; $agi_str = ""; $acrobatie_str = "";
            if(is_numeric($this->getAcrobatie_bonus()) && is_numeric($this->getAgi())){
                $total_int += $this->getAcrobatie_bonus() + $this->getAgi() + $bonus_item + $bonus_mastery;
                $total = $total_int;
            } elseif(is_numeric($this->getAcrobatie_bonus()) && !is_numeric($this->getAgi())){
                $total_int += $this->getAcrobatie_bonus() + $bonus_item + $bonus_mastery;
                $total_str = $this->getAgi();
                $agi_str = $this->getAgi();
                $total = $total_int . " + " . $agi_str;
            } elseif(!is_numeric($this->getAcrobatie_bonus()) && is_numeric($this->getAgi())){
                $total_int += $this->getAgi() + $bonus_item + $bonus_mastery;
                $total_str = $this->getAcrobatie_bonus();
                $acrobatie_str = $this->getAcrobatie_bonus();
                $total = $total_int . " + " . $acrobatie_str;
            } else {
                $total_int += $bonus_item + $bonus_mastery;
                $total_str = $this->getAcrobatie_bonus() . " + " . $this->getAgi();
                $agi_str = $this->getAgi();
                $acrobatie_str = $this->getAcrobatie_bonus();
                $total = $total_int . " + " . $total_str;
            }
            
            $data = ""; $detail_on_level = "";
            if(!empty($total_str)){
                $level = Content::getMinMaxFromFormule($this->getLevel());$data = "data-formule='".$total."' data-text='acrobatie'";
                if($level['same'] != true){
                    for($i=$level['min']; $i<=$level['max']; $i++){
                        $total_int_on_level = $total_int;
                        if(!empty($agi_str)){
                            $total_int_on_level += Content::getValueFromFormule($agi_str, $i);
                        }
                        if(!empty($acrobatie_str)){
                            $total_int_on_level += Content::getValueFromFormule($acrobatie_str, $i);
                        }
             $detail_on_level .= "<p class='size-0-7 text-grey-d-2'>lvl ".$i." : <span class='bold'>".$total_int_on_level."</span></p>";
                        $data .= " data-level".$i."='".$total_int_on_level."' ";
                    }
                }
            }

            switch ($format) {
                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$total} Acrobatie",
                            "color" => "agi",
                            "tooltip" => "Acrobatie (Agilité + Bonus d'Acrobatie + Équipements + Maîtrise)",
                            "style" => Style::STYLE_BACK,
                            "data" => $data
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    $icon = $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "skill_agi.png",
                            "color" => "agi",
                            "content" => $total,
                            "size" => 10,
                            "content_placement" => Style::POSITION_RIGHT
                        ], 
                        write: false);

                    return $view->dispatch(
                        template_name : "tile",
                        data : [
                            "title" => $icon,
                            "text" => "Acrobatie",
                            "color" => "agi",
                            "size" => Style::SIZE_SM,
                            "tooltip" => "Acrobatie (Agilité + Bonus d'Acrobatie + Équipements + Maîtrise)",
                            "with_border" => false
                        ], 
                        write: false);

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "modules/characteristic",
                        data : [
                            "name" => "Acrobatie",
                            "value" => $total,
                            "color" => "agi",
                            "icon" => "skill_agi.png",
                            "size" => Style::SIZE_SM,
                            "detail" => $this->getAgi(Content::FORMAT_BADGE) . " + " . $this->getBonus_skill_mastery($this->getAcrobatie_mastery(), Content::FORMAT_BADGE, true) . " + " . $this->getAcrobatie_bonus(Content::FORMAT_BADGE) . " + <span class='badge back-grey'>".$bonus_item."</span> (équipement)",
                            "data" => $data,
                            "detail_on_level" => $detail_on_level
                        ], 
                        write: false);
                
                default:
                    return $total;
            }
        }
        public function getAcrobatie_bonus(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::DISPLAY_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => ucfirst(get_class($this)),
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "acrobatie",
                            "label" => "Bonus d'Acrobatie",
                            "placeholder" => "Bonus d'Acrobatie " . self::VERBAL_NAME_OF_CLASSE,
                            "tooltip" => "Bonus d'Acrobatie",
                            "value" => $this->_acrobatie_bonus,
                            "color" => "agi",
                            "style" => Style::INPUT_BASIC
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->_acrobatie_bonus} Bonus d'Acrobatie",
                            "color" => "agi",
                            "tooltip" => "Bonus d'Acrobatie",
                            "style" => Style::STYLE_OUTLINE
                        ], 
                        write: false);

                default:
                    return $this->_acrobatie_bonus;
            }
        }
        public function getAcrobatie_mastery(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    $items = [];
                    foreach (self::SKILL_MATERY_LIST as $name => $mastery) {
                
                        switch ($mastery) {
                            case self::SKILL_EXPERTISE: $color = "blue"; break;
                            case self::SKILL_MASTERY: $color = "blue-grey"; break;
                            default: $color = "grey"; break;
                        }
                        $items[] = [
                            "display" => "<span class='badge back-".$color."-d-2'>" .ucfirst($name)."</span>",
                            "onclick" => ucfirst(get_class($this)).".update('{$this->getUniqid()}', {$mastery}, 'acrobatie_mastery', ".Controller::IS_VALUE.")"
                        ];
                    }
                    return $view->dispatch(
                        template_name : "dropdown",
                        data : [
                            "label" => $this->getAcrobatie_mastery(Content::FORMAT_BADGE),
                            "tooltip" => "Maîtrise de la compétence",
                            "items" => $items,
                            "id" => "acrobatie_mastery_{$this->getUniqid()}",
                        ], 
                        write: false);
    
                case Content::FORMAT_BADGE:
                    if(in_array($this->_acrobatie_mastery, self::SKILL_MATERY_LIST)){
                        switch ($this->_acrobatie_mastery) {
                            case self::SKILL_EXPERTISE: $color = "blue"; break;
                            case self::SKILL_MASTERY: $color = "blue-grey"; break;
                            default: $color = "grey"; break;
                        }

                        return $view->dispatch(
                            template_name : "badge",
                            data : [
                                "content" => ucfirst(array_search($this->_acrobatie_mastery, self::SKILL_MATERY_LIST)),
                                "color" =>  $color. "-d-2",
                                "tooltip" => "Maîtrise de la compétence",
                                "style" => Style::STYLE_BACK
                            ], 
                            write: false);

                    } else  {
                        return "";
                    }

                default:
                    return $this->_acrobatie_mastery;
            }
        }  
        public function getDiscretion(int $format = Content::FORMAT_BRUT){
            $view = new View();
            $bonus_item = 0;
            if(is_numeric($this->getBonus_caract_by_item("discretion"))){
                $bonus_item = $this->getBonus_caract_by_item("discretion");
            }
            $bonus_mastery = 0;
            if(is_numeric($this->getBonus_skill_mastery($this->getDiscretion_mastery()))){
                $bonus_mastery = $this->getBonus_skill_mastery($this->getDiscretion_mastery());
            }

            $total = "";
            $total_int = 0;
            $total_str = ""; $agi_str = ""; $discretion_str = "";

            if(is_numeric($this->getDiscretion_bonus()) && is_numeric($this->getAgi())){
                $total_int += $this->getDiscretion_bonus() + $this->getAgi() + $bonus_item + $bonus_mastery;
                $total = $total_int;
            } elseif(is_numeric($this->getDiscretion_bonus()) && !is_numeric($this->getAgi())){
                $total_int += $this->getDiscretion_bonus() + $bonus_item + $bonus_mastery;
                $total_str = $this->getAgi();
                $agi_str = $this->getAgi();
                $total = $total_int . " + " . $agi_str;
            } elseif(!is_numeric($this->getDiscretion_bonus()) && is_numeric($this->getAgi())){
                $total_int += $this->getAgi() + $bonus_item + $bonus_mastery;
                $total_str = $this->getDiscretion_bonus();
                $discretion_str = $this->getDiscretion_bonus();
                $total = $total_int . " + " . $discretion_str;
            } else {
                $total_int += $bonus_item + $bonus_mastery;
                $total_str = $this->getDiscretion_bonus() . " + " . $this->getAgi();
                $agi_str = $this->getAgi();
                $discretion_str = $this->getDiscretion_bonus();
                $total = $total_int . " + " . $total_str;
            }

            $data = ""; $detail_on_level = "";
            if(!empty($total_str)){
                $level = Content::getMinMaxFromFormule($this->getLevel());$data = "data-formule='".$total."' data-text='discrétion'";
                if($level['same'] != true){
                    for($i=$level['min']; $i<=$level['max']; $i++){
                        $total_int_on_level = $total_int;
                        if(!empty($agi_str)){
                            $total_int_on_level += Content::getValueFromFormule($agi_str, $i);
                        }
                        if(!empty($discretion_str)){
                            $total_int_on_level += Content::getValueFromFormule($discretion_str, $i);
                        }
             $detail_on_level .= "<p class='size-0-7 text-grey-d-2'>lvl ".$i." : <span class='bold'>".$total_int_on_level."</span></p>";
                        $data .= " data-level".$i."='".$total_int_on_level."' ";
                    }
                }
            }

            switch ($format) {
                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$total} Discrétion",
                            "color" => "agi",
                            "tooltip" => "Discrétion (Agilité + Bonus de Discrétion + Équipements + Maîtrise)",
                            "style" => Style::STYLE_BACK,
                            "data" => $data
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    $icon = $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "skill_agi.png",
                            "color" => "agi",
                            "content" => $total,
                            "size" => 10,
                            "content_placement" => Style::POSITION_RIGHT
                        ], 
                        write: false);

                    return $view->dispatch(
                        template_name : "tile",
                        data : [
                            "title" => $icon,
                            "text" => "Discrétion",
                            "color" => "agi",
                            "size" => Style::SIZE_SM,
                            "tooltip" => "Discrétion (Agilité + Bonus de Discrétion + Équipements + Maîtrise)",
                            "with_border" => false
                        ], 
                        write: false);

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "modules/characteristic",
                        data : [
                            "name" => "Discrétion",
                            "value" => $total,
                            "color" => "agi",
                            "icon" => "skill_agi.png",
                            "size" => Style::SIZE_SM,
                            "detail" => $this->getAgi(Content::FORMAT_BADGE) . " + " . $this->getBonus_skill_mastery($this->getDiscretion_mastery(), Content::FORMAT_BADGE, true) . " + " . $this->getDiscretion_bonus(Content::FORMAT_BADGE) . " + <span class='badge back-grey'>".$bonus_item."</span> (équipement)",
                            "data" => $data,
                            "detail_on_level" => $detail_on_level
                        ], 
                        write: false);
                
                default:
                    return $total;
            }
        }
        public function getDiscretion_bonus(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::DISPLAY_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => ucfirst(get_class($this)),
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "discretion",
                            "label" => "Bonus de discrétion",
                            "placeholder" => "Bonus de discrétion " . self::VERBAL_NAME_OF_CLASSE,
                            "tooltip" => "Bonus de discrétion",
                            "value" => $this->_discretion_bonus,
                            "color" => "agi",
                            "style" => Style::INPUT_BASIC
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->_discretion_bonus} Bonus de discrétion",
                            "color" => "agi",
                            "tooltip" => "Bonus de discrétion",
                            "style" => Style::STYLE_OUTLINE
                        ], 
                        write: false);

                default:
                    return $this->_discretion_bonus;
            }
        }
        public function getDiscretion_mastery(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    $items = [];
                    foreach (self::SKILL_MATERY_LIST as $name => $mastery) {
                
                        switch ($mastery) {
                            case self::SKILL_EXPERTISE: $color = "blue"; break;
                            case self::SKILL_MASTERY: $color = "blue-grey"; break;
                            default: $color = "grey"; break;
                        }
                        $items[] = [
                            "display" => "<span class='badge back-".$color."-d-2'>" .ucfirst($name)."</span>",
                            "onclick" => ucfirst(get_class($this)).".update('{$this->getUniqid()}', {$mastery}, 'discretion_mastery', ".Controller::IS_VALUE.")"
                        ];
                    }
                    return $view->dispatch(
                        template_name : "dropdown",
                        data : [
                            "label" => $this->getDiscretion_mastery(Content::FORMAT_BADGE),
                            "tooltip" => "Maîtrise de la compétence",
                            "items" => $items,
                            "id" => "discretion_mastery_{$this->getUniqid()}",
                        ], 
                        write: false);
    
                case Content::FORMAT_BADGE:
                    if(in_array($this->_discretion_mastery, self::SKILL_MATERY_LIST)){
                        switch ($this->_discretion_mastery) {
                            case self::SKILL_EXPERTISE: $color = "blue"; break;
                            case self::SKILL_MASTERY: $color = "blue-grey"; break;
                            default: $color = "grey"; break;
                        }

                        return $view->dispatch(
                            template_name : "badge",
                            data : [
                                "content" => ucfirst(array_search($this->_discretion_mastery, self::SKILL_MATERY_LIST)),
                                "color" =>  $color. "-d-2",
                                "tooltip" => "Maîtrise de la compétence",
                                "style" => Style::STYLE_BACK
                            ], 
                            write: false);

                    } else  {
                        return "";
                    }

                default:
                    return $this->_discretion_mastery;
            }
        }
        public function getEscamotage(int $format = Content::FORMAT_BRUT){
            $view = new View();
            $bonus_item = 0;
            if(is_numeric($this->getBonus_caract_by_item("escamotage"))){
                $bonus_item = $this->getBonus_caract_by_item("escamotage");
            }
            $bonus_mastery = 0;
            if(is_numeric($this->getBonus_skill_mastery($this->getEscamotage_mastery()))){
                $bonus_mastery = $this->getBonus_skill_mastery($this->getEscamotage_mastery());
            }

            $total = "";
            $total_int = 0;
            $total_str = ""; $agi_str = ""; $escamotage_str = "";

            if(is_numeric($this->getEscamotage_bonus()) && is_numeric($this->getAgi())){
                $total_int += $this->getEscamotage_bonus() + $this->getAgi() + $bonus_item + $bonus_mastery;
                $total = $total_int;
            } elseif(is_numeric($this->getEscamotage_bonus()) && !is_numeric($this->getAgi())){
                $total_int += $this->getEscamotage_bonus() + $bonus_item + $bonus_mastery;
                $total_str = $this->getAgi();
                $agi_str = $this->getAgi();
                $total = $total_int . " + " . $agi_str;
            } elseif(!is_numeric($this->getEscamotage_bonus()) && is_numeric($this->getAgi())){
                $total_int += $this->getAgi() + $bonus_item + $bonus_mastery;
                $total_str = $this->getEscamotage_bonus();
                $escamotage_str = $this->getEscamotage_bonus();
                $total = $total_int . " + " . $escamotage_str;
            } else {
                $total_int += $bonus_item + $bonus_mastery;
                $total_str = $this->getEscamotage_bonus() . " + " . $this->getAgi();
                $agi_str = $this->getAgi();
                $escamotage_str = $this->getEscamotage_bonus();
                $total = $total_int . " + " . $total_str;
            }

            $data = ""; $detail_on_level = "";
            if(!empty($total_str)){
                $level = Content::getMinMaxFromFormule($this->getLevel());$data = "data-formule='".$total."' data-text='escamotage'";
                if($level['same'] != true){ 
                    for($i=$level['min']; $i<=$level['max']; $i++){
                        $total_int_on_level = $total_int;
                        if(!empty($agi_str)){
                            $total_int_on_level += Content::getValueFromFormule($agi_str, $i);
                        }
                        if(!empty($escamotage_str)){
                            $total_int_on_level += Content::getValueFromFormule($escamotage_str, $i);
                        }
             $detail_on_level .= "<p class='size-0-7 text-grey-d-2'>lvl ".$i." : <span class='bold'>".$total_int_on_level."</span></p>";
                        $data .= " data-level".$i."='".$total_int_on_level."' ";
                    }
                }
            }

            switch ($format) {
                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$total} Escamotage",
                            "color" => "agi",
                            "tooltip" => "Escamotage (Agilité + Bonus d'Escamotage + Équipements + Maîtrise)",
                            "style" => Style::STYLE_BACK,
                            "data" => $data
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    $icon = $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "skill_agi.png",
                            "color" => "agi",
                            "content" => $total,
                            "size" => 10,
                            "content_placement" => Style::POSITION_RIGHT
                        ], 
                        write: false);

                    return $view->dispatch(
                        template_name : "tile",
                        data : [
                            "title" => $icon,
                            "text" => "Escamotage",
                            "color" => "agi",
                            "size" => Style::SIZE_SM,
                            "tooltip" => "Escamotage (Agilité + Bonus d'Escamotage + Équipements + Maîtrise)",
                            "with_border" => false
                        ], 
                        write: false);

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "modules/characteristic",
                        data : [
                            "name" => "Escamotage",
                            "value" => $total,
                            "color" => "agi",
                            "icon" => "skill_agi.png",
                            "size" => Style::SIZE_SM,
                            "detail" => $this->getAgi(Content::FORMAT_BADGE) . " + " . $this->getBonus_skill_mastery($this->getEscamotage_mastery(), Content::FORMAT_BADGE, true) . " + " . $this->getEscamotage_bonus(Content::FORMAT_BADGE) . " + <span class='badge back-grey'>".$bonus_item."</span> (équipement)",
                            "data" => $data,
                            "detail_on_level" => $detail_on_level
                        ], 
                        write: false);
                
                default:
                    return $total;
            }
        }
        public function getEscamotage_bonus(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::DISPLAY_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [ 
                            "class_name" => ucfirst(get_class($this)),
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "escamotage",
                            "label" => "Bonus d'Escamotage",
                            "placeholder" => "Bonus d'Escamotage " . self::VERBAL_NAME_OF_CLASSE,
                            "tooltip" => "Bonus d'Escamotage",
                            "value" => $this->_escamotage_bonus,
                            "color" => "agi",
                            "style" => Style::INPUT_BASIC
                        ], 
                        write: false); 

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [ 
                            "content" => "{$this->_escamotage_bonus} Bonus d'Escamotage",
                            "color" => "agi",
                            "tooltip" => "Bonus d'Escamotage",
                            "style" => Style::STYLE_OUTLINE
                        ], 
                        write: false); 

                default:
                    return $this->_escamotage_bonus;
            }
        }
        public function getEscamotage_mastery(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    $items = [];
                    foreach (self::SKILL_MATERY_LIST as $name => $mastery) {
                
                        switch ($mastery) {
                            case self::SKILL_EXPERTISE: $color = "blue"; break;
                            case self::SKILL_MASTERY: $color = "blue-grey"; break;
                            default: $color = "grey"; break;
                        }
                        $items[] = [
                            "display" => "<span class='badge back-".$color."-d-2'>" .ucfirst($name)."</span>",
                            "onclick" => ucfirst(get_class($this)).".update('{$this->getUniqid()}', {$mastery}, 'escamotage_mastery', ".Controller::IS_VALUE.")"
                        ];
                    }
                    return $view->dispatch(
                        template_name : "dropdown",
                        data : [
                            "label" => $this->getEscamotage_mastery(Content::FORMAT_BADGE),
                            "tooltip" => "Maîtrise de la compétence",
                            "items" => $items,
                            "id" => "escamotage_mastery_{$this->getUniqid()}",
                        ], 
                        write: false);
    
                case Content::FORMAT_BADGE:
                    if(in_array($this->_escamotage_mastery, self::SKILL_MATERY_LIST)){
                        switch ($this->_escamotage_mastery) {
                            case self::SKILL_EXPERTISE: $color = "blue"; break;
                            case self::SKILL_MASTERY: $color = "blue-grey"; break;
                            default: $color = "grey"; break;
                        }

                        return $view->dispatch(
                            template_name : "badge",
                            data : [
                                "content" => ucfirst(array_search($this->_escamotage_mastery, self::SKILL_MATERY_LIST)),
                                "color" =>  $color. "-d-2",
                                "tooltip" => "Maîtrise de la compétence",
                                "style" => Style::STYLE_BACK
                            ], 
                            write: false);

                    } else  {
                        return "";
                    }

                default:
                    return $this->_escamotage_mastery;
            }
        }
        public function getAthletisme(int $format = Content::FORMAT_BRUT){
            $view = new View();
            $bonus_item = 0;
            if(is_numeric($this->getBonus_caract_by_item("athletisme"))){
                $bonus_item = $this->getBonus_caract_by_item("athletisme");
            }
            $bonus_mastery = 0;
            if(is_numeric($this->getBonus_skill_mastery($this->getAthletisme_mastery()))){
                $bonus_mastery = $this->getBonus_skill_mastery($this->getAthletisme_mastery());
            }

            $total = "";
            $total_int = 0;
            $total_str = ""; $strong_str = ""; $athletisme_str = "";

            if(is_numeric($this->getAthletisme_bonus()) && is_numeric($this->getStrong())){
                $total_int += $this->getAthletisme_bonus() + $this->getStrong() + $bonus_item + $bonus_mastery;
                $total = $total_int;
            } elseif(is_numeric($this->getAthletisme_bonus()) && !is_numeric($this->getStrong())){
                $total_int += $this->getAthletisme_bonus() + $bonus_item + $bonus_mastery;
                $total_str = $this->getStrong();
                $strong_str = $this->getStrong();
                $total = $total_int . " + " . $strong_str;
            } elseif(!is_numeric($this->getAthletisme_bonus()) && is_numeric($this->getStrong())){
                $total_int += $this->getStrong() + $bonus_item + $bonus_mastery;
                $total_str = $this->getAthletisme_bonus();
                $athletisme_str = $this->getAthletisme_bonus();
                $total = $total_int . " + " . $athletisme_str;
            } else {
                $total_int += $bonus_item + $bonus_mastery;
                $total_str = $this->getAthletisme_bonus() . " + " . $this->getStrong();
                $strong_str = $this->getStrong();
                $athletisme_str = $this->getAthletisme_bonus();
                $total = $total_int . " + " . $total_str;
            }

            $data = ""; $detail_on_level = "";
            if(!empty($total_str)){
                $level = Content::getMinMaxFromFormule($this->getLevel());$data = "data-formule='".$total."' data-text='athlétisme'";
                if($level['same'] != true){  
                    for($i=$level['min']; $i<=$level['max']; $i++){
                        $total_int_on_level = $total_int;
                        if(!empty($strong_str)){
                            $total_int_on_level += Content::getValueFromFormule($strong_str, $i);
                        }
                        if(!empty($athletisme_str)){
                            $total_int_on_level += Content::getValueFromFormule($athletisme_str, $i);
                        }
             $detail_on_level .= "<p class='size-0-7 text-grey-d-2'>lvl ".$i." : <span class='bold'>".$total_int_on_level."</span></p>";
                        $data .= " data-level".$i."='".$total_int_on_level."' ";
                    }
                }
            }

            switch ($format) {
                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$total} Athlétisme",
                            "color" => "strong",
                            "tooltip" => "Athlétisme (Force + Bonus d'Athlétisme + Équipements + Maîtrise)",
                            "style" => Style::STYLE_BACK,
                            "data" => $data
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    $icon = $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "skill_force.png",
                            "color" => "strong",
                            "content" => $total,
                            "size" => 10,
                            "content_placement" => Style::POSITION_RIGHT
                        ], 
                        write: false);

                    return $view->dispatch(
                        template_name : "tile",
                        data : [
                            "title" => $icon,
                            "text" => "Athlétisme",
                            "color" => "strong",
                            "size" => Style::SIZE_SM,
                            "tooltip" => "Athlétisme (Force + Bonus d'Athlétisme + Équipements + Maîtrise)",
                            "with_border" => false
                        ], 
                        write: false);

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "modules/characteristic",
                        data : [
                            "name" => "Athlétisme",
                            "value" => $total,
                            "color" => "strong",
                            "icon" => "skill_force.png",
                            "size" => Style::SIZE_SM,
                            "detail" => $this->getStrong(Content::FORMAT_BADGE) . " + " . $this->getBonus_skill_mastery($this->getAthletisme_mastery(), Content::FORMAT_BADGE, true) . " + " . $this->getAthletisme_bonus(Content::FORMAT_BADGE) . " + <span class='badge back-grey'>".$bonus_item."</span> (équipement)",
                            "data" => $data,
                            "detail_on_level" => $detail_on_level
                        ], 
                        write: false);
                
                default:
                    return $total;
            }
        }        
        public function getAthletisme_bonus(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::DISPLAY_EDITABLE: 
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [ 
                            "class_name" => ucfirst(get_class($this)),
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "athletisme",
                            "label" => "Bonus d'Athletisme",
                            "placeholder" => "Bonus d'Athletisme " . self::VERBAL_NAME_OF_CLASSE,
                            "tooltip" => "Bonus d'Athletisme",
                            "value" => $this->_athletisme_bonus,
                            "color" => "strong",
                            "style" => Style::INPUT_BASIC
                        ], 
                        write: false); 

                case Content::FORMAT_BADGE: 
                    return $view->dispatch(
                        template_name : "badge",
                        data : [ 
                            "content" => "{$this->_athletisme_bonus} Bonus d'Athletisme",
                            "color" => "strong",
                            "tooltip" => "Bonus d'Athletisme",
                            "style" => Style::STYLE_OUTLINE
                        ], 
                        write: false); 

                default:
                    return $this->_athletisme_bonus;
            }
        }
        public function getAthletisme_mastery(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    $items = [];
                    foreach (self::SKILL_MATERY_LIST as $name => $mastery) {
                
                        switch ($mastery) {
                            case self::SKILL_EXPERTISE: $color = "blue"; break;
                            case self::SKILL_MASTERY: $color = "blue-grey"; break;
                            default: $color = "grey"; break;
                        }
                        $items[] = [
                            "display" => "<span class='badge back-".$color."-d-2'>" .ucfirst($name)."</span>",
                            "onclick" => ucfirst(get_class($this)).".update('{$this->getUniqid()}', {$mastery}, 'athletisme_mastery', ".Controller::IS_VALUE.")"
                        ];
                    }
                    return $view->dispatch(
                        template_name : "dropdown",
                        data : [
                            "label" => $this->getAthletisme_mastery(Content::FORMAT_BADGE),
                            "tooltip" => "Maîtrise de la compétence",
                            "items" => $items,
                            "id" => "athletisme_mastery_{$this->getUniqid()}",
                        ], 
                        write: false);
    
                case Content::FORMAT_BADGE:
                    if(in_array($this->_athletisme_mastery, self::SKILL_MATERY_LIST)){
                        switch ($this->_athletisme_mastery) {
                            case self::SKILL_EXPERTISE: $color = "blue"; break;
                            case self::SKILL_MASTERY: $color = "blue-grey"; break;
                            default: $color = "grey"; break;
                        }

                        return $view->dispatch(
                            template_name : "badge",
                            data : [
                                "content" => ucfirst(array_search($this->_athletisme_mastery, self::SKILL_MATERY_LIST)),
                                "color" =>  $color. "-d-2",
                                "tooltip" => "Maîtrise de la compétence",
                                "style" => Style::STYLE_BACK
                            ], 
                            write: false);

                    } else  {
                        return "";
                    }

                default:
                    return $this->_athletisme_mastery;
            }
        }
        public function getIntimidation(int $format = Content::FORMAT_BRUT){
            $view = new View();
            $bonus_item = 0;
            if(is_numeric($this->getBonus_caract_by_item("intimidation"))){
                $bonus_item = $this->getBonus_caract_by_item("intimidation");
            }
            $bonus_mastery = 0;
            if(is_numeric($this->getBonus_skill_mastery($this->getIntimidation_mastery()))){
                $bonus_mastery = $this->getBonus_skill_mastery($this->getIntimidation_mastery());
            }

            $total = "";
            $total_int = 0;
            $total_str = ""; $strong_str = ""; $intimidation_str = "";

            if(is_numeric($this->getIntimidation_bonus()) && is_numeric($this->getStrong())){
                $total_int += $this->getIntimidation_bonus() + $this->getStrong() + $bonus_item + $bonus_mastery;
                $total = $total_int;
            } elseif(is_numeric($this->getIntimidation_bonus()) && !is_numeric($this->getStrong())){
                $total_int += $this->getIntimidation_bonus() + $bonus_item + $bonus_mastery;
                $total_str = $this->getStrong();
                $strong_str = $this->getStrong();
                $total = $total_int . " + " . $strong_str;
            } elseif(!is_numeric($this->getIntimidation_bonus()) && is_numeric($this->getStrong())){
                $total_int += $this->getStrong() + $bonus_item + $bonus_mastery;
                $total_str = $this->getIntimidation_bonus();
                $intimidation_str = $this->getIntimidation_bonus();
                $total = $total_int . " + " . $intimidation_str;
            } else {
                $total_int += $bonus_item + $bonus_mastery;
                $total_str = $this->getIntimidation_bonus() . " + " . $this->getStrong();
                $strong_str = $this->getStrong();
                $intimidation_str = $this->getIntimidation_bonus();
                $total = $total_int . " + " . $total_str;
            }

            $data = ""; $detail_on_level = "";
            if(!empty($total_str)){
                $level = Content::getMinMaxFromFormule($this->getLevel());$data = "data-formule='".$total."' data-text='intimidation'";
                if($level['same'] != true){ 
                    for($i=$level['min']; $i<=$level['max']; $i++){
                        $total_int_on_level = $total_int;
                        if(!empty($strong_str)){
                            $total_int_on_level += Content::getValueFromFormule($strong_str, $i);
                        }
                        if(!empty($intimidation_str)){
                            $total_int_on_level += Content::getValueFromFormule($intimidation_str, $i);
                        }
             $detail_on_level .= "<p class='size-0-7 text-grey-d-2'>lvl ".$i." : <span class='bold'>".$total_int_on_level."</span></p>";
                        $data .= " data-level".$i."='".$total_int_on_level."' ";
                    }
                }
            }

            switch ($format) {
                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$total} Intimidation",
                            "color" => "strong",
                            "tooltip" => "Intimidation (Force + Bonus d'Intimidation + Équipements + Maîtrise)",
                            "style" => Style::STYLE_BACK,
                            "data" => $data
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    $icon = $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "skill_force.png",
                            "color" => "strong",
                            "content" => $total,
                            "size" => 10,
                            "content_placement" => Style::POSITION_RIGHT
                        ], 
                        write: false);

                    return $view->dispatch(
                        template_name : "tile",
                        data : [
                            "title" => $icon,
                            "text" => "Intimidation",
                            "color" => "strong",
                            "size" => Style::SIZE_SM,
                            "tooltip" => "Intimidation (Force + Bonus d'Intimidation + Équipements + Maîtrise)",
                            "with_border" => false
                        ], 
                        write: false);

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "modules/characteristic",
                        data : [
                            "name" => "Intimidation",
                            "value" => $total,
                            "color" => "strong",
                            "icon" => "skill_force.png",
                            "size" => Style::SIZE_SM,
                            "detail" => $this->getStrong(Content::FORMAT_BADGE) . " + " . $this->getBonus_skill_mastery($this->getIntimidation_mastery(), Content::FORMAT_BADGE, true) . " + " . $this->getIntimidation_bonus(Content::FORMAT_BADGE) . " + <span class='badge back-grey'>".$bonus_item."</span> (équipement)",
                            "data" => $data,
                            "detail_on_level" => $detail_on_level
                        ], 
                        write: false);
                
                default:
                    return $total;
            }
        } 
        public function getIntimidation_bonus(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::DISPLAY_EDITABLE: 
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [ 
                            "class_name" => ucfirst(get_class($this)),
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "intimidation",
                            "label" => "Bonus d'Intimidation",
                            "placeholder" => "Bonus d'Intimidation " . self::VERBAL_NAME_OF_CLASSE,
                            "tooltip" => "Bonus d'Intimidation",
                            "value" => $this->_intimidation_bonus,
                            "color" => "strong",
                            "style" => Style::INPUT_BASIC
                        ], 
                        write: false); 

                case Content::FORMAT_BADGE: 
                    return $view->dispatch(
                        template_name : "badge",
                        data : [ 
                            "content" => "{$this->_intimidation_bonus} Bonus d'Intimidation",
                            "color" => "strong",
                            "tooltip" => "Bonus d'Intimidation",
                            "style" => Style::STYLE_OUTLINE
                        ], 
                        write: false); 

                default:
                    return $this->_intimidation_bonus;
            }
        }
        public function getIntimidation_mastery(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    $items = [];
                    foreach (self::SKILL_MATERY_LIST as $name => $mastery) {
                
                        switch ($mastery) {
                            case self::SKILL_EXPERTISE: $color = "blue"; break;
                            case self::SKILL_MASTERY: $color = "blue-grey"; break;
                            default: $color = "grey"; break;
                        }
                        $items[] = [
                            "display" => "<span class='badge back-".$color."-d-2'>" .ucfirst($name)."</span>",
                            "onclick" => ucfirst(get_class($this)).".update('{$this->getUniqid()}', {$mastery}, 'intimidation_mastery', ".Controller::IS_VALUE.")"
                        ];
                    }
                    return $view->dispatch(
                        template_name : "dropdown",
                        data : [
                            "label" => $this->getIntimidation_mastery(Content::FORMAT_BADGE),
                            "tooltip" => "Maîtrise de la compétence",
                            "items" => $items,
                            "id" => "intimidation_mastery_{$this->getUniqid()}",
                        ], 
                        write: false);
    
                case Content::FORMAT_BADGE:
                    if(in_array($this->_intimidation_mastery, self::SKILL_MATERY_LIST)){
                        switch ($this->_intimidation_mastery) {
                            case self::SKILL_EXPERTISE: $color = "blue"; break;
                            case self::SKILL_MASTERY: $color = "blue-grey"; break;
                            default: $color = "grey"; break;
                        }

                        return $view->dispatch(
                            template_name : "badge",
                            data : [
                                "content" => ucfirst(array_search($this->_intimidation_mastery, self::SKILL_MATERY_LIST)),
                                "color" =>  $color. "-d-2",
                                "tooltip" => "Maîtrise de la compétence",
                                "style" => Style::STYLE_BACK
                            ], 
                            write: false);

                    } else  {
                        return "";
                    }

                default:
                    return $this->_intimidation_mastery;
            }
        }
        public function getArcane(int $format = Content::FORMAT_BRUT){
            $view = new View();
            $bonus_item = 0;
            if(is_numeric($this->getBonus_caract_by_item("arcane"))){
                $bonus_item = $this->getBonus_caract_by_item("arcane");
            }
            $bonus_mastery = 0;
            if(is_numeric($this->getBonus_skill_mastery($this->getArcane_mastery()))){
                $bonus_mastery = $this->getBonus_skill_mastery($this->getArcane_mastery());
            }

            $total = "";
            $total_int = 0;
            $total_str = ""; $intel_str = ""; $arcane_str = "";

            if(is_numeric($this->getArcane_bonus()) && is_numeric($this->getIntel())){
                $total_int += $this->getArcane_bonus() + $this->getIntel() + $bonus_item + $bonus_mastery;
                $total = $total_int;
            } elseif(is_numeric($this->getArcane_bonus()) && !is_numeric($this->getIntel())){
                $total_int += $this->getArcane_bonus() + $bonus_item + $bonus_mastery;
                $total_str = $this->getIntel();
                $intel_str = $this->getIntel();
                $total = $total_int . " + " . $intel_str;
            } elseif(!is_numeric($this->getArcane_bonus()) && is_numeric($this->getIntel())){
                $total_int += $this->getIntel() + $bonus_item + $bonus_mastery;
                $total_str = $this->getArcane_bonus();
                $arcane_str = $this->getArcane_bonus();
                $total = $total_int . " + " . $arcane_str;
            } else {
                $total_int += $bonus_item + $bonus_mastery;
                $total_str = $this->getArcane_bonus() . " + " . $this->getIntel();
                $intel_str = $this->getIntel();
                $arcane_str = $this->getArcane_bonus();
                $total = $total_int . " + " . $total_str;
            }

            $data = ""; $detail_on_level = "";
            if(!empty($total_str)){
                $level = Content::getMinMaxFromFormule($this->getLevel());$data = "data-formule='".$total."' data-text='arcane'";
                if($level['same'] != true){ 
                    for($i=$level['min']; $i<=$level['max']; $i++){
                        $total_int_on_level = $total_int;
                        if(!empty($intel_str)){
                            $total_int_on_level += Content::getValueFromFormule($intel_str, $i);
                        }
                        if(!empty($arcane_str)){
                            $total_int_on_level += Content::getValueFromFormule($arcane_str, $i);
                        }
             $detail_on_level .= "<p class='size-0-7 text-grey-d-2'>lvl ".$i." : <span class='bold'>".$total_int_on_level."</span></p>";
                        $data .= " data-level".$i."='".$total_int_on_level."' ";
                    }
                }
            }

            switch ($format) {
                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$total} Arcane",
                            "color" => "intel",
                            "tooltip" => "Arcane (Force + Bonus d'Arcane + Équipements + Maîtrise)",
                            "style" => Style::STYLE_BACK,
                            "data" => $data
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    $icon = $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "skill_intel.png",
                            "color" => "intel",
                            "content" => $total,
                            "size" => 10,
                            "content_placement" => Style::POSITION_RIGHT
                        ], 
                        write: false);

                    return $view->dispatch(
                        template_name : "tile",
                        data : [
                            "title" => $icon,
                            "text" => "Arcane",
                            "color" => "intel",
                            "size" => Style::SIZE_SM,
                            "tooltip" => "Arcane (Force + Bonus d'Arcane + Équipements + Maîtrise)",
                            "with_border" => false
                        ], 
                        write: false);

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "modules/characteristic",
                        data : [
                            "name" => "Arcane",
                            "value" => $total,
                            "color" => "intel",
                            "icon" => "skill_intel.png",
                            "size" => Style::SIZE_SM,
                            "detail" => $this->getStrong(Content::FORMAT_BADGE) . " + " . $this->getBonus_skill_mastery($this->getArcane_mastery(), Content::FORMAT_BADGE, true) . " + " . $this->getArcane_bonus(Content::FORMAT_BADGE) . " + <span class='badge back-grey'>".$bonus_item."</span> (équipement)",
                            "data" => $data,
                            "detail_on_level" => $detail_on_level
                        ], 
                        write: false);
                
                default:
                    return $total;
            }
        } 
        public function getArcane_bonus(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::DISPLAY_EDITABLE: 
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [ 
                            "class_name" => ucfirst(get_class($this)),
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "arcane",
                            "label" => "Bonus d'Arcane",
                            "placeholder" => "Bonus d'Arcane " . self::VERBAL_NAME_OF_CLASSE,
                            "tooltip" => "Bonus d'Arcane",
                            "value" => $this->_arcane_bonus,
                            "color" => "intel",
                            "style" => Style::INPUT_BASIC
                        ], 
                        write: false); 

                case Content::FORMAT_BADGE: 
                    return $view->dispatch(
                        template_name : "badge",
                        data : [ 
                            "content" => "{$this->_arcane_bonus} Bonus d'Arcane",
                            "color" => "intel",
                            "tooltip" => "Bonus d'Arcane",
                            "style" => Style::STYLE_OUTLINE
                        ], 
                        write: false); 

                default:
                    return $this->_arcane_bonus;
            }
        }
        public function getArcane_mastery(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    $items = [];
                    foreach (self::SKILL_MATERY_LIST as $name => $mastery) {
                
                        switch ($mastery) {
                            case self::SKILL_EXPERTISE: $color = "blue"; break;
                            case self::SKILL_MASTERY: $color = "blue-grey"; break;
                            default: $color = "grey"; break;
                        }
                        $items[] = [
                            "display" => "<span class='badge back-".$color."-d-2'>" .ucfirst($name)."</span>",
                            "onclick" => ucfirst(get_class($this)).".update('{$this->getUniqid()}', {$mastery}, 'arcane_mastery', ".Controller::IS_VALUE.")"
                        ];
                    }
                    return $view->dispatch(
                        template_name : "dropdown",
                        data : [
                            "label" => $this->getArcane_mastery(Content::FORMAT_BADGE),
                            "tooltip" => "Maîtrise de la compétence",
                            "items" => $items,
                            "id" => "arcane_mastery_{$this->getUniqid()}",
                        ], 
                        write: false);
    
                case Content::FORMAT_BADGE:
                    if(in_array($this->_arcane_mastery, self::SKILL_MATERY_LIST)){
                        switch ($this->_arcane_mastery) {
                            case self::SKILL_EXPERTISE: $color = "blue"; break;
                            case self::SKILL_MASTERY: $color = "blue-grey"; break;
                            default: $color = "grey"; break;
                        }

                        return $view->dispatch(
                            template_name : "badge",
                            data : [
                                "content" => ucfirst(array_search($this->_arcane_mastery, self::SKILL_MATERY_LIST)),
                                "color" =>  $color. "-d-2",
                                "tooltip" => "Maîtrise de la compétence",
                                "style" => Style::STYLE_BACK
                            ], 
                            write: false);

                    } else  {
                        return "";
                    }

                default:
                    return $this->_arcane_mastery;
            }
        }
        public function getHistoire(int $format = Content::FORMAT_BRUT){
            $view = new View();
            $bonus_item = 0;
            if(is_numeric($this->getBonus_caract_by_item("histoire"))){
                $bonus_item = $this->getBonus_caract_by_item("histoire");
            }
            $bonus_mastery = 0;
            if(is_numeric($this->getBonus_skill_mastery($this->getHistoire_mastery()))){
                $bonus_mastery = $this->getBonus_skill_mastery($this->getHistoire_mastery());
            }

            $total = "";
            $total_int = 0;
            $total_str = ""; $intel_str = ""; $histoire_str = "";

            if(is_numeric($this->getHistoire_bonus()) && is_numeric($this->getIntel())){
                $total_int += $this->getHistoire_bonus() + $this->getIntel() + $bonus_item + $bonus_mastery;
                $total = $total_int;
            } elseif(is_numeric($this->getHistoire_bonus()) && !is_numeric($this->getIntel())){
                $total_int += $this->getHistoire_bonus() + $bonus_item + $bonus_mastery;
                $total_str = $this->getIntel();
                $intel_str = $this->getIntel();
                $total = $total_int . " + " . $intel_str;
            } elseif(!is_numeric($this->getHistoire_bonus()) && is_numeric($this->getIntel())){
                $total_int += $this->getIntel() + $bonus_item + $bonus_mastery;
                $total_str = $this->getHistoire_bonus();
                $histoire_str = $this->getHistoire_bonus();
                $total = $total_int . " + " . $histoire_str;
            } else {
                $total_int += $bonus_item + $bonus_mastery;
                $total_str = $this->getHistoire_bonus() . " + " . $this->getIntel();
                $intel_str = $this->getIntel();
                $histoire_str = $this->getHistoire_bonus();
                $total = $total_int . " + " . $total_str;
            }

            $data = ""; $detail_on_level = "";
            if(!empty($total_str)){
                $level = Content::getMinMaxFromFormule($this->getLevel());$data = "data-formule='".$total."' data-text='histoire'";
                if($level['same'] != true){ 
                    for($i=$level['min']; $i<=$level['max']; $i++){
                        $total_int_on_level = $total_int;
                        if(!empty($intel_str)){
                            $total_int_on_level += Content::getValueFromFormule($intel_str, $i);
                        }
                        if(!empty($histoire_str)){
                            $total_int_on_level += Content::getValueFromFormule($histoire_str, $i);
                        }
             $detail_on_level .= "<p class='size-0-7 text-grey-d-2'>lvl ".$i." : <span class='bold'>".$total_int_on_level."</span></p>";
                        $data .= " data-level".$i."='".$total_int_on_level."' ";
                    }
                }
            }

            switch ($format) {
                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$total} Histoire",
                            "color" => "intel",
                            "tooltip" => "Histoire (Intel + Bonus d'Histoire + Équipements + Maîtrise)",
                            "style" => Style::STYLE_BACK,
                            "data" => $data
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    $icon = $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "skill_intel.png",
                            "color" => "intel",
                            "content" => $total,
                            "size" => 10,
                            "content_placement" => Style::POSITION_RIGHT
                        ], 
                        write: false);

                    return $view->dispatch(
                        template_name : "tile",
                        data : [
                            "title" => $icon,
                            "text" => "Histoire",
                            "color" => "intel",
                            "size" => Style::SIZE_SM,
                            "tooltip" => "Histoire (Intel + Bonus d'Histoire + Équipements + Maîtrise)",
                            "with_border" => false
                        ], 
                        write: false);

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "modules/characteristic",
                        data : [
                            "name" => "Histoire",
                            "value" => $total,
                            "color" => "intel",
                            "icon" => "skill_intel.png",
                            "size" => Style::SIZE_SM,
                            "detail" => $this->getIntel(Content::FORMAT_BADGE) . " + " . $this->getBonus_skill_mastery($this->getHistoire_mastery(), Content::FORMAT_BADGE, true) . " + " . $this->getHistoire_bonus(Content::FORMAT_BADGE) . " + <span class='badge back-grey'>".$bonus_item."</span> (équipement)",
                            "data" => $data,
                            "detail_on_level" => $detail_on_level
                        ], 
                        write: false);
                
                default:
                    return $total;
            }
        }
        public function getHistoire_bonus(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::DISPLAY_EDITABLE: 
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [ 
                            "class_name" => ucfirst(get_class($this)),
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "histoire",
                            "label" => "Bonus d'Histoire",
                            "placeholder" => "Bonus d'Histoire " . self::VERBAL_NAME_OF_CLASSE,
                            "tooltip" => "Bonus d'Histoire",
                            "value" => $this->_histoire_bonus,
                            "color" => "intel",
                            "style" => Style::INPUT_BASIC
                        ], 
                        write: false); 

                case Content::FORMAT_BADGE: 
                    return $view->dispatch(
                        template_name : "badge",
                        data : [ 
                            "content" => "{$this->_histoire_bonus} Bonus d'Histoire",
                            "color" => "intel",
                            "tooltip" => "Bonus d'Histoire",
                            "style" => Style::STYLE_OUTLINE
                        ], 
                        write: false); 

                default:
                    return $this->_histoire_bonus;
            }
        }
        public function getHistoire_mastery(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    $items = [];
                    foreach (self::SKILL_MATERY_LIST as $name => $mastery) {
                
                        switch ($mastery) {
                            case self::SKILL_EXPERTISE: $color = "blue"; break;
                            case self::SKILL_MASTERY: $color = "blue-grey"; break;
                            default: $color = "grey"; break;
                        }
                        $items[] = [
                            "display" => "<span class='badge back-".$color."-d-2'>" .ucfirst($name)."</span>",
                            "onclick" => ucfirst(get_class($this)).".update('{$this->getUniqid()}', {$mastery}, 'histoire_mastery', ".Controller::IS_VALUE.")"
                        ];
                    }
                    return $view->dispatch(
                        template_name : "dropdown",
                        data : [
                            "label" => $this->getHistoire_mastery(Content::FORMAT_BADGE),
                            "tooltip" => "Maîtrise de la compétence",
                            "items" => $items,
                            "id" => "histoire_mastery_{$this->getUniqid()}",
                        ], 
                        write: false);
    
                case Content::FORMAT_BADGE:
                    if(in_array($this->_histoire_mastery, self::SKILL_MATERY_LIST)){
                        switch ($this->_histoire_mastery) {
                            case self::SKILL_EXPERTISE: $color = "blue"; break;
                            case self::SKILL_MASTERY: $color = "blue-grey"; break;
                            default: $color = "grey"; break;
                        }

                        return $view->dispatch(
                            template_name : "badge",
                            data : [
                                "content" => ucfirst(array_search($this->_histoire_mastery, self::SKILL_MATERY_LIST)),
                                "color" =>  $color. "-d-2",
                                "tooltip" => "Maîtrise de la compétence",
                                "style" => Style::STYLE_BACK
                            ], 
                            write: false);

                    } else  {
                        return "";
                    }

                default:
                    return $this->_histoire_mastery;
            }
        }
        public function getInvestigation(int $format = Content::FORMAT_BRUT){
            $view = new View();
            $bonus_item = 0;
            if(is_numeric($this->getBonus_caract_by_item("investigation"))){
                $bonus_item = $this->getBonus_caract_by_item("investigation");
            }
            $bonus_mastery = 0;
            if(is_numeric($this->getBonus_skill_mastery($this->getInvestigation_mastery()))){
                $bonus_mastery = $this->getBonus_skill_mastery($this->getInvestigation_mastery());
            }

            $total = "";
            $total_int = 0;
            $total_str = ""; $intel_str = ""; $investigation_str = "";

            if(is_numeric($this->getInvestigation_bonus()) && is_numeric($this->getIntel())){
                $total_int += $this->getInvestigation_bonus() + $this->getIntel() + $bonus_item + $bonus_mastery;
                $total = $total_int;
            } elseif(is_numeric($this->getInvestigation_bonus()) && !is_numeric($this->getIntel())){
                $total_int += $this->getInvestigation_bonus() + $bonus_item + $bonus_mastery;
                $total_str = $this->getIntel();
                $intel_str = $this->getIntel();
                $total = $total_int . " + " . $intel_str;
            } elseif(!is_numeric($this->getInvestigation_bonus()) && is_numeric($this->getIntel())){
                $total_int += $this->getIntel() + $bonus_item + $bonus_mastery;
                $total_str = $this->getInvestigation_bonus();
                $investigation_str = $this->getInvestigation_bonus();
                $total = $total_int . " + " . $investigation_str;
            } else {
                $total_int += $bonus_item + $bonus_mastery;
                $total_str = $this->getInvestigation_bonus() . " + " . $this->getIntel();
                $intel_str = $this->getIntel();
                $investigation_str = $this->getInvestigation_bonus();
                $total = $total_int . " + " . $total_str;
            }

            $data = ""; $detail_on_level = "";
            if(!empty($total_str)){
                $level = Content::getMinMaxFromFormule($this->getLevel());$data = "data-formule='".$total."' data-text='investigation'";
                if($level['same'] != true){ 
                    for($i=$level['min']; $i<=$level['max']; $i++){
                        $total_int_on_level = $total_int;
                        if(!empty($intel_str)){
                            $total_int_on_level += Content::getValueFromFormule($intel_str, $i);
                        }
                        if(!empty($investigation_str)){
                            $total_int_on_level += Content::getValueFromFormule($investigation_str, $i);
                        }
             $detail_on_level .= "<p class='size-0-7 text-grey-d-2'>lvl ".$i." : <span class='bold'>".$total_int_on_level."</span></p>";
                        $data .= " data-level".$i."='".$total_int_on_level."' ";
                    }
                }
            }

            switch ($format) {
                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$total} Investigation",
                            "color" => "intel",
                            "tooltip" => "Investigation (Intel + Bonus d'Investigation + Équipements + Maîtrise)",
                            "style" => Style::STYLE_BACK,
                            "data" => $data
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    $icon = $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "skill_intel.png",
                            "color" => "intel",
                            "content" => $total,
                            "size" => 10,
                            "content_placement" => Style::POSITION_RIGHT
                        ], 
                        write: false);

                    return $view->dispatch(
                        template_name : "tile",
                        data : [
                            "title" => $icon,
                            "text" => "Investigation",
                            "color" => "intel",
                            "size" => Style::SIZE_SM,
                            "tooltip" => "Investigation (Intel + Bonus d'Investigation + Équipements + Maîtrise)",
                            "with_border" => false
                        ], 
                        write: false);

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "modules/characteristic",
                        data : [
                            "name" => "Investigation",
                            "value" => $total,
                            "color" => "intel",
                            "icon" => "skill_intel.png",
                            "size" => Style::SIZE_SM,
                            "detail" => $this->getIntel(Content::FORMAT_BADGE) . " + " . $this->getBonus_skill_mastery($this->getInvestigation_mastery(), Content::FORMAT_BADGE, true) . " + " . $this->getInvestigation_bonus(Content::FORMAT_BADGE) . " + <span class='badge back-grey'>".$bonus_item."</span> (équipement)",
                            "data" => $data,
                            "detail_on_level" => $detail_on_level
                        ], 
                        write: false);
                
                default:
                    return $total;
            }
        }
        public function getInvestigation_bonus(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::DISPLAY_EDITABLE: 
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [ 
                            "class_name" => ucfirst(get_class($this)),
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "investigation",
                            "label" => "Bonus d'Investigation",
                            "placeholder" => "Bonus d'Investigation " . self::VERBAL_NAME_OF_CLASSE,
                            "tooltip" => "Bonus d'Investigation",
                            "value" => $this->_investigation_bonus,
                            "color" => "intel",
                            "style" => Style::INPUT_BASIC
                        ], 
                        write: false); 

                case Content::FORMAT_BADGE: 
                    return $view->dispatch(
                        template_name : "badge",
                        data : [ 
                            "content" => "{$this->_investigation_bonus} Bonus d'Investigation",
                            "color" => "intel",
                            "tooltip" => "Bonus d'Investigation",
                            "style" => Style::STYLE_OUTLINE
                        ], 
                        write: false); 

                default:
                    return $this->_investigation_bonus;
            }
        }
        public function getInvestigation_mastery(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    $items = [];
                    foreach (self::SKILL_MATERY_LIST as $name => $mastery) {
                
                        switch ($mastery) {
                            case self::SKILL_EXPERTISE: $color = "blue"; break;
                            case self::SKILL_MASTERY: $color = "blue-grey"; break;
                            default: $color = "grey"; break;
                        }
                        $items[] = [
                            "display" => "<span class='badge back-".$color."-d-2'>" .ucfirst($name)."</span>",
                            "onclick" => ucfirst(get_class($this)).".update('{$this->getUniqid()}', {$mastery}, 'investigation_mastery', ".Controller::IS_VALUE.")"
                        ];
                    }
                    return $view->dispatch(
                        template_name : "dropdown",
                        data : [
                            "label" => $this->getInvestigation_mastery(Content::FORMAT_BADGE),
                            "tooltip" => "Maîtrise de la compétence",
                            "items" => $items,
                            "id" => "investigation_mastery_{$this->getUniqid()}",
                        ], 
                        write: false);
    
                case Content::FORMAT_BADGE:
                    if(in_array($this->_investigation_mastery, self::SKILL_MATERY_LIST)){
                        switch ($this->_investigation_mastery) {
                            case self::SKILL_EXPERTISE: $color = "blue"; break;
                            case self::SKILL_MASTERY: $color = "blue-grey"; break;
                            default: $color = "grey"; break;
                        }

                        return $view->dispatch(
                            template_name : "badge",
                            data : [
                                "content" => ucfirst(array_search($this->_investigation_mastery, self::SKILL_MATERY_LIST)),
                                "color" =>  $color. "-d-2",
                                "tooltip" => "Maîtrise de la compétence",
                                "style" => Style::STYLE_BACK
                            ], 
                            write: false);

                    } else  {
                        return "";
                    }

                default:
                    return $this->_investigation_mastery;
            }
        }
        public function getReligion(int $format = Content::FORMAT_BRUT){
            $view = new View();
            $bonus_item = 0;
            if(is_numeric($this->getBonus_caract_by_item("religion"))){
                $bonus_item = $this->getBonus_caract_by_item("religion");
            }
            $bonus_mastery = 0;
            if(is_numeric($this->getBonus_skill_mastery($this->getReligion_mastery()))){
                $bonus_mastery = $this->getBonus_skill_mastery($this->getReligion_mastery());
            }

            $total = "";
            $total_int = 0;
            $total_str = ""; $intel_str = ""; $religion_str = "";

            if(is_numeric($this->getReligion_bonus()) && is_numeric($this->getIntel())){
                $total_int += $this->getReligion_bonus() + $this->getIntel() + $bonus_item + $bonus_mastery;
                $total = $total_int;
            } elseif(is_numeric($this->getReligion_bonus()) && !is_numeric($this->getIntel())){
                $total_int += $this->getReligion_bonus() + $bonus_item + $bonus_mastery;
                $total_str = $this->getIntel();
                $intel_str = $this->getIntel();
                $total = $total_int . " + " . $intel_str;
            } elseif(!is_numeric($this->getReligion_bonus()) && is_numeric($this->getIntel())){
                $total_int += $this->getIntel() + $bonus_item + $bonus_mastery;
                $total_str = $this->getReligion_bonus();
                $religion_str = $this->getReligion_bonus();
                $total = $total_int . " + " . $religion_str;
            } else {
                $total_int += $bonus_item + $bonus_mastery;
                $total_str = $this->getReligion_bonus() . " + " . $this->getIntel();
                $intel_str = $this->getIntel();
                $religion_str = $this->getReligion_bonus();
                $total = $total_int . " + " . $total_str;
            }

            $data = ""; $detail_on_level = "";
            if(!empty($total_str)){
                $level = Content::getMinMaxFromFormule($this->getLevel());$data = "data-formule='".$total."' data-text='religion'";
                if($level['same'] != true){ 
                    for($i=$level['min']; $i<=$level['max']; $i++){
                        $total_int_on_level = $total_int;
                        if(!empty($intel_str)){
                            $total_int_on_level += Content::getValueFromFormule($intel_str, $i);
                        }
                        if(!empty($religion_str)){
                            $total_int_on_level += Content::getValueFromFormule($religion_str, $i);
                        }
             $detail_on_level .= "<p class='size-0-7 text-grey-d-2'>lvl ".$i." : <span class='bold'>".$total_int_on_level."</span></p>";
                        $data .= " data-level".$i."='".$total_int_on_level."' ";
                    }
                }
            }

            switch ($format) {
                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$total} Religion",
                            "color" => "intel",
                            "tooltip" => "Religion (Intel + Bonus de Religion + Équipements + Maîtrise)",
                            "style" => Style::STYLE_BACK,
                            "data" => $data
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    $icon = $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "skill_intel.png",
                            "color" => "intel",
                            "content" => $total,
                            "size" => 10,
                            "content_placement" => Style::POSITION_RIGHT
                        ], 
                        write: false);

                    return $view->dispatch(
                        template_name : "tile",
                        data : [
                            "title" => $icon,
                            "text" => "Religion",
                            "color" => "intel",
                            "size" => Style::SIZE_SM,
                            "tooltip" => "Religion (Intel + Bonus de Religion + Équipements + Maîtrise)",
                            "with_border" => false
                        ], 
                        write: false);

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "modules/characteristic",
                        data : [
                            "name" => "Religion",
                            "value" => $total,
                            "color" => "intel",
                            "icon" => "skill_intel.png",
                            "size" => Style::SIZE_SM,
                            "detail" => $this->getIntel(Content::FORMAT_BADGE) . " + " . $this->getBonus_skill_mastery($this->getReligion_mastery(), Content::FORMAT_BADGE, true) . " + " . $this->getReligion_bonus(Content::FORMAT_BADGE) . " + <span class='badge back-grey'>".$bonus_item."</span> (équipement)",
                            "data" => $data,
                            "detail_on_level" => $detail_on_level
                        ], 
                        write: false);
                
                default:
                    return $total;
            }
        }        
        public function getReligion_bonus(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::DISPLAY_EDITABLE: 
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [ 
                            "class_name" => ucfirst(get_class($this)),
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "religion",
                            "label" => "Bonus d'Religion",
                            "placeholder" => "Bonus d'Religion " . self::VERBAL_NAME_OF_CLASSE,
                            "tooltip" => "Bonus d'Religion",
                            "value" => $this->_religion_bonus,
                            "color" => "intel",
                            "style" => Style::INPUT_BASIC
                        ], 
                        write: false); 

                case Content::FORMAT_BADGE: 
                    return $view->dispatch(
                        template_name : "badge",
                        data : [ 
                            "content" => "{$this->_religion_bonus} Bonus d'Religion",
                            "color" => "intel",
                            "tooltip" => "Bonus d'Religion",
                            "style" => Style::STYLE_OUTLINE
                        ], 
                        write: false); 

                default:
                    return $this->_religion_bonus;
            }
        }
        public function getReligion_mastery(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    $items = [];
                    foreach (self::SKILL_MATERY_LIST as $name => $mastery) {
                
                        switch ($mastery) {
                            case self::SKILL_EXPERTISE: $color = "blue"; break;
                            case self::SKILL_MASTERY: $color = "blue-grey"; break;
                            default: $color = "grey"; break;
                        }
                        $items[] = [
                            "display" => "<span class='badge back-".$color."-d-2'>" .ucfirst($name)."</span>",
                            "onclick" => ucfirst(get_class($this)).".update('{$this->getUniqid()}', {$mastery}, 'religion_mastery', ".Controller::IS_VALUE.")"
                        ];
                    }
                    return $view->dispatch(
                        template_name : "dropdown",
                        data : [
                            "label" => $this->getReligion_mastery(Content::FORMAT_BADGE),
                            "tooltip" => "Maîtrise de la compétence",
                            "items" => $items,
                            "id" => "religion_mastery_{$this->getUniqid()}",
                        ], 
                        write: false);
    
                case Content::FORMAT_BADGE:
                    if(in_array($this->_religion_mastery, self::SKILL_MATERY_LIST)){
                        switch ($this->_religion_mastery) {
                            case self::SKILL_EXPERTISE: $color = "blue"; break;
                            case self::SKILL_MASTERY: $color = "blue-grey"; break;
                            default: $color = "grey"; break;
                        }

                        return $view->dispatch(
                            template_name : "badge",
                            data : [
                                "content" => ucfirst(array_search($this->_religion_mastery, self::SKILL_MATERY_LIST)),
                                "color" =>  $color. "-d-2",
                                "tooltip" => "Maîtrise de la compétence",
                                "style" => Style::STYLE_BACK
                            ], 
                            write: false);

                    } else  {
                        return "";
                    }

                default:
                    return $this->_religion_mastery;
            }
        }
        public function getNature(int $format = Content::FORMAT_BRUT){
            $view = new View();
            $bonus_item = 0;
            if(is_numeric($this->getBonus_caract_by_item("nature"))){
                $bonus_item = $this->getBonus_caract_by_item("nature");
            }
            $bonus_mastery = 0;
            if(is_numeric($this->getBonus_skill_mastery($this->getNature_mastery()))){
                $bonus_mastery = $this->getBonus_skill_mastery($this->getNature_mastery());
            }

            $total = "";
            $total_int = 0;
            $total_str = ""; $intel_str = ""; $nature_str = "";

            if(is_numeric($this->getNature_bonus()) && is_numeric($this->getIntel())){
                $total_int += $this->getNature_bonus() + $this->getIntel() + $bonus_item + $bonus_mastery;
                $total = $total_int;
            } elseif(is_numeric($this->getNature_bonus()) && !is_numeric($this->getIntel())){
                $total_int += $this->getNature_bonus() + $bonus_item + $bonus_mastery;
                $total_str = $this->getIntel();
                $intel_str = $this->getIntel();
                $total = $total_int . " + " . $intel_str;
            } elseif(!is_numeric($this->getNature_bonus()) && is_numeric($this->getIntel())){
                $total_int += $this->getIntel() + $bonus_item + $bonus_mastery;
                $total_str = $this->getNature_bonus();
                $nature_str = $this->getNature_bonus();
                $total = $total_int . " + " . $nature_str;
            } else {
                $total_int += $bonus_item + $bonus_mastery;
                $total_str = $this->getNature_bonus() . " + " . $this->getIntel();
                $intel_str = $this->getIntel();
                $nature_str = $this->getNature_bonus();
                $total = $total_int . " + " . $total_str;
            }

            $data = ""; $detail_on_level = "";
            if(!empty($total_str)){
                $level = Content::getMinMaxFromFormule($this->getLevel());$data = "data-formule='".$total."' data-text='nature'";
                if($level['same'] != true){ 
                    for($i=$level['min']; $i<=$level['max']; $i++){
                        $total_int_on_level = $total_int;
                        if(!empty($intel_str)){
                            $total_int_on_level += Content::getValueFromFormule($intel_str, $i);
                        }
                        if(!empty($nature_str)){
                            $total_int_on_level += Content::getValueFromFormule($nature_str, $i);
                        }
             $detail_on_level .= "<p class='size-0-7 text-grey-d-2'>lvl ".$i." : <span class='bold'>".$total_int_on_level."</span></p>";
                        $data .= " data-level".$i."='".$total_int_on_level."' ";
                    }
                }
            }

            switch ($format) {
                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$total} Nature",
                            "color" => "intel",
                            "tooltip" => "Nature (Force + Bonus d'Nature + Équipements + Maîtrise)",
                            "style" => Style::STYLE_BACK,
                            "data" => $data
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    $icon = $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "skill_intel.png",
                            "color" => "intel",
                            "content" => $total,
                            "size" => 10,
                            "content_placement" => Style::POSITION_RIGHT
                        ], 
                        write: false);

                    return $view->dispatch(
                        template_name : "tile",
                        data : [
                            "title" => $icon,
                            "text" => "Nature",
                            "color" => "intel",
                            "size" => Style::SIZE_SM,
                            "tooltip" => "Nature (Force + Bonus d'Nature + Équipements + Maîtrise)",
                            "with_border" => false
                        ], 
                        write: false);

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "modules/characteristic",
                        data : [
                            "name" => "Nature",
                            "value" => $total,
                            "color" => "intel",
                            "icon" => "skill_intel.png",
                            "size" => Style::SIZE_SM,
                            "detail" => $this->getStrong(Content::FORMAT_BADGE) . " + " . $this->getBonus_skill_mastery($this->getNature_mastery(), Content::FORMAT_BADGE, true) . " + " . $this->getNature_bonus(Content::FORMAT_BADGE) . " + <span class='badge back-grey'>".$bonus_item."</span> (équipement)",
                            "data" => $data,
                            "detail_on_level" => $detail_on_level
                        ], 
                        write: false);
                
                default:
                    return $total;
            }
        }                
        public function getNature_bonus(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::DISPLAY_EDITABLE: 
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [ 
                            "class_name" => ucfirst(get_class($this)),
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "nature",
                            "label" => "Bonus de Nature",
                            "placeholder" => "Bonus de Nature " . self::VERBAL_NAME_OF_CLASSE,
                            "tooltip" => "Bonus de Nature",
                            "value" => $this->_nature_bonus,
                            "color" => "intel",
                            "style" => Style::INPUT_BASIC
                        ], 
                        write: false); 

                case Content::FORMAT_BADGE: 
                    return $view->dispatch(
                        template_name : "badge",
                        data : [ 
                            "content" => "{$this->_nature_bonus} Bonus de Nature",
                            "color" => "intel",
                            "tooltip" => "Bonus de Nature",
                            "style" => Style::STYLE_OUTLINE
                        ], 
                        write: false); 

                default:
                    return $this->_nature_bonus;
            }
        }
        public function getNature_mastery(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    $items = [];
                    foreach (self::SKILL_MATERY_LIST as $name => $mastery) {
                
                        switch ($mastery) {
                            case self::SKILL_EXPERTISE: $color = "blue"; break;
                            case self::SKILL_MASTERY: $color = "blue-grey"; break;
                            default: $color = "grey"; break;
                        }
                        $items[] = [
                            "display" => "<span class='badge back-".$color."-d-2'>" .ucfirst($name)."</span>",
                            "onclick" => ucfirst(get_class($this)).".update('{$this->getUniqid()}', {$mastery}, 'nature_mastery', ".Controller::IS_VALUE.")"
                        ];
                    }
                    return $view->dispatch(
                        template_name : "dropdown",
                        data : [
                            "label" => $this->getNature_mastery(Content::FORMAT_BADGE),
                            "tooltip" => "Maîtrise de la compétence",
                            "items" => $items,
                            "id" => "nature_mastery_{$this->getUniqid()}",
                        ], 
                        write: false);
    
                case Content::FORMAT_BADGE:
                    if(in_array($this->_nature_mastery, self::SKILL_MATERY_LIST)){
                        switch ($this->_nature_mastery) {
                            case self::SKILL_EXPERTISE: $color = "blue"; break;
                            case self::SKILL_MASTERY: $color = "blue-grey"; break;
                            default: $color = "grey"; break;
                        }

                        return $view->dispatch(
                            template_name : "badge",
                            data : [
                                "content" => ucfirst(array_search($this->_nature_mastery, self::SKILL_MATERY_LIST)),
                                "color" =>  $color. "-d-2",
                                "tooltip" => "Maîtrise de la compétence",
                                "style" => Style::STYLE_BACK
                            ], 
                            write: false);

                    } else  {
                        return "";
                    }

                default:
                    return $this->_nature_mastery;
            }
        }
        public function getDressage(int $format = Content::FORMAT_BRUT){
            $view = new View();
            $bonus_item = 0;
            if(is_numeric($this->getBonus_caract_by_item("dressage"))){
                $bonus_item = $this->getBonus_caract_by_item("dressage");
            }
            $bonus_mastery = 0;
            if(is_numeric($this->getBonus_skill_mastery($this->getDressage_mastery()))){
                $bonus_mastery = $this->getBonus_skill_mastery($this->getDressage_mastery());
            }

            $total = "";
            $total_int = 0;
            $total_str = ""; $sagesse_str = ""; $dressage_str = "";

            if(is_numeric($this->getDressage_bonus()) && is_numeric($this->getSagesse())){
                $total_int += $this->getDressage_bonus() + $this->getSagesse() + $bonus_item + $bonus_mastery;
                $total = $total_int;
            } elseif(is_numeric($this->getDressage_bonus()) && !is_numeric($this->getSagesse())){
                $total_int += $this->getDressage_bonus() + $bonus_item + $bonus_mastery;
                $total_str = $this->getSagesse();
                $sagesse_str = $this->getSagesse();
                $total = $total_int . " + " . $sagesse_str;
            } elseif(!is_numeric($this->getDressage_bonus()) && is_numeric($this->getSagesse())){
                $total_int += $this->getSagesse() + $bonus_item + $bonus_mastery;
                $total_str = $this->getDressage_bonus();
                $dressage_str = $this->getDressage_bonus();
                $total = $total_int . " + " . $dressage_str;
            } else {
                $total_int += $bonus_item + $bonus_mastery;
                $total_str = $this->getDressage_bonus() . " + " . $this->getSagesse();
                $sagesse_str = $this->getSagesse();
                $dressage_str = $this->getDressage_bonus();
                $total = $total_int . " + " . $total_str;
            }

            $data = ""; $detail_on_level = "";
            if(!empty($total_str)){
                $level = Content::getMinMaxFromFormule($this->getLevel());$data = "data-formule='".$total."' data-text='dressage'";
                if($level['same'] != true){ 
                    for($i=$level['min']; $i<=$level['max']; $i++){
                        $total_int_on_level = $total_int;
                        if(!empty($sagesse_str)){
                            $total_int_on_level += Content::getValueFromFormule($sagesse_str, $i);
                        }
                        if(!empty($dressage_str)){
                            $total_int_on_level += Content::getValueFromFormule($dressage_str, $i);
                        }
             $detail_on_level .= "<p class='size-0-7 text-grey-d-2'>lvl ".$i." : <span class='bold'>".$total_int_on_level."</span></p>";
                        $data .= " data-level".$i."='".$total_int_on_level."' ";
                    }
                }
            }

            switch ($format) {
                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$total} Dressage",
                            "color" => "sagesse",
                            "tooltip" => "Dressage (Sagesse + Bonus de Dressage + Équipements + Maîtrise)",
                            "style" => Style::STYLE_BACK,
                            "data" => $data
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    $icon = $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "skill_sagesse.png",
                            "color" => "sagesse",
                            "content" => $total,
                            "size" => 10,
                            "content_placement" => Style::POSITION_RIGHT
                        ], 
                        write: false);

                    return $view->dispatch(
                        template_name : "tile",
                        data : [
                            "title" => $icon,
                            "text" => "Dressage",
                            "color" => "sagesse",
                            "size" => Style::SIZE_SM,
                            "tooltip" => "Dressage (Sagesse + Bonus de Dressage + Équipements + Maîtrise)",
                            "with_border" => false
                        ], 
                        write: false);

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "modules/characteristic",
                        data : [
                            "name" => "Dressage",
                            "value" => $total,
                            "color" => "sagesse",
                            "icon" => "skill_sagesse.png",
                            "size" => Style::SIZE_SM,
                            "detail" => $this->getSagesse(Content::FORMAT_BADGE) . " + " . $this->getBonus_skill_mastery($this->getDressage_mastery(), Content::FORMAT_BADGE, true) . " + " . $this->getDressage_bonus(Content::FORMAT_BADGE) . " + <span class='badge back-grey'>".$bonus_item."</span> (équipement)",
                            "data" => $data,
                            "detail_on_level" => $detail_on_level
                        ], 
                        write: false);
                
                default:
                    return $total;
            }
        }
        public function getDressage_bonus(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::DISPLAY_EDITABLE: 
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [ 
                            "class_name" => ucfirst(get_class($this)),
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "dressage",
                            "label" => "Bonus de Dressage",
                            "placeholder" => "Bonus de Dressage " . self::VERBAL_NAME_OF_CLASSE,
                            "tooltip" => "Bonus de Dressage",
                            "value" => $this->_dressage_bonus,
                            "color" => "sagesse",
                            "style" => Style::INPUT_BASIC
                        ], 
                        write: false); 

                case Content::FORMAT_BADGE: 
                    return $view->dispatch(
                        template_name : "badge",
                        data : [ 
                            "content" => "{$this->_dressage_bonus} Bonus de Dressage",
                            "color" => "sagesse",
                            "tooltip" => "Bonus de Dressage",
                            "style" => Style::STYLE_OUTLINE
                        ], 
                        write: false); 

                default:
                    return $this->_dressage_bonus;
            }
        }
        public function getDressage_mastery(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    $items = [];
                    foreach (self::SKILL_MATERY_LIST as $name => $mastery) {
                
                        switch ($mastery) {
                            case self::SKILL_EXPERTISE: $color = "blue"; break;
                            case self::SKILL_MASTERY: $color = "blue-grey"; break;
                            default: $color = "grey"; break;
                        }
                        $items[] = [
                            "display" => "<span class='badge back-".$color."-d-2'>" .ucfirst($name)."</span>",
                            "onclick" => ucfirst(get_class($this)).".update('{$this->getUniqid()}', {$mastery}, 'dressage_mastery', ".Controller::IS_VALUE.")"
                        ];
                    }
                    return $view->dispatch(
                        template_name : "dropdown",
                        data : [
                            "label" => $this->getDressage_mastery(Content::FORMAT_BADGE),
                            "tooltip" => "Maîtrise de la compétence",
                            "items" => $items,
                            "id" => "dressage_mastery_{$this->getUniqid()}",
                        ], 
                        write: false);
    
                case Content::FORMAT_BADGE:
                    if(in_array($this->_dressage_mastery, self::SKILL_MATERY_LIST)){
                        switch ($this->_dressage_mastery) {
                            case self::SKILL_EXPERTISE: $color = "blue"; break;
                            case self::SKILL_MASTERY: $color = "blue-grey"; break;
                            default: $color = "grey"; break;
                        }

                        return $view->dispatch(
                            template_name : "badge",
                            data : [
                                "content" => ucfirst(array_search($this->_dressage_mastery, self::SKILL_MATERY_LIST)),
                                "color" =>  $color. "-d-2",
                                "tooltip" => "Maîtrise de la compétence",
                                "style" => Style::STYLE_BACK
                            ], 
                            write: false);

                    } else  {
                        return "";
                    }

                default:
                    return $this->_dressage_mastery;
            }
        }
        public function getMedecine(int $format = Content::FORMAT_BRUT){
            $view = new View();
            $bonus_item = 0;
            if(is_numeric($this->getBonus_caract_by_item("medecine"))){
                $bonus_item = $this->getBonus_caract_by_item("medecine");
            }
            $bonus_mastery = 0;
            if(is_numeric($this->getBonus_skill_mastery($this->getMedecine_mastery()))){
                $bonus_mastery = $this->getBonus_skill_mastery($this->getMedecine_mastery());
            }

            $total = "";
            $total_int = 0;
            $total_str = ""; $sagesse_str = ""; $medecine_str = "";

            if(is_numeric($this->getMedecine_bonus()) && is_numeric($this->getSagesse())){
                $total_int += $this->getMedecine_bonus() + $this->getSagesse() + $bonus_item + $bonus_mastery;
                $total = $total_int;
            } elseif(is_numeric($this->getMedecine_bonus()) && !is_numeric($this->getSagesse())){
                $total_int += $this->getMedecine_bonus() + $bonus_item + $bonus_mastery;
                $total_str = $this->getSagesse();
                $sagesse_str = $this->getSagesse();
                $total = $total_int . " + " . $sagesse_str;
            } elseif(!is_numeric($this->getMedecine_bonus()) && is_numeric($this->getSagesse())){
                $total_int += $this->getSagesse() + $bonus_item + $bonus_mastery;
                $total_str = $this->getMedecine_bonus();
                $medecine_str = $this->getMedecine_bonus();
                $total = $total_int . " + " . $medecine_str;
            } else {
                $total_int += $bonus_item + $bonus_mastery;
                $total_str = $this->getMedecine_bonus() . " + " . $this->getSagesse();
                $sagesse_str = $this->getSagesse();
                $medecine_str = $this->getMedecine_bonus();
                $total = $total_int . " + " . $total_str;
            }

            $data = ""; $detail_on_level = "";
            if(!empty($total_str)){
                $level = Content::getMinMaxFromFormule($this->getLevel());$data = "data-formule='".$total."' data-text='médecine'";
                if($level['same'] != true){ 
                    for($i=$level['min']; $i<=$level['max']; $i++){
                        $total_int_on_level = $total_int;
                        if(!empty($sagesse_str)){
                            $total_int_on_level += Content::getValueFromFormule($sagesse_str, $i);
                        }
                        if(!empty($medecine_str)){
                            $total_int_on_level += Content::getValueFromFormule($medecine_str, $i);
                        }
             $detail_on_level .= "<p class='size-0-7 text-grey-d-2'>lvl ".$i." : <span class='bold'>".$total_int_on_level."</span></p>";
                        $data .= " data-level".$i."='".$total_int_on_level."' ";
                    }
                }
            }

            switch ($format) {
                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$total} Médecine",
                            "color" => "sagesse",
                            "tooltip" => "Médecine (Sagesse + Bonus de Médecine + Équipements + Maîtrise)",
                            "style" => Style::STYLE_BACK,
                            "data" => $data
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    $icon = $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "skill_sagesse.png",
                            "color" => "sagesse",
                            "content" => $total,
                            "size" => 10,
                            "content_placement" => Style::POSITION_RIGHT
                        ], 
                        write: false);

                    return $view->dispatch(
                        template_name : "tile",
                        data : [
                            "title" => $icon,
                            "text" => "Médecine",
                            "color" => "sagesse",
                            "size" => Style::SIZE_SM,
                            "tooltip" => "Médecine (Sagesse + Bonus de Médecine + Équipements + Maîtrise)",
                            "with_border" => false
                        ], 
                        write: false);

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "modules/characteristic",
                        data : [
                            "name" => "Médecine",
                            "value" => $total,
                            "color" => "sagesse",
                            "icon" => "skill_sagesse.png",
                            "size" => Style::SIZE_SM,
                            "detail" => $this->getIntel(Content::FORMAT_BADGE) . " + " . $this->getBonus_skill_mastery($this->getMedecine_mastery(), Content::FORMAT_BADGE, true) . " + " . $this->getMedecine_bonus(Content::FORMAT_BADGE) . " + <span class='badge back-grey'>".$bonus_item."</span> (équipement)",
                            "data" => $data,
                            "detail_on_level" => $detail_on_level
                        ], 
                        write: false);
                
                default:
                    return $total;
            }
        }
        public function getMedecine_bonus(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::DISPLAY_EDITABLE: 
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [ 
                            "class_name" => ucfirst(get_class($this)),
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "medecine",
                            "label" => "Bonus de Medecine",
                            "placeholder" => "Bonus de Medecine " . self::VERBAL_NAME_OF_CLASSE,
                            "tooltip" => "Bonus de Medecine",
                            "value" => $this->_medecine_bonus,
                            "color" => "sagesse",
                            "style" => Style::INPUT_BASIC
                        ], 
                        write: false); 

                case Content::FORMAT_BADGE: 
                    return $view->dispatch(
                        template_name : "badge",
                        data : [ 
                            "content" => "{$this->_medecine_bonus} Bonus de Medecine",
                            "color" => "sagesse",
                            "tooltip" => "Bonus de Medecine",
                            "style" => Style::STYLE_OUTLINE
                        ], 
                        write: false); 

                default:
                    return $this->_medecine_bonus;
            }
        }
        public function getMedecine_mastery(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    $items = [];
                    foreach (self::SKILL_MATERY_LIST as $name => $mastery) {
                
                        switch ($mastery) {
                            case self::SKILL_EXPERTISE: $color = "blue"; break;
                            case self::SKILL_MASTERY: $color = "blue-grey"; break;
                            default: $color = "grey"; break;
                        }
                        $items[] = [
                            "display" => "<span class='badge back-".$color."-d-2'>" .ucfirst($name)."</span>",
                            "onclick" => ucfirst(get_class($this)).".update('{$this->getUniqid()}', {$mastery}, 'medecine_mastery', ".Controller::IS_VALUE.")"
                        ];
                    }
                    return $view->dispatch(
                        template_name : "dropdown",
                        data : [
                            "label" => $this->getMedecine_mastery(Content::FORMAT_BADGE),
                            "tooltip" => "Maîtrise de la compétence",
                            "items" => $items,
                            "id" => "medecine_mastery_{$this->getUniqid()}",
                        ], 
                        write: false);
    
                case Content::FORMAT_BADGE:
                    if(in_array($this->_medecine_mastery, self::SKILL_MATERY_LIST)){
                        switch ($this->_medecine_mastery) {
                            case self::SKILL_EXPERTISE: $color = "blue"; break;
                            case self::SKILL_MASTERY: $color = "blue-grey"; break;
                            default: $color = "grey"; break;
                        }

                        return $view->dispatch(
                            template_name : "badge",
                            data : [
                                "content" => ucfirst(array_search($this->_medecine_mastery, self::SKILL_MATERY_LIST)),
                                "color" =>  $color. "-d-2",
                                "tooltip" => "Maîtrise de la compétence",
                                "style" => Style::STYLE_BACK
                            ], 
                            write: false);

                    } else  {
                        return "";
                    }

                default:
                    return $this->_medecine_mastery;
            }
        }
        public function getPerception(int $format = Content::FORMAT_BRUT){
            $view = new View();
            $bonus_item = 0;
            if(is_numeric($this->getBonus_caract_by_item("perception"))){
                $bonus_item = $this->getBonus_caract_by_item("perception");
            }
            $bonus_mastery = 0;
            if(is_numeric($this->getBonus_skill_mastery($this->getPerception_mastery()))){
                $bonus_mastery = $this->getBonus_skill_mastery($this->getPerception_mastery());
            }

            $total = "";
            $total_int = 0;
            $total_str = ""; $sagesse_str = ""; $perception_str = "";

            if(is_numeric($this->getPerception_bonus()) && is_numeric($this->getSagesse())){
                $total_int += $this->getPerception_bonus() + $this->getSagesse() + $bonus_item + $bonus_mastery;
                $total = $total_int;
            } elseif(is_numeric($this->getPerception_bonus()) && !is_numeric($this->getSagesse())){
                $total_int += $this->getPerception_bonus() + $bonus_item + $bonus_mastery;
                $total_str = $this->getSagesse();
                $sagesse_str = $this->getSagesse();
                $total = $total_int . " + " . $sagesse_str;
            } elseif(!is_numeric($this->getPerception_bonus()) && is_numeric($this->getSagesse())){
                $total_int += $this->getSagesse() + $bonus_item + $bonus_mastery;
                $total_str = $this->getPerception_bonus();
                $perception_str = $this->getPerception_bonus();
                $total = $total_int . " + " . $perception_str;
            } else {
                $total_int += $bonus_item + $bonus_mastery;
                $total_str = $this->getPerception_bonus() . " + " . $this->getSagesse();
                $sagesse_str = $this->getSagesse();
                $perception_str = $this->getPerception_bonus();
                $total = $total_int . " + " . $total_str;
            }

            $data = ""; $detail_on_level = "";
            if(!empty($total_str)){
                $level = Content::getMinMaxFromFormule($this->getLevel());$data = "data-formule='".$total."' data-text='perception'";
                if($level['same'] != true){ 
                    for($i=$level['min']; $i<=$level['max']; $i++){
                        $total_int_on_level = $total_int;
                        if(!empty($sagesse_str)){
                            $total_int_on_level += Content::getValueFromFormule($sagesse_str, $i);
                        }
                        if(!empty($perception_str)){
                            $total_int_on_level += Content::getValueFromFormule($perception_str, $i);
                        }
             $detail_on_level .= "<p class='size-0-7 text-grey-d-2'>lvl ".$i." : <span class='bold'>".$total_int_on_level."</span></p>";
                        $data .= " data-level".$i."='".$total_int_on_level."' ";
                    }
                }
            }

            switch ($format) {
                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$total} Perception",
                            "color" => "sagesse",
                            "tooltip" => "Perception (Sagesse + Bonus de Perception + Équipements + Maîtrise)",
                            "style" => Style::STYLE_BACK,
                            "data" => $data
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    $icon = $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "skill_sagesse.png",
                            "color" => "sagesse",
                            "content" => $total,
                            "size" => 10,
                            "content_placement" => Style::POSITION_RIGHT
                        ], 
                        write: false);

                    return $view->dispatch(
                        template_name : "tile",
                        data : [
                            "title" => $icon,
                            "text" => "Perception",
                            "color" => "sagesse",
                            "size" => Style::SIZE_SM,
                            "tooltip" => "Perception (Sagesse + Bonus de Perception + Équipements + Maîtrise)",
                            "with_border" => false
                        ], 
                        write: false);

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "modules/characteristic",
                        data : [
                            "name" => "Perception",
                            "value" => $total,
                            "color" => "sagesse",
                            "icon" => "skill_sagesse.png",
                            "size" => Style::SIZE_SM,
                            "detail" => $this->getSagesse(Content::FORMAT_BADGE) . " + " . $this->getBonus_skill_mastery($this->getPerception_mastery(), Content::FORMAT_BADGE, true) . " + " . $this->getPerception_bonus(Content::FORMAT_BADGE) . " + <span class='badge back-grey'>".$bonus_item."</span> (équipement)",
                            "data" => $data,
                            "detail_on_level" => $detail_on_level
                        ], 
                        write: false);
                
                default:
                    return $total;
            }
        }
        public function getPerception_bonus(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::DISPLAY_EDITABLE: 
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [ 
                            "class_name" => ucfirst(get_class($this)),
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "perception",
                            "label" => "Bonus de Perception",
                            "placeholder" => "Bonus de Perception " . self::VERBAL_NAME_OF_CLASSE,
                            "tooltip" => "Bonus de Perception",
                            "value" => $this->_perception_bonus,
                            "color" => "sagesse",
                            "style" => Style::INPUT_BASIC
                        ], 
                        write: false); 

                case Content::FORMAT_BADGE: 
                    return $view->dispatch(
                        template_name : "badge",
                        data : [ 
                            "content" => "{$this->_perception_bonus} Bonus de Perception",
                            "color" => "sagesse",
                            "tooltip" => "Bonus de Perception",
                            "style" => Style::STYLE_OUTLINE
                        ], 
                        write: false); 

                default:
                    return $this->_perception_bonus;
            }
        }
        public function getPerception_mastery(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    $items = [];
                    foreach (self::SKILL_MATERY_LIST as $name => $mastery) {
                
                        switch ($mastery) {
                            case self::SKILL_EXPERTISE: $color = "blue"; break;
                            case self::SKILL_MASTERY: $color = "blue-grey"; break;
                            default: $color = "grey"; break;
                        }
                        $items[] = [
                            "display" => "<span class='badge back-".$color."-d-2'>" .ucfirst($name)."</span>",
                            "onclick" => ucfirst(get_class($this)).".update('{$this->getUniqid()}', {$mastery}, 'perception_mastery', ".Controller::IS_VALUE.")"
                        ];
                    }
                    return $view->dispatch(
                        template_name : "dropdown",
                        data : [
                            "label" => $this->getPerception_mastery(Content::FORMAT_BADGE),
                            "tooltip" => "Maîtrise de la compétence",
                            "items" => $items,
                            "id" => "perception_mastery_{$this->getUniqid()}",
                        ], 
                        write: false);
    
                case Content::FORMAT_BADGE:
                    if(in_array($this->_perception_mastery, self::SKILL_MATERY_LIST)){
                        switch ($this->_perception_mastery) {
                            case self::SKILL_EXPERTISE: $color = "blue"; break;
                            case self::SKILL_MASTERY: $color = "blue-grey"; break;
                            default: $color = "grey"; break;
                        }

                        return $view->dispatch(
                            template_name : "badge",
                            data : [
                                "content" => ucfirst(array_search($this->_perception_mastery, self::SKILL_MATERY_LIST)),
                                "color" =>  $color. "-d-2",
                                "tooltip" => "Maîtrise de la compétence",
                                "style" => Style::STYLE_BACK
                            ], 
                            write: false);

                    } else  {
                        return "";
                    }

                default:
                    return $this->_perception_mastery;
            }
        }
        public function getPerspicacite(int $format = Content::FORMAT_BRUT){
            $view = new View();
            $bonus_item = 0;
            if(is_numeric($this->getBonus_caract_by_item("perspicacite"))){
                $bonus_item = $this->getBonus_caract_by_item("perspicacite");
            }
            $bonus_mastery = 0;
            if(is_numeric($this->getBonus_skill_mastery($this->getPerspicacite_mastery()))){
                $bonus_mastery = $this->getBonus_skill_mastery($this->getPerspicacite_mastery());
            }

            $total = "";
            $total_int = 0;
            $total_str = ""; $sagesse_str = ""; $perspicacite_str = "";

            if(is_numeric($this->getPerspicacite_bonus()) && is_numeric($this->getSagesse())){
                $total_int += $this->getPerspicacite_bonus() + $this->getSagesse() + $bonus_item + $bonus_mastery;
                $total = $total_int;
            } elseif(is_numeric($this->getPerspicacite_bonus()) && !is_numeric($this->getSagesse())){
                $total_int += $this->getPerspicacite_bonus() + $bonus_item + $bonus_mastery;
                $total_str = $this->getSagesse();
                $sagesse_str = $this->getSagesse();
                $total = $total_int . " + " . $sagesse_str;
            } elseif(!is_numeric($this->getPerspicacite_bonus()) && is_numeric($this->getSagesse())){
                $total_int += $this->getSagesse() + $bonus_item + $bonus_mastery;
                $total_str = $this->getPerspicacite_bonus();
                $perspicacite_str = $this->getPerspicacite_bonus();
                $total = $total_int . " + " . $perspicacite_str;
            } else {
                $total_int += $bonus_item + $bonus_mastery;
                $total_str = $this->getPerspicacite_bonus() . " + " . $this->getSagesse();
                $sagesse_str = $this->getSagesse();
                $perspicacite_str = $this->getPerspicacite_bonus();
                $total = $total_int . " + " . $total_str;
            }

            $data = ""; $detail_on_level = "";
            if(!empty($total_str)){
                $level = Content::getMinMaxFromFormule($this->getLevel());$data = "data-formule='".$total."' data-text='perspicacite'";
                if($level['same'] != true){ 
                    for($i=$level['min']; $i<=$level['max']; $i++){
                        $total_int_on_level = $total_int;
                        if(!empty($sagesse_str)){
                            $total_int_on_level += Content::getValueFromFormule($sagesse_str, $i);
                        }
                        if(!empty($perspicacite_str)){
                            $total_int_on_level += Content::getValueFromFormule($perspicacite_str, $i);
                        }
             $detail_on_level .= "<p class='size-0-7 text-grey-d-2'>lvl ".$i." : <span class='bold'>".$total_int_on_level."</span></p>";
                        $data .= " data-level".$i."='".$total_int_on_level."' ";
                    }
                }
            }

            switch ($format) {
                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$total} Perspicacité",
                            "color" => "sagesse",
                            "tooltip" => "Perspicacité (Sagesse + Bonus de Perspicacité + Équipements + Maîtrise)",
                            "style" => Style::STYLE_BACK,
                            "data" => $data
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    $icon = $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "skill_sagesse.png",
                            "color" => "sagesse",
                            "content" => $total,
                            "size" => 10,
                            "content_placement" => Style::POSITION_RIGHT
                        ], 
                        write: false);

                    return $view->dispatch(
                        template_name : "tile",
                        data : [
                            "title" => $icon,
                            "text" => "Perspicacité",
                            "color" => "sagesse",
                            "size" => Style::SIZE_SM,
                            "tooltip" => "Perspicacité (Sagesse + Bonus de Perspicacité + Équipements + Maîtrise)",
                            "with_border" => false
                        ], 
                        write: false);

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "modules/characteristic",
                        data : [
                            "name" => "Perspicacité",
                            "value" => $total,
                            "color" => "sagesse",
                            "icon" => "skill_sagesse.png",
                            "size" => Style::SIZE_SM,
                            "detail" => $this->getSagesse(Content::FORMAT_BADGE) . " + " . $this->getBonus_skill_mastery($this->getPerspicacite_mastery(), Content::FORMAT_BADGE, true) . " + " . $this->getPerspicacite_bonus(Content::FORMAT_BADGE) . " + <span class='badge back-grey'>".$bonus_item."</span> (équipement)",
                            "data" => $data,
                            "detail_on_level" => $detail_on_level
                        ], 
                        write: false);
                
                default:
                    return $total;
            }
        }
        public function getPerspicacite_bonus(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::DISPLAY_EDITABLE: 
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [ 
                            "class_name" => ucfirst(get_class($this)),
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "perspicacite",
                            "label" => "Bonus de Perspicacité",
                            "placeholder" => "Bonus de Perspicacité " . self::VERBAL_NAME_OF_CLASSE,
                            "tooltip" => "Bonus de Perspicacité",
                            "value" => $this->_perspicacite_bonus,
                            "color" => "sagesse",
                            "style" => Style::INPUT_BASIC
                        ], 
                        write: false); 

                case Content::FORMAT_BADGE: 
                    return $view->dispatch(
                        template_name : "badge",
                        data : [ 
                            "content" => "{$this->_perspicacite_bonus} Bonus de Perspicacité",
                            "color" => "sagesse",
                            "tooltip" => "Bonus de Perspicacité",
                            "style" => Style::STYLE_OUTLINE
                        ], 
                        write: false); 

                default:
                    return $this->_perspicacite_bonus;
            }
        }
        public function getPerspicacite_mastery(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    $items = [];
                    foreach (self::SKILL_MATERY_LIST as $name => $mastery) {
                
                        switch ($mastery) {
                            case self::SKILL_EXPERTISE: $color = "blue"; break;
                            case self::SKILL_MASTERY: $color = "blue-grey"; break;
                            default: $color = "grey"; break;
                        }
                        $items[] = [
                            "display" => "<span class='badge back-".$color."-d-2'>" .ucfirst($name)."</span>",
                            "onclick" => ucfirst(get_class($this)).".update('{$this->getUniqid()}', {$mastery}, 'perspicacite_mastery', ".Controller::IS_VALUE.")"
                        ];
                    }
                    return $view->dispatch(
                        template_name : "dropdown",
                        data : [
                            "label" => $this->getPerspicacite_mastery(Content::FORMAT_BADGE),
                            "tooltip" => "Maîtrise de la compétence",
                            "items" => $items,
                            "id" => "perspicacite_mastery_{$this->getUniqid()}",
                        ], 
                        write: false);
    
                case Content::FORMAT_BADGE:
                    if(in_array($this->_perspicacite_mastery, self::SKILL_MATERY_LIST)){
                        switch ($this->_perspicacite_mastery) {
                            case self::SKILL_EXPERTISE: $color = "blue"; break;
                            case self::SKILL_MASTERY: $color = "blue-grey"; break;
                            default: $color = "grey"; break;
                        }

                        return $view->dispatch(
                            template_name : "badge",
                            data : [
                                "content" => ucfirst(array_search($this->_perspicacite_mastery, self::SKILL_MATERY_LIST)),
                                "color" =>  $color. "-d-2",
                                "tooltip" => "Maîtrise de la compétence",
                                "style" => Style::STYLE_BACK
                            ], 
                            write: false);

                    } else  {
                        return "";
                    }

                default:
                    return $this->_perspicacite_mastery;
            }
        }
        public function getSurvie(int $format = Content::FORMAT_BRUT){
            $view = new View();
            $bonus_item = 0;
            if(is_numeric($this->getBonus_caract_by_item("survie"))){
                $bonus_item = $this->getBonus_caract_by_item("survie");
            }
            $bonus_mastery = 0;
            if(is_numeric($this->getBonus_skill_mastery($this->getSurvie_mastery()))){
                $bonus_mastery = $this->getBonus_skill_mastery($this->getSurvie_mastery());
            }

            $total = "";
            $total_int = 0;
            $total_str = ""; $sagesse_str = ""; $survie_str = "";

            if(is_numeric($this->getSurvie_bonus()) && is_numeric($this->getSagesse())){
                $total_int += $this->getSurvie_bonus() + $this->getSagesse() + $bonus_item + $bonus_mastery;
                $total = $total_int;
            } elseif(is_numeric($this->getSurvie_bonus()) && !is_numeric($this->getSagesse())){
                $total_int += $this->getSurvie_bonus() + $bonus_item + $bonus_mastery;
                $total_str = $this->getSagesse();
                $sagesse_str = $this->getSagesse();
                $total = $total_int . " + " . $sagesse_str;
            } elseif(!is_numeric($this->getSurvie_bonus()) && is_numeric($this->getSagesse())){
                $total_int += $this->getSagesse() + $bonus_item + $bonus_mastery;
                $total_str = $this->getSurvie_bonus();
                $survie_str = $this->getSurvie_bonus();
                $total = $total_int . " + " . $survie_str;
            } else {
                $total_int += $bonus_item + $bonus_mastery;
                $total_str = $this->getSurvie_bonus() . " + " . $this->getSagesse();
                $sagesse_str = $this->getSagesse();
                $survie_str = $this->getSurvie_bonus();
                $total = $total_int . " + " . $total_str;
            }

            $data = ""; $detail_on_level = "";
            if(!empty($total_str)){
                $level = Content::getMinMaxFromFormule($this->getLevel());$data = "data-formule='".$total."' data-text='survie'";
                if($level['same'] != true){ 
                    for($i=$level['min']; $i<=$level['max']; $i++){
                        $total_int_on_level = $total_int;
                        if(!empty($sagesse_str)){
                            $total_int_on_level += Content::getValueFromFormule($sagesse_str, $i);
                        }
                        if(!empty($survie_str)){
                            $total_int_on_level += Content::getValueFromFormule($survie_str, $i);
                        }
             $detail_on_level .= "<p class='size-0-7 text-grey-d-2'>lvl ".$i." : <span class='bold'>".$total_int_on_level."</span></p>";
                        $data .= " data-level".$i."='".$total_int_on_level."' ";
                    }
                }
            }

            switch ($format) {
                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$total} Survie",
                            "color" => "sagesse",
                            "tooltip" => "Survie (Sagesse + Bonus de Survie + Équipements + Maîtrise)",
                            "style" => Style::STYLE_BACK,
                            "data" => $data
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    $icon = $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "skill_sagesse.png",
                            "color" => "sagesse",
                            "content" => $total,
                            "size" => 10,
                            "content_placement" => Style::POSITION_RIGHT
                        ], 
                        write: false);

                    return $view->dispatch(
                        template_name : "tile",
                        data : [
                            "title" => $icon,
                            "text" => "Survie",
                            "color" => "sagesse",
                            "size" => Style::SIZE_SM,
                            "tooltip" => "Survie (Sagesse + Bonus de Survie + Équipements + Maîtrise)",
                            "with_border" => false
                        ], 
                        write: false);

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "modules/characteristic",
                        data : [
                            "name" => "Survie",
                            "value" => $total,
                            "color" => "sagesse",
                            "icon" => "skill_sagesse.png",
                            "size" => Style::SIZE_SM,
                            "detail" => $this->getSagesse(Content::FORMAT_BADGE) . " + " . $this->getBonus_skill_mastery($this->getSurvie_mastery(), Content::FORMAT_BADGE, true) . " + " . $this->getSurvie_bonus(Content::FORMAT_BADGE) . " + <span class='badge back-grey'>".$bonus_item."</span> (équipement)",
                            "data" => $data,
                            "detail_on_level" => $detail_on_level
                        ], 
                        write: false);
                
                default:
                    return $total;
            }
        }
        public function getSurvie_bonus(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::DISPLAY_EDITABLE: 
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [ 
                            "class_name" => ucfirst(get_class($this)),
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "survie",
                            "label" => "Bonus de Survie",
                            "placeholder" => "Bonus de Survie " . self::VERBAL_NAME_OF_CLASSE,
                            "tooltip" => "Bonus de Survie",
                            "value" => $this->_survie_bonus,
                            "color" => "sagesse",
                            "style" => Style::INPUT_BASIC
                        ], 
                        write: false); 

                case Content::FORMAT_BADGE: 
                    return $view->dispatch(
                        template_name : "badge",
                        data : [ 
                            "content" => "{$this->_survie_bonus} Bonus de Survie",
                            "color" => "sagesse",
                            "tooltip" => "Bonus de Survie",
                            "style" => Style::STYLE_OUTLINE
                        ], 
                        write: false); 

                default:
                    return $this->_survie_bonus;
            }
        }
        public function getSurvie_mastery(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    $items = [];
                    foreach (self::SKILL_MATERY_LIST as $name => $mastery) {
                
                        switch ($mastery) {
                            case self::SKILL_EXPERTISE: $color = "bleu"; break;
                            case self::SKILL_MASTERY: $color = "blue-grey"; break;
                            default: $color = "grey"; break;
                        }
                        $items[] = [
                            "display" => "<span class='badge back-".$color."-d-2'>" .ucfirst($name)."</span>",
                            "onclick" => ucfirst(get_class($this)).".update('{$this->getUniqid()}', {$mastery}, 'survie_mastery', ".Controller::IS_VALUE.")"
                        ];
                    }
                    return $view->dispatch(
                        template_name : "dropdown",
                        data : [
                            "label" => $this->getSurvie_mastery(Content::FORMAT_BADGE),
                            "tooltip" => "Maîtrise de la compétence",
                            "items" => $items,
                            "id" => "survie_mastery_{$this->getUniqid()}",
                        ], 
                        write: false);
    
                case Content::FORMAT_BADGE:
                    if(in_array($this->_survie_mastery, self::SKILL_MATERY_LIST)){
                        switch ($this->_survie_mastery) {
                            case self::SKILL_EXPERTISE: $color = "blue"; break;
                            case self::SKILL_MASTERY: $color = "blue-grey"; break;
                            default: $color = "grey"; break;
                        }

                        return $view->dispatch(
                            template_name : "badge",
                            data : [
                                "content" => ucfirst(array_search($this->_survie_mastery, self::SKILL_MATERY_LIST)),
                                "color" =>  $color. "-d-2",
                                "tooltip" => "Maîtrise de la compétence",
                                "style" => Style::STYLE_BACK
                            ], 
                            write: false);

                    } else  {
                        return "";
                    }

                default:
                    return $this->_survie_mastery;
            }
        }
        public function getPersuasion(int $format = Content::FORMAT_BRUT){
            $view = new View();
            $bonus_item = 0;
            if(is_numeric($this->getBonus_caract_by_item("persuasion"))){
                $bonus_item = $this->getBonus_caract_by_item("persuasion");
            }
            $bonus_mastery = 0;
            if(is_numeric($this->getBonus_skill_mastery($this->getPersuasion_mastery()))){
                $bonus_mastery = $this->getBonus_skill_mastery($this->getPersuasion_mastery());
            }

            $total = "";
            $total_int = 0;
            $total_str = ""; $chance_str = ""; $persuasion_str = "";

            if(is_numeric($this->getPersuasion_bonus()) && is_numeric($this->getChance())){
                $total_int += $this->getPersuasion_bonus() + $this->getChance() + $bonus_item + $bonus_mastery;
                $total = $total_int;
            } elseif(is_numeric($this->getPersuasion_bonus()) && !is_numeric($this->getChance())){
                $total_int += $this->getPersuasion_bonus() + $bonus_item + $bonus_mastery;
                $total_str = $this->getChance();
                $chance_str = $this->getChance();
                $total = $total_int . " + " . $chance_str;
            } elseif(!is_numeric($this->getPersuasion_bonus()) && is_numeric($this->getChance())){
                $total_int += $this->getChance() + $bonus_item + $bonus_mastery;
                $total_str = $this->getPersuasion_bonus();
                $persuasion_str = $this->getPersuasion_bonus();
                $total = $total_int . " + " . $persuasion_str;
            } else {
                $total_int += $bonus_item + $bonus_mastery;
                $total_str = $this->getPersuasion_bonus() . " + " . $this->getChance();
                $chance_str = $this->getChance();
                $persuasion_str = $this->getPersuasion_bonus();
                $total = $total_int . " + " . $total_str;
            }

            $data = ""; $detail_on_level = "";
            if(!empty($total_str)){
                $level = Content::getMinMaxFromFormule($this->getLevel());$data = "data-formule='".$total."' data-text='persuasion'";
                if($level['same'] != true){ 
                    for($i=$level['min']; $i<=$level['max']; $i++){
                        $total_int_on_level = $total_int;
                        if(!empty($chance_str)){
                            $total_int_on_level += Content::getValueFromFormule($chance_str, $i);
                        }
                        if(!empty($persuasion_str)){
                            $total_int_on_level += Content::getValueFromFormule($persuasion_str, $i);
                        }
             $detail_on_level .= "<p class='size-0-7 text-grey-d-2'>lvl ".$i." : <span class='bold'>".$total_int_on_level."</span></p>";
                        $data .= " data-level".$i."='".$total_int_on_level."' ";
                    }
                }
            }
            
            switch ($format) {
                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$total} Persuasion",
                            "color" => "chance",
                            "tooltip" => "Persuasion (Chance + Bonus de Persuasion + Équipements + Maîtrise)",
                            "style" => Style::STYLE_BACK,
                            "data" => $data
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    $icon = $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "skill_chance.png",
                            "color" => "chance",
                            "content" => $total,
                            "size" => 10,
                            "content_placement" => Style::POSITION_RIGHT
                        ], 
                        write: false);

                    return $view->dispatch(
                        template_name : "tile",
                        data : [
                            "title" => $icon,
                            "text" => "Persuasion",
                            "color" => "chance",
                            "size" => Style::SIZE_SM,
                            "tooltip" => "Persuasion (Chance + Bonus de Persuasion + Équipements + Maîtrise)",
                            "with_border" => false
                        ], 
                        write: false);

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "modules/characteristic",
                        data : [
                            "name" => "Persuasion",
                            "value" => $total,
                            "color" => "chance",
                            "icon" => "skill_chance.png",
                            "size" => Style::SIZE_SM,
                            "detail" => $this->getChance(Content::FORMAT_BADGE) . " + " . $this->getBonus_skill_mastery($this->getPersuasion_mastery(), Content::FORMAT_BADGE, true) . " + " . $this->getPersuasion_bonus(Content::FORMAT_BADGE) . " + <span class='badge back-grey'>".$bonus_item."</span> (équipement)",
                            "data" => $data,
                            "detail_on_level" => $detail_on_level
                        ], 
                        write: false);
                
                default:
                    return $total;
            }
        }
        public function getPersuasion_bonus(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::DISPLAY_EDITABLE: 
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [ 
                            "class_name" => ucfirst(get_class($this)),
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "persuasion",
                            "label" => "Bonus de Persuasion",
                            "placeholder" => "Bonus de Persuasion " . self::VERBAL_NAME_OF_CLASSE,
                            "tooltip" => "Bonus de Persuasion",
                            "value" => $this->_persuasion_bonus,
                            "color" => "chance",
                            "style" => Style::INPUT_BASIC
                        ], 
                        write: false); 

                case Content::FORMAT_BADGE: 
                    return $view->dispatch(
                        template_name : "badge",
                        data : [ 
                            "content" => "{$this->_persuasion_bonus} Bonus de Persuasion",
                            "color" => "chance",
                            "tooltip" => "Bonus de Persuasion",
                            "style" => Style::STYLE_OUTLINE
                        ], 
                        write: false); 

                default:
                    return $this->_persuasion_bonus;
            }
        }
        public function getPersuasion_mastery(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    $items = [];
                    foreach (self::SKILL_MATERY_LIST as $name => $mastery) {
                
                        switch ($mastery) {
                            case self::SKILL_EXPERTISE: $color = "blue"; break;
                            case self::SKILL_MASTERY: $color = "blue-grey"; break;
                            default: $color = "grey"; break;
                        }
                        $items[] = [
                            "display" => "<span class='badge back-".$color."-d-2'>" .ucfirst($name)."</span>",
                            "onclick" => ucfirst(get_class($this)).".update('{$this->getUniqid()}', {$mastery}, 'persuasion_mastery', ".Controller::IS_VALUE.")"
                        ];
                    }
                    return $view->dispatch(
                        template_name : "dropdown",
                        data : [
                            "label" => $this->getPersuasion_mastery(Content::FORMAT_BADGE),
                            "tooltip" => "Maîtrise de la compétence",
                            "items" => $items,
                            "id" => "persuasion_mastery_{$this->getUniqid()}",
                        ], 
                        write: false);
    
                case Content::FORMAT_BADGE:
                    if(in_array($this->_persuasion_mastery, self::SKILL_MATERY_LIST)){
                        switch ($this->_persuasion_mastery) {
                            case self::SKILL_EXPERTISE: $color = "blue"; break;
                            case self::SKILL_MASTERY: $color = "blue-grey"; break;
                            default: $color = "grey"; break;
                        }

                        return $view->dispatch(
                            template_name : "badge",
                            data : [
                                "content" => ucfirst(array_search($this->_persuasion_mastery, self::SKILL_MATERY_LIST)),
                                "color" =>  $color. "-d-2",
                                "tooltip" => "Maîtrise de la compétence",
                                "style" => Style::STYLE_BACK
                            ], 
                            write: false);

                    } else  {
                        return "";
                    }

                default:
                    return $this->_persuasion_mastery;
            }
        }
        public function getRepresentation(int $format = Content::FORMAT_BRUT){
            $view = new View();
            $bonus_item = 0;
            if(is_numeric($this->getBonus_caract_by_item("representation"))){
                $bonus_item = $this->getBonus_caract_by_item("representation");
            }
            $bonus_mastery = 0;
            if(is_numeric($this->getBonus_skill_mastery($this->getRepresentation_mastery()))){
                $bonus_mastery = $this->getBonus_skill_mastery($this->getRepresentation_mastery());
            }
            
            $total = "";
            $total_int = 0;
            $total_str = ""; $chance_str = ""; $representation_str = "";

            if(is_numeric($this->getRepresentation_bonus()) && is_numeric($this->getChance())){
                $total_int += $this->getRepresentation_bonus() + $this->getChance() + $bonus_item + $bonus_mastery;
                $total = $total_int;
            } elseif(is_numeric($this->getRepresentation_bonus()) && !is_numeric($this->getChance())){
                $total_int += $this->getRepresentation_bonus() + $bonus_item + $bonus_mastery;
                $total_str = $this->getChance();
                $chance_str = $this->getChance();
                $total = $total_int . " + " . $chance_str;
            } elseif(!is_numeric($this->getRepresentation_bonus()) && is_numeric($this->getChance())){
                $total_int += $this->getChance() + $bonus_item + $bonus_mastery;
                $total_str = $this->getRepresentation_bonus();
                $representation_str = $this->getRepresentation_bonus();
                $total = $total_int . " + " . $representation_str;
            } else {
                $total_int += $bonus_item + $bonus_mastery;
                $total_str = $this->getRepresentation_bonus() . " + " . $this->getChance();
                $chance_str = $this->getChance();
                $representation_str = $this->getRepresentation_bonus();
                $total = $total_int . " + " . $total_str;
            }

            $data = ""; $detail_on_level = "";
            if(!empty($total_str)){
                $level = Content::getMinMaxFromFormule($this->getLevel());$data = "data-formule='".$total."' data-text='représentation'";
                if($level['same'] != true){ 
                    for($i=$level['min']; $i<=$level['max']; $i++){
                        $total_int_on_level = $total_int;
                        if(!empty($chance_str)){
                            $total_int_on_level += Content::getValueFromFormule($chance_str, $i);
                        }
                        if(!empty($representation_str)){
                            $total_int_on_level += Content::getValueFromFormule($representation_str, $i);
                        }
             $detail_on_level .= "<p class='size-0-7 text-grey-d-2'>lvl ".$i." : <span class='bold'>".$total_int_on_level."</span></p>";
                        $data .= " data-level".$i."='".$total_int_on_level."' ";
                    }
                }
            }

            switch ($format) {
                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$total} Représentation",
                            "color" => "chance",
                            "tooltip" => "Représentation (Chance + Bonus de Représentation + Équipements + Maîtrise)",
                            "style" => Style::STYLE_BACK,
                            "data" => $data
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    $icon = $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "skill_chance.png",
                            "color" => "chance",
                            "content" => $total,
                            "size" => 10,
                            "content_placement" => Style::POSITION_RIGHT
                        ], 
                        write: false);

                    return $view->dispatch(
                        template_name : "tile",
                        data : [
                            "title" => $icon,
                            "text" => "Représentation",
                            "color" => "chance",
                            "size" => Style::SIZE_SM,
                            "tooltip" => "Représentation (Chance + Bonus de Représentation + Équipements + Maîtrise)",
                            "with_border" => false
                        ], 
                        write: false);

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "modules/characteristic",
                        data : [
                            "name" => "Représentation",
                            "value" => $total,
                            "color" => "chance",
                            "icon" => "skill_chance.png",
                            "size" => Style::SIZE_SM,
                            "detail" => $this->getChance(Content::FORMAT_BADGE) . " + " . $this->getBonus_skill_mastery($this->getRepresentation_mastery(), Content::FORMAT_BADGE, true) . " + " . $this->getRepresentation_bonus(Content::FORMAT_BADGE) . " + <span class='badge back-grey'>".$bonus_item."</span> (équipement)",
                            "data" => $data,
                            "detail_on_level" => $detail_on_level
                        ], 
                        write: false);
                
                default:
                    return $total;
            }
        }
        public function getRepresentation_bonus(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::DISPLAY_EDITABLE: 
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [ 
                            "class_name" => ucfirst(get_class($this)),
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "_representation",
                            "label" => "Bonus de Représentation",
                            "placeholder" => "Bonus de Représentation " . self::VERBAL_NAME_OF_CLASSE,
                            "tooltip" => "Bonus de Représentation",
                            "value" => $this->_representation_bonus,
                            "color" => "chance",
                            "style" => Style::INPUT_BASIC
                        ], 
                        write: false); 

                case Content::FORMAT_BADGE: 
                    return $view->dispatch(
                        template_name : "badge",
                        data : [ 
                            "content" => "{$this->_representation_bonus} Bonus de Représentation",
                            "color" => "chance",
                            "tooltip" => "Bonus de Représentation",
                            "style" => Style::STYLE_OUTLINE
                        ], 
                        write: false); 

                default:
                    return $this->_representation_bonus;
            }
        }
        public function getRepresentation_mastery(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    $items = [];
                    foreach (self::SKILL_MATERY_LIST as $name => $mastery) {
                
                        switch ($mastery) {
                            case self::SKILL_EXPERTISE: $color = "blue"; break;
                            case self::SKILL_MASTERY: $color = "blue-grey"; break;
                            default: $color = "grey"; break;
                        }
                        $items[] = [
                            "display" => "<span class='badge back-".$color."-d-2'>" .ucfirst($name)."</span>",
                            "onclick" => ucfirst(get_class($this)).".update('{$this->getUniqid()}', {$mastery}, 'representation_mastery', ".Controller::IS_VALUE.")"
                        ];
                    }
                    return $view->dispatch(
                        template_name : "dropdown",
                        data : [
                            "label" => $this->getRepresentation_mastery(Content::FORMAT_BADGE),
                            "tooltip" => "Maîtrise de la compétence",
                            "items" => $items,
                            "id" => "representation_mastery_{$this->getUniqid()}",
                        ], 
                        write: false);
    
                case Content::FORMAT_BADGE:
                    if(in_array($this->_representation_mastery, self::SKILL_MATERY_LIST)){
                        switch ($this->_representation_mastery) {
                            case self::SKILL_EXPERTISE: $color = "blue"; break;
                            case self::SKILL_MASTERY: $color = "blue-grey"; break;
                            default: $color = "grey"; break;
                        }

                        return $view->dispatch(
                            template_name : "badge",
                            data : [
                                "content" => ucfirst(array_search($this->_representation_mastery, self::SKILL_MATERY_LIST)),
                                "color" =>  $color. "-d-2",
                                "tooltip" => "Maîtrise de la compétence",
                                "style" => Style::STYLE_BACK
                            ], 
                            write: false);

                    } else  {
                        return "";
                    }

                default:
                    return $this->_representation_mastery;
            }
        }
        public function getSupercherie(int $format = Content::FORMAT_BRUT){
            $view = new View();
            $bonus_item = 0;
            if(is_numeric($this->getBonus_caract_by_item("supercherie"))){
                $bonus_item = $this->getBonus_caract_by_item("supercherie");
            }
            $bonus_mastery = 0;
            if(is_numeric($this->getBonus_skill_mastery($this->getSupercherie_mastery()))){
                $bonus_mastery = $this->getBonus_skill_mastery($this->getSupercherie_mastery());
            }

            $total = "";
            $total_int = 0;
            $total_str = ""; $chance_str = ""; $supercherie_str = "";

            if(is_numeric($this->getSupercherie_bonus()) && is_numeric($this->getChance())){
                $total_int += $this->getSupercherie_bonus() + $this->getChance() + $bonus_item + $bonus_mastery;
                $total = $total_int;
            } elseif(is_numeric($this->getSupercherie_bonus()) && !is_numeric($this->getChance())){
                $total_int += $this->getSupercherie_bonus() + $bonus_item + $bonus_mastery;
                $total_str = $this->getChance();
                $chance_str = $this->getChance();
                $total = $total_int . " + " . $chance_str;
            } elseif(!is_numeric($this->getSupercherie_bonus()) && is_numeric($this->getChance())){
                $total_int += $this->getChance() + $bonus_item + $bonus_mastery;
                $total_str = $this->getSupercherie_bonus();
                $supercherie_str = $this->getSupercherie_bonus();
                $total = $total_int . " + " . $supercherie_str;
            } else {
                $total_int += $bonus_item + $bonus_mastery;
                $total_str = $this->getSupercherie_bonus() . " + " . $this->getChance();
                $chance_str = $this->getChance();
                $supercherie_str = $this->getSupercherie_bonus();
                $total = $total_int . " + " . $total_str;
            }

            $data = ""; $detail_on_level = "";
            if(!empty($total_str)){
                $level = Content::getMinMaxFromFormule($this->getLevel());$data = "data-formule='".$total."' data-text='supercherie'";
                if($level['same'] != true){ 
                    for($i=$level['min']; $i<=$level['max']; $i++){
                        $total_int_on_level = $total_int;
                        if(!empty($chance_str)){
                            $total_int_on_level += Content::getValueFromFormule($chance_str, $i);
                        }
                        if(!empty($supercherie_str)){
                            $total_int_on_level += Content::getValueFromFormule($supercherie_str, $i);
                        }
                        $detail_on_level .= "<p class='size-0-7 text-grey-d-2'>lvl ".$i." : <span class='bold'><span class='bold'>".$total_int_on_level."</span></p>";
                        $data .= " data-level".$i."='".$total_int_on_level."' ";
                    }
                }
            }

            switch ($format) {
                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$total} Supercherie",
                            "color" => "chance",
                            "tooltip" => "Supercherie (Chance + Bonus de Supercherie + Équipements + Maîtrise)",
                            "style" => Style::STYLE_BACK,
                            "data" => $data
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    $icon = $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "skill_chance.png",
                            "color" => "chance",
                            "content" => $total,
                            "size" => 10,
                            "content_placement" => Style::POSITION_RIGHT
                        ], 
                        write: false);

                    return $view->dispatch(
                        template_name : "tile",
                        data : [
                            "title" => $icon,
                            "text" => "Supercherie",
                            "color" => "chance",
                            "size" => Style::SIZE_SM,
                            "tooltip" => "Supercherie (Chance + Bonus de Supercherie + Équipements + Maîtrise)",
                            "with_border" => false
                        ], 
                        write: false);

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "modules/characteristic",
                        data : [
                            "name" => "Supercherie",
                            "value" => $total,
                            "color" => "chance",
                            "icon" => "skill_chance.png",
                            "size" => Style::SIZE_SM,
                            "detail" => $this->getChance(Content::FORMAT_BADGE) . " + " . $this->getBonus_skill_mastery($this->getSupercherie_mastery(), Content::FORMAT_BADGE, true) . " + " . $this->getSupercherie_bonus(Content::FORMAT_BADGE) . " + <span class='badge back-grey'>".$bonus_item."</span> (équipement)",
                            "data" => $data,
                            "detail_on_level" => $detail_on_level
                        ], 
                        write: false);
                
                default:
                    return $total;
            }
        }
        public function getSupercherie_bonus(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::DISPLAY_EDITABLE: 
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [ 
                            "class_name" => ucfirst(get_class($this)),
                            "id" => "supercherie".$this->getUniqid(),
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "supercherie",
                            "label" => "Bonus de Supercherie",
                            "placeholder" => "Bonus de Supercherie " . self::VERBAL_NAME_OF_CLASSE,
                            "tooltip" => "Bonus de Supercherie",
                            "value" => $this->_supercherie_bonus,
                            "color" => "chance",
                            "style" => Style::INPUT_BASIC
                        ], 
                        write: false); 

                case Content::FORMAT_BADGE: 
                    return $view->dispatch(
                        template_name : "badge",
                        data : [ 
                            "content" => "{$this->_supercherie_bonus} Bonus de Supercherie",
                            "color" => "chance",
                            "tooltip" => "Bonus de Supercherie",
                            "style" => Style::STYLE_OUTLINE
                        ], 
                        write: false); 

                default:
                    return $this->_supercherie_bonus;
            }
        }
        public function getSupercherie_mastery(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::FORMAT_EDITABLE:
                    $items = [];
                    foreach (self::SKILL_MATERY_LIST as $name => $mastery) {
                
                        switch ($mastery) {
                            case self::SKILL_EXPERTISE: $color = "blue"; break;
                            case self::SKILL_MASTERY: $color = "blue-grey"; break;
                            default: $color = "grey"; break;
                        }
                        $items[] = [
                            "display" => "<span class='badge back-".$color."-d-2'>" .ucfirst($name)."</span>",
                            "onclick" => ucfirst(get_class($this)).".update('{$this->getUniqid()}', {$mastery}, 'supercherie_mastery', ".Controller::IS_VALUE.")"
                        ];
                    }
                    return $view->dispatch(
                        template_name : "dropdown",
                        data : [
                            "label" => $this->getSupercherie_mastery(Content::FORMAT_BADGE),
                            "tooltip" => "Maîtrise de la compétence",
                            "items" => $items,
                            "id" => "supercherie_mastery_{$this->getUniqid()}",
                        ], 
                        write: false);
    
                case Content::FORMAT_BADGE:
                    if(in_array($this->_supercherie_mastery, self::SKILL_MATERY_LIST)){
                        switch ($this->_supercherie_mastery) {
                            case self::SKILL_EXPERTISE: $color = "blue"; break;
                            case self::SKILL_MASTERY: $color = "blue-grey"; break;
                            default: $color = "grey"; break;
                        }

                        return $view->dispatch(
                            template_name : "badge",
                            data : [
                                "content" => ucfirst(array_search($this->_supercherie_mastery, self::SKILL_MATERY_LIST)),
                                "color" =>  $color. "-d-2",
                                "tooltip" => "Maîtrise de la compétence",
                                "style" => Style::STYLE_BACK
                            ], 
                            write: false);

                    } else  {
                        return "";
                    }

                default:
                    return $this->_supercherie_mastery;
            }
        }
     
        public function getKamas(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::DISPLAY_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => ucfirst(get_class($this)),
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "kamas",
                            "label" => "Kamas",
                            "placeholder" => "Nb de Kamas " . self::VERBAL_NAME_OF_CLASSE,
                            "tooltip" => "Kamas",
                            "value" => $this->_kamas,
                            "color" => "kamas-d-3",
                            "style" => Style::INPUT_ICON,
                            "size" => Style::SIZE_SM,
                            "icon" => "kamas.png",
                            "style_icon" => Style::ICON_MEDIA
                        ], 
                        write: false);

                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$this->_kamas} Kamas",
                            "color" => "kamas-d-3",
                            "tooltip" => "Nb de Kamas",
                            "style" => Style::STYLE_BACK,
                            "id" => "kamas"
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "icon",
                        data : [
                            "style" => Style::ICON_MEDIA,
                            "icon" => "kamas.png",
                            "color" => "kamas-d-3",
                            "tooltip" => "Kamas",
                            "content" => $this->_kamas,
                            "content_placement" => Style::POSITION_RIGHT
                        ], 
                        write: false); 

                default:
                    return $this->_kamas;
            }
        }
        public function getDrop_(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::DISPLAY_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/text",
                        data : [
                            "class_name" => ucfirst(get_class($this)),
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "drop_",
                            "label" => "Objets Récupérables",
                            "placeholder" => "Objets récupérables " . self::VERBAL_NAME_OF_CLASSE,
                            "value" => $this->_drop_,
                            "style" => Style::INPUT_FLOATING,
                            "comment" => "Objets récupérables " . self::VERBAL_NAME_OF_CLASSE
                        ], 
                        write: false);
                
                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => $this->_drop_,
                            "color" => "grey-d-2",
                            "tooltip" => "Objets récupérables " . self::VERBAL_NAME_OF_CLASSE,
                            "style" => Style::STYLE_OUTLINE
                        ], 
                        write: false);

                default:
                    return $this->_drop_;

            }
        }

        public function getOther_item(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::DISPLAY_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/ckeditor",
                        data : [
                            "class_name" => ucfirst(get_class($this)),
                            "id" => "other_item".$this->getUniqid(),
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "other_item",
                            "label" => "Autres équipements",
                            "value" => $this->_other_item
                        ], 
                        write: false);
                
                default:
                    if(empty($this->_other_item)){return "";}
                    return html_entity_decode($this->_other_item);
            }
        }
        public function getOther_spell(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::DISPLAY_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/ckeditor",
                        data : [
                            "class_name" => ucfirst(get_class($this)),
                            "id" => "other_spell".$this->getUniqid(),
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "other_spell",
                            "label" => "Autres sorts",
                            "value" => $this->_other_spell
                        ], 
                        write: false);
                
                default:
                    if(empty($this->_other_spell)){return "";}
                    return html_entity_decode($this->_other_spell);
            }
        }
        public function getOther_consumable(int $format = Content::FORMAT_BRUT){
            $view = new View();
            switch ($format) {
                case Content::DISPLAY_EDITABLE:
                    return $view->dispatch(
                        template_name : "input/ckeditor",
                        data : [
                            "class_name" => ucfirst(get_class($this)),
                            "id" => "other_consumable".$this->getUniqid(),
                            "uniqid" => $this->getUniqid(),
                            "input_name" => "other_consumable",
                            "label" => "Autres consommables",
                            "value" => $this->_other_consumable
                        ], 
                        write: false);
                
                default:
                    if(empty($this->_other_consumable)){return "";}
                    return html_entity_decode($this->_other_consumable);
            }
        }

        public function getConsumable(int $format = Content::FORMAT_BRUT, bool $is_removable = false){
            $view = new View();
            $managername = ucfirst(get_class($this)) . "Manager";
            $manager = new $managername;
            if(method_exists($manager, "getLinkConsumable")){
                $links = $manager->getLinkConsumable($this);

                switch ($format) { 
                    case Content::DISPLAY_EDITABLE:
                        ob_start(); ?>
                            <div><?=$this->getConsumable(Content::FORMAT_BRUT, true)?></div>
                            <?php 
                                $view->dispatch(
                                    template_name : "input/search",
                                    data : [
                                        "id" => "addConsumable" . $this->getUniqid(),
                                        "title" => "Ajouter un consommable",
                                        "label" => "Rechercher un consommable",
                                        "placeholder" => "Rechercher un consommable",
                                        "search_in" => ControllerModule::SEARCH_IN_CONSUMABLE,
                                        "parameter" => $this->getUniqid(),
                                        "action" => ControllerModule::SEARCH_DONE_ADD_CONSUMABLE_TO_NPC,
                                    ], 
                                    write: true);
                            ?>  
                        <?php return ob_get_clean();
    
                    case Content::FORMAT_ARRAY:
                        return $links;
                    
                    default:
                        if(!empty($links)){
                            $view->setTemplate_type(View::TEMPLATE_DISPLAY);
                            return $view->dispatch(
                                template_name : "npc/list_link",
                                data : [
                                    "links" => $links,
                                    "uniqid" => $this->getUniqid(),
                                    "user" => ControllerConnect::getCurrentUser(),
                                    "class_name" => "npc",
                                    "input_name" => "consumable",
                                    "is_editable" => $is_removable
                                ], 
                                write: false
                            );
                        }else{
                            echo "Aucun consommable associé";
                        }
                }   
            }

        }
        public function getItem(int $format = Content::FORMAT_BRUT, bool $is_removable = false){
            $view = new View();
            $managername = ucfirst(get_class($this)) . "Manager";
            $manager = new $managername;
            if(method_exists($manager, "getLinkItem")){
                $links = $manager->getLinkItem($this);

                switch ($format) { 
                    case Content::DISPLAY_EDITABLE:
                        ob_start(); ?>
                            <div><?=$this->getItem(Content::FORMAT_BRUT, true)?></div>
                            <?php 
                                $view->dispatch(
                                    template_name : "input/search",
                                    data : [
                                        "id" => "addItem" . $this->getUniqid(),
                                        "title" => "Ajouter un équipement",
                                        "label" => "Rechercher un équipement",
                                        "placeholder" => "Rechercher un équipement",
                                        "search_in" => ControllerModule::SEARCH_IN_ITEM,
                                        "parameter" => $this->getUniqid(),
                                        "action" => ControllerModule::SEARCH_DONE_ADD_ITEM_TO_NPC,
                                    ], 
                                    write: true);
                            ?>  
                        <?php return ob_get_clean();

                    case Content::FORMAT_ARRAY:
                        return $links;
                    
                    default:
                        if(!empty($links)){
                            $view->setTemplate_type(View::TEMPLATE_DISPLAY);
                            return $view->dispatch(
                                template_name : "npc/list_link",
                                data : [
                                    "links" => $links,
                                    "uniqid" => $this->getUniqid(),
                                    "user" => ControllerConnect::getCurrentUser(),
                                    "class_name" => "npc",
                                    "input_name" => "item",
                                    "is_editable" => $is_removable
                                ], 
                                write: false
                            );
                        }else{
                            echo "Aucun équipement associé";
                        }
                }  
            } 
        }
        public function getSpell(int $format = Content::FORMAT_BRUT, $display_remove = false, $size = 300){
            $managername = ucfirst(get_class($this)) . "Manager";
            $manager = new $managername;
            if(method_exists($manager, "getLinkSpell")){
                $spells = $manager->getLinkSpell($this);
            
                switch ($format) {
                    case Content::DISPLAY_EDITABLE:
                        $view = new View();
                        $html = $view->dispatch(
                            template_name : "input/search",
                            data : [
                                "id" => "addSpell" . $this->getUniqid(),
                                "title" => "Ajouter un sort",
                                "label" => "Rechercher un sort",
                                "placeholder" => "Rechercher un sort",
                                "search_in" => ControllerModule::SEARCH_IN_SPELL,
                                "parameter" => $this->getUniqid(),
                                "action" => ControllerModule::SEARCH_DONE_ADD_SPELL_TO_NPC,
                            ], 
                            write: false);
                        return $html . $this->getSpell(Content::DISPLAY_RESUME, true);

                    case Content::DISPLAY_RESUME:
                        $view = new View(View::TEMPLATE_DISPLAY);
                        if(!empty($spells)){
                            return $view->dispatch(
                                template_name : "spell/list",
                                data : [
                                    "spells" => $spells,
                                    "is_removable" => $display_remove,
                                    "uniqid" => $this->getUniqid(),
                                    "class_name" => "Npc",
                                    "size" => $size
                                ], 
                                write: false);
                        }
                        return "";

                    case Content::DISPLAY_LIST:
                        $view = new View(View::TEMPLATE_DISPLAY);
                        if(!empty($spells)){
                            ob_start();
                                ?> <ul class="list-unstyled"> <?php
                                    foreach ($spells as $spell) {?>
                                        <li>
                                            <?php $view->dispatch(
                                                template_name : "spell/text",
                                                data : [
                                                    "obj" => $spell,
                                                    "is_link" => true
                                                ], 
                                                write: true); ?>
                                        </li> <?php
                                    }
                                ?> </ul> <?php
                            return ob_get_clean();
                        }
                        return "";

                    case Content::FORMAT_ARRAY:
                        return $spells;
                }
            }
        }
        public function getCapability(int $format = Content::FORMAT_BRUT, $display_remove = false, $size = 300){
            $managername = ucfirst(get_class($this)) . "Manager";
            $manager = new $managername;
            if(method_exists($manager, "getLinkItem")){
                $capabilities = $manager->getLinkCapability($this);

                switch ($format) {
                    case Content::DISPLAY_EDITABLE:
                        $view = new View();
                        $html = $view->dispatch(
                            template_name : "input/search",
                            data : [
                                "id" => "addCapability" . $this->getUniqid(),
                                "title" => "Ajouter une aptitude",
                                "label" => "Rechercher une aptitude",
                                "placeholder" => "Rechercher une aptitude",
                                "search_in" => ControllerModule::SEARCH_IN_CAPABILITY,
                                "parameter" => $this->getUniqid(),
                                "action" => ControllerModule::SEARCH_DONE_ADD_CAPABILITY_TO_NPC,
                            ], 
                            write: false);
                        return $html . $this->getCapability(Content::DISPLAY_RESUME, true);

                    case Content::DISPLAY_RESUME:
                        $view = new View(View::TEMPLATE_DISPLAY);
                        if(!empty($capabilities)){
                            return $view->dispatch(
                                template_name : "capability/list",
                                data : [
                                    "capabilities" => $capabilities,
                                    "is_removable" => $display_remove,
                                    "uniqid" => $this->getUniqid(),
                                    "class_name" => "Npc",
                                    "size" => $size
                                ], 
                                write: false);
                        }
                        return "";

                    case Content::DISPLAY_LIST:
                        $view = new View(View::TEMPLATE_DISPLAY);
                        if(!empty($capabilities)){
                            ob_start();
                                ?> <ul class="list-unstyled"> <?php
                                    foreach ($capabilities as $capability) {?>
                                        <li>
                                            <?php $view->dispatch(
                                                template_name : "capability/text",
                                                data : [
                                                    "obj" => $capability,
                                                    "is_link" => true
                                                ], 
                                                write: true); ?>
                                        </li> <?php
                                    }
                                ?> </ul> <?php
                            return ob_get_clean();
                        }
                        return "";

                    case Content::FORMAT_ARRAY:
                        return $capabilities;
                }
            }
        }

        public function getMaster_bonus(int $format = Content::FORMAT_BRUT){
            $master_bonus =  Controller::calcMaster_bonus($this->getLevel());
            $view = new View();
            switch ($format) {
                case Content::FORMAT_BADGE:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => "{$master_bonus} Bonus de maîtrise",
                            "color" => "master_bonus-d-4",
                            "tooltip" => "Bonus de maîtrise",
                            "style" => Style::STYLE_BACK
                        ], 
                        write: false);

                case Content::FORMAT_ICON:
                    return $view->dispatch(
                        template_name : "badge",
                        data : [
                            "content" => $master_bonus,
                            "color" => "master_bonus-d-4",
                            "tooltip" => "Bonus de maîtrise",
                            "style" => Style::STYLE_BACK
                        ], 
                        write: false);

                case Content::FORMAT_VIEW:
                    return $view->dispatch(
                        template_name : "modules/characteristic",
                        data : [ 
                            "name" => "Bonus de maîtrise",
                            "value" => $master_bonus,
                            "color" => "master_bonus",
                            "icon" => "master_bonus.png",
                            "size" => Style::SIZE_MD,
                            "detail" => "(1 + Niveau) divisé par 4"
                        ], 
                        write: false);

                default:
                    return $master_bonus;
            }
        }
        public function getBonus_skill_mastery(int $skill_mastery = Creature::SKILL_NONE, int $format = Content::FORMAT_BRUT, $display_if_none = false){
            $view = new View();
            switch ($skill_mastery) {
                case self::SKILL_EXPERTISE:
                    $total = 2 * $this->getMaster_bonus();
                    $color = "blue";
                break;
                case self::SKILL_MASTERY:
                    $total = $this->getMaster_bonus();
                    $color = "blue-grey";
                break;
                default:
                    $total = 0;
                    $color = "grey";
                break;
            }

            switch ($format) {
                case Content::FORMAT_BADGE:
                    if($display_if_none){
                        if($total > 0 ) { $value = "+". $total . " | "; } else { $value = "";}
                        return $view->dispatch(
                            template_name : "badge",
                            data : [
                                "content" => $value . ucfirst(array_search($total, self::SKILL_MATERY_LIST)),
                                "color" =>  $color. "-d-2",
                                "tooltip" => "Maîtrise de la compétence",
                                "style" => Style::STYLE_BACK
                            ], 
                            write: false);
                    } else {
                        return "";
                    }

                default:
                    return $total;
            }
        }

        public function getBonus_caract_by_item($caract){
            if(isset(self::CARACTERISTICS[$caract])){
                if(!empty($this->_bonus_array)){
                    if(isset($this->_bonus_array[$caract])){
                        if(isset($this->_bonus_array[$caract])){
                            return  $this->_bonus_array[$caract];
                        }
                    }
                }
                return 0;
            } else {
                throw new Exception("La caractéristique ".$caract." n'existe pas");
            }
        }

    //♥♥♥♥♥♥♥♥♥♥♥♥♥♥ SETTERS ♥♥♥♥♥♥♥♥♥♥♥♥♥♥
        public function setName(string | int $data){
            $this->_name = $data;
            return true;
        }
        public function setDescription(string | null $data){
            $this->_description = $data;
            return true;
        }
        public function setLevel(string | int | null  $data){
            $this->_level = $data;
            return true;
        }
        public function setTrait(string | int | null  $data){
            $this->_trait = $data;
            return true;
        }
        public function setOther_info(string | null $data){
            $this->_other_info = $data;
            return true;
        }
        public function setLocation(string | null $data){
            $this->_location = $data;
            return true;
        }
      
        public function setLife(string | int | null  $data){
            $this->_life = $data;
            return true;
        }
        public function setPa(string | int | null  $data){
            $this->_pa = $data;
            return true;
        }
        public function setPm(string | int | null  $data){
            $this->_pm = $data;
            return true;
        }
        public function setPo(string | int | null  $data){
            $this->_po = $data;
            return true;
        }
        public function setIni(string | int | null  $data){
            $this->_ini = $data;
            return true;
        }
        public function setInvocation(string | int | null  $data){
            $this->_invocation = $data;
            return true;
        }
        public function setTouch(string | int | null  $data){
            $this->_touch = $data;
            return true;
        }
        public function setCa(string | int | null  $data){
            $this->_ca = $data;
            return true;
        }
        public function setDodge_pa(string | int | null  $data){
            $this->_dodge_pa = $data;
            return true;
        }
        public function setDodge_pm(string | int | null  $data){
            $this->_dodge_pm = $data;
            return true;
        }
        public function setFuite(string | int | null  $data){
            $this->_fuite = $data;
            return true;
        }
        public function setTacle(string | int | null  $data){
            $this->_tacle = $data;
            return true;
        }
        public function setVitality(string | int | null  $data){
            $this->_vitality = $data;
            return true;
        }
        public function setSagesse(string | int | null  $data){
            $this->_sagesse = $data;
            return true;
        }
        public function setStrong(string | int | null  $data){
            $this->_strong = $data;
            return true;
        }
        public function setIntel(string | int | null  $data){
            $this->_intel = $data;
            return true;
        }
        public function setAgi(string | int | null  $data){
            $this->_agi = $data;
            return true;
        }
        public function setChance(string | int | null  $data){
            $this->_chance = $data;
            return true;
        }
        public function setDo_fixe_neutre(string | int | null $data){
            $this->_do_fixe_neutre = $data;
            return true;
        }
        public function setDo_fixe_terre(string | int | null $data){
            $this->_do_fixe_terre = $data;
            return true;
        }
        public function setDo_fixe_feu(string | int | null $data){
            $this->_do_fixe_feu = $data;
            return true;
        }
        public function setDo_fixe_eau(string | int | null $data){
            $this->_do_fixe_eau = $data;
            return true;
        }
        public function setDo_fixe_air(string | int | null $data){
            $this->_do_fixe_air = $data;
            return true;
        }
        public function setDo_fixe_multiple(string | int | null $data){
            $this->_do_fixe_multiple = $data;
            return true;
        }
        public function setRes_neutre(string | int | null  $data){
            $this->_res_neutre = $data;
            return true;
        }
        public function setRes_terre(string | int | null  $data){
            $this->_res_terre = $data;
            return true;
        }
        public function setRes_feu(string | int | null  $data){
            $this->_res_feu = $data;
            return true;
        }
        public function setRes_air(string | int | null  $data){
            $this->_res_air = $data;
            return true;
        }
        public function setRes_eau(string | int | null  $data){
            $this->_res_eau = $data;
            return true;
        }
        public function setAcrobatie_bonus(string | int | null  $data){
            $this->_acrobatie_bonus = $data;
            return true;
        }
        public function setAcrobatie_mastery(int | null  $data){
            if(in_array($data, [self::SKILL_NONE, self::SKILL_MASTERY, self::SKILL_EXPERTISE])){
                $this->_acrobatie_mastery = $data;
                return true;
            } else {
                throw new Exception("La valeur envoyée n'est pas correcte.");
            }
        }
        public function setDiscretion_bonus(string | int | null  $data){
            $this->_discretion_bonus = $data;
            return true;
        }
        public function setDiscretion_mastery(int | null  $data){
            if(in_array($data, [self::SKILL_NONE, self::SKILL_MASTERY, self::SKILL_EXPERTISE])){
                $this->_discretion_mastery = $data;
                return true;
            } else {
                throw new Exception("La valeur envoyée n'est pas correcte.");
            }
        }
        public function setEscamotage_bonus(string | int | null  $data){
            $this->_escamotage_bonus = $data;
            return true;
        }
        public function setEscamotage_mastery(int | null  $data){
            if(in_array($data, [self::SKILL_NONE, self::SKILL_MASTERY, self::SKILL_EXPERTISE])){
                $this->_escamotage_mastery = $data;
                return true;
            } else {
                throw new Exception("La valeur envoyée n'est pas correcte.");
            }
        }
        public function setAthletisme_bonus(string | int | null  $data){
            $this->_athletisme_bonus = $data;
            return true;
        }
        public function setAthletisme_mastery(int | null  $data){
            if(in_array($data, [self::SKILL_NONE, self::SKILL_MASTERY, self::SKILL_EXPERTISE])){
                $this->_athletisme_mastery = $data;
                return true;
            } else {
                throw new Exception("La valeur envoyée n'est pas correcte.");
            }
        }
        public function setIntimidation_bonus(string | int | null  $data){
            $this->_intimidation_bonus = $data;
            return true;
        }
        public function setIntimidation_mastery(int | null  $data){
            if(in_array($data, [self::SKILL_NONE, self::SKILL_MASTERY, self::SKILL_EXPERTISE])){
                $this->_intimidation_mastery = $data;
                return true;
            } else {
                throw new Exception("La valeur envoyée n'est pas correcte.");
            }
        }
        public function setArcane_bonus(string | int | null  $data){
            $this->_arcane_bonus = $data;
            return true;
        }
        public function setArcane_mastery(int | null  $data){
            if(in_array($data, [self::SKILL_NONE, self::SKILL_MASTERY, self::SKILL_EXPERTISE])){
                $this->_arcane_mastery = $data;
                return true;
            } else {
                throw new Exception("La valeur envoyée n'est pas correcte.");
            }
        }
        public function setHistoire_bonus(string | int | null  $data){
            $this->_histoire_bonus = $data;
            return true;
        }
        public function setHistoire_mastery(int | null  $data){
            if(in_array($data, [self::SKILL_NONE, self::SKILL_MASTERY, self::SKILL_EXPERTISE])){
                $this->_histoire_mastery = $data;
                return true;
            } else {
                throw new Exception("La valeur envoyée n'est pas correcte.");
            }
        }
        public function setInvestigation_bonus(string | int | null  $data){
            $this->_investigation_bonus = $data;
            return true;
        }
        public function setInvestigation_mastery(int | null  $data){
            if(in_array($data, [self::SKILL_NONE, self::SKILL_MASTERY, self::SKILL_EXPERTISE])){
                $this->_investigation_mastery = $data;
                return true;
            } else {
                throw new Exception("La valeur envoyée n'est pas correcte.");
            }
        }
        public function setNature_bonus(string | int | null  $data){
            $this->_nature_bonus = $data;
            return true;
        }
        public function setNature_mastery(int | null  $data){
            if(in_array($data, [self::SKILL_NONE, self::SKILL_MASTERY, self::SKILL_EXPERTISE])){
                $this->_nature_mastery = $data;
                return true;
            } else {
                throw new Exception("La valeur envoyée n'est pas correcte.");
            }
        }
        public function setReligion_bonus(string | int | null  $data){
            $this->_religion_bonus = $data;
            return true;
        }
        public function setReligion_mastery(int | null  $data){
            if(in_array($data, [self::SKILL_NONE, self::SKILL_MASTERY, self::SKILL_EXPERTISE])){
               $this->_religion_mastery = $data;
               return true;
            } else {
               throw new Exception("La valeur envoyée n'est pas correcte.");
            }
        }
        public function setDressage_bonus(string | int | null  $data){
            $this->_dressage_bonus = $data;
            return true;
        }
        public function setDressage_mastery(int | null  $data){
            if(in_array($data, [self::SKILL_NONE, self::SKILL_MASTERY])){
                $this->_dressage_mastery = $data;
                return true;
            } else {
                throw new Exception("La valeur envoyée n'est pas correcte.");
            }
        }
        public function setMedecine_bonus(string | int | null  $data){
            $this->_medecine_bonus = $data;
            return true;
        }
        public function setMedecine_mastery(int | null  $data){
            if(in_array($data, [self::SKILL_NONE, self::SKILL_MASTERY])){
                $this->_medecine_mastery = $data;
                return true;
            } else {
                throw new Exception("La valeur envoyée n'est pas correcte.");
            }
        }
        public function setPerception_bonus(string | int | null  $data){
            $this->_perception_bonus = $data;
            return true;
        }
        public function setPerception_mastery(int | null  $data){
            if(in_array($data, [self::SKILL_NONE, self::SKILL_MASTERY])){
                $this->_perception_mastery = $data;
                return true;
            } else {
                throw new Exception("La valeur envoyée n'est pas correcte.");
            }
        }
        public function setPerspicacite_bonus(string | int | null  $data){
            $this->_perspicacite_bonus = $data;
            return true;
        }
        public function setPerspicacite_mastery(int | null  $data){
            if(in_array($data, [self::SKILL_NONE, self::SKILL_MASTERY])){
                $this->_perspicacite_mastery = $data;
                return true;
            } else {
                throw new Exception("La valeur envoyée n'est pas correcte.");
            }
        }
        public function setSurvie_bonus(string | int | null  $data){
            $this->_survie_bonus = $data;
            return true;
        }
        public function setSurvie_mastery(int | null  $data){
            if(in_array($data, [self::SKILL_NONE, self::SKILL_MASTERY])){
                $this->_survie_mastery = $data;
                return true;
            } else {
                throw new Exception("La valeur envoyée n'est pas correcte.");
            }
        }
        public function setPersuasion_bonus(string | int | null  $data){
            $this->_persuasion_bonus = $data;
            return true;
        }
        public function setPersuasion_mastery(int | null  $data){
            if(in_array($data, [self::SKILL_NONE, self::SKILL_MASTERY])){
                $this->_persuasion_mastery = $data;
                return true;
            } else {
                throw new Exception("La valeur envoyée n'est pas correcte.");
            }
        }
        public function setRepresentation_bonus(string | int | null  $data){
            $this->_representation_bonus = $data;
            return true;
        }
        public function setRepresentation_mastery(int | null  $data){
            if(in_array($data, [self::SKILL_NONE, self::SKILL_MASTERY])){
                $this->_representation_mastery = $data;
                return true;
            } else {
                throw new Exception("La valeur envoyée n'est pas correcte.");
            }
        }
        public function setSupercherie_bonus(string | int | null  $data){
            $this->_supercherie_bonus = $data;
            return true;
        }
        public function setSupercherie_mastery(int | null  $data){
            if(in_array($data, [self::SKILL_NONE, self::SKILL_MASTERY])){
               $this->_supercherie_mastery = $data;
               return true;
            } else {
               throw new Exception("La valeur envoyée n'est pas correcte.");
            }
        }

        public function setKamas(string | int | null  $data){
            $this->_kamas = $data;
            return true;
        }
        public function setDrop_(string | int | null  $data){
            $this->_drop_ = $data;
            return true;
        }
        
        public function setOther_item(string | int | null  $data){
            $this->_other_item = $data;
            return true;
        }
        public function setOther_consumable(string | int | null  $data){
            $this->_other_consumable = $data;
            return true;
        }
        public function setOther_spell(string | int | null  $data){
            $this->_other_spell = $data;
            return true;
        }
        
        /* Data = array(
                uniqid => id du consommable,
                quantity => quantité du consommable,
                price => prix du consommable,
                action => remove / add / update
            )
        Js : Npc.update(UniqidS,{action:'add|remove|update', uniqid:'uniqIdC', quantity:'Quantity'},'consumable', IS_VALUE);
        */
        public function setConsumable(array $data){ 
            $managername = ucfirst(get_class($this)) . "Manager";
            $managerN = new $managername;
            if(method_exists($managerN, "getLinkConsumable")){

                $managerC = new ConsumableManager;
                if(!isset($data['uniqid'])){throw new Exception("L'uniqid n'est pas défini");}
                if($managerC->existsUniqid($data['uniqid'])){
                    $consumable = $managerC->getFromUniqid($data['uniqid']); 

                    if(isset($data['action'])){
                        switch ($data['action']) {
                            case 'add':
                                if(isset($data['quantity'])){$quantity=$data['quantity'];} else {$quantity="";}
                                return $managerN->addLinkConsumable($this, $consumable, $quantity);
                
                            case "remove":
                                return $managerN->removeLinkConsumable($this, $consumable);

                            case "update":
                                if($managerN->existsLinkConsumable($this, $consumable)){
                                    $link = $managerN->getLinkConsumable($this, $consumable);
                                    if(isset($data['quantity'])){$quantity=$data['quantity'];} else {$quantity=$link["quantity"];}
                                    return $managerN->updateLinkConsumable($this, $consumable, $quantity);

                                } else {
                                    if(isset($data['quantity'])){$quantity=$data['quantity'];} else {$quantity="";}
                                    return $managerN->addLinkConsumable($this, $consumable, $quantity);
                                }
                            
                            default:
                                throw new Exception("L'action n'est pas valide");
                        }

                    } else {
                        throw new Exception("Une action est requise");
                    }

                }
            } else {
                throw new Exception("Impossible d’associer un consommable à une créature de ce type.");
            }
        }

        /* Data = array(
                uniqid => id de l'équipement,
                quantity => quantité de l'équipement,
                price => prix du consommable,
                action => remove / add / update
            )
            Js : Npc.update(UniqidS,{action:'add|remove|update', uniqid:'uniqIdC', quantity:'Quantity'},'item', IS_VALUE);
        */
        public function setItem(array $data){ 
            $managername = ucfirst(get_class($this)) . "Manager";
            $managerN = new $managername;
            if(method_exists($managerN, "getLinkItem")){

                $managerI = new ItemManager;
                if(!isset($data['uniqid'])){throw new Exception("L'uniqid du l'équipement n'est pas défini");}
                if($managerI->existsUniqid($data['uniqid'])){
                    $item = $managerI->getFromUniqid($data['uniqid']); 

                    if(isset($data['action'])){
                        switch ($data['action']) {
                            case 'add':
                                if(array_key_exists("quantity", $data)){$quantity=$data['quantity'];} else {$quantity="";}
                                return $managerN->addLinkItem($this, $item, $quantity);
                
                            case "remove":
                                return $managerN->removeLinkItem($this, $item);

                            case "update":
                                if($managerN->existsLinkItem($this, $item)){
                                    $link = $managerN->getLinkItemFromItem($this, $item);
                                    if(array_key_exists("quantity", $data)){$quantity=$data['quantity'];} else {$quantity=$link["quantity"];}
                                    return $managerN->updateLinkItem($this, $item, $quantity);

                                } else {
                                    if(array_key_exists("quantity", $data)){$quantity=$data['quantity'];} else {$quantity="";}
                                    return $managerN->addLinkItem($this, $item, $quantity);
                                }
                            
                            default:
                                throw new Exception("L'action n'est pas valide");
                        }

                    } else {
                        throw new Exception("Une action est requise");
                    }

                }

            } else {
                throw new Exception("Impossible d’associer un équipement à une créature de ce type.");
            }
        }

        /* Data = array(
                uniqid => id du spell
            )
            Js : Npc.update(UniqidM,{action:'add|remove|update', uniqid:'uniqIdS'},'spell', IS_VALUE);
        */
        public function setSpell(array $data){ 
            $managername = ucfirst(get_class($this)) . "Manager";
            $managerN = new $managername;
            if(method_exists($managerN, "getLinkSpell")){

                $managerS = new SpellManager;
                if(!isset($data['uniqid'])){throw new Exception("L'uniqid du sort n'est pas défini");}
                if($managerS->existsUniqid($data['uniqid'])){
                    $spell = $managerS->getFromUniqid($data['uniqid']); 

                    if(isset($data['action'])){
                        switch ($data['action']) {
                            case 'add':
                                if($managerN->addLinkSpell($this, $spell)){
                                    return true;
                                }else{
                                    throw new Exception("Erreur lors de l'ajout du sort");
                                }
                
                            case "remove":
                                if($managerN->removeLinkSpell($this, $spell)){
                                    return true;
                                }else{
                                    throw new Exception("Erreur lors de la suppression du sort");
                                }

                            default:
                                throw new Exception("L'action n'est pas valide");
                        }

                    } else {
                        throw new Exception("Une action est requise");
                    }

                }

            } else {
                throw new Exception("Impossible d’associer un sort à une créature de ce type.");
            }
        }

        /* Data = array(uniqid => id du capability)
            Js : Classe.update(UniqidM,{action:'add|remove|update', uniqid:'uniqIdS'},'capability', IS_VALUE);
        */
        public function setCapability(array $data){ 
            $managername = ucfirst(get_class($this)) . "Manager";
            $managerN = new $managername;
            if(method_exists($managerN, "getLinkCapability")){

                $managerS = new CapabilityManager;
                if(!isset($data['uniqid'])){throw new Exception("L'uniqid de l'aptitude n'est pas défini");}
                if($managerS->existsUniqid($data['uniqid'])){
                    $capability = $managerS->getFromUniqid($data['uniqid']); 

                    if(isset($data['action'])){
                        switch ($data['action']) {
                            case 'add':
                                if($managerN->addLinkCapability($this, $capability)){
                                    return true;
                                }else{
                                    throw new Exception("Erreur lors de l'ajout de l'aptitude");
                                }
                
                            case "remove":
                                if($managerN->removeLinkCapability($this, $capability)){
                                    return true;
                                }else{
                                    throw new Exception("Erreur lors de la suppression de l'aptitude");
                                }

                            default:
                                throw new Exception("L'action n'est pas valide");
                        }

                    } else {
                        throw new Exception("Une action est requise.");
                    }

                }

            } else {
                throw new Exception("Impossible d’associer une aptitude à une créature de ce type.");
            }
        }

    // AUTRES FONCTIONS
        static function distribCaractericticsPoints(int $pointsTotals = 1, int $level = 1, array $caracteristicsFillingOrder = []) {
            $caracteristics = ['strong', 'intel', 'agi', 'chance','vitality', 'sagesse'];

            foreach ($caracteristics as $caracteristic) {
                if (!in_array($caracteristic, $caracteristicsFillingOrder)) {
                    array_push($caracteristicsFillingOrder, $caracteristic);
                }
            }

            $result = [
                'strong' =>  Creature::BALANCE_CARACTERISTICS_MAIN['classe']['base'],
                'vitality' =>  Creature::BALANCE_CARACTERISTICS_MAIN['classe']['base'],
                'sagesse' =>  Creature::BALANCE_CARACTERISTICS_MAIN['classe']['base'],
                'intel' =>  Creature::BALANCE_CARACTERISTICS_MAIN['classe']['base'],
                'agi' =>  Creature::BALANCE_CARACTERISTICS_MAIN['classe']['base'],
                'chance' => Creature::BALANCE_CARACTERISTICS_MAIN['classe']['base']
            ];

            $caracteristiquesMax = round(($level / 2) + 1);
            if($caracteristiquesMax > Creature::BALANCE_CARACTERISTICS_MAIN['classe']['max_base']){
                $caracteristiquesMax = Creature::BALANCE_CARACTERISTICS_MAIN['classe']['max_base'];
            } elseif($caracteristiquesMax < Creature::BALANCE_CARACTERISTICS_MAIN['classe']['min']){
                $caracteristiquesMax = Creature::BALANCE_CARACTERISTICS_MAIN['classe']['min'];
            }

            foreach ($caracteristicsFillingOrder as $caracteristic) {
                if($pointsTotals > 0) {
                    $points = $pointsTotals > $caracteristiquesMax ? $caracteristiquesMax : $pointsTotals;
                    $pointsTotals -= $points;
                    if($points < Creature::CARACTERISTICS[$caracteristic]['classe']['min']){$points = Creature::CARACTERISTICS['intel']['classe']['min'];}
                    if($points > Creature::CARACTERISTICS[$caracteristic]['classe']['max_base']){$points = Creature::CARACTERISTICS['intel']['classe']['max_base'];}
                    $result[$caracteristic] += $points;
                }
            }
            return $result;
        }
}