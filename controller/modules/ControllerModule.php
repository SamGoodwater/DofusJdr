<?php

abstract class ControllerModule extends Controller{

    // SEARCH
        public const SEARCH_IN_ALL = 0;
        public const SEARCH_IN_SECTION = 1;
        public const SEARCH_IN_SPELL = 2;
        public const SEARCH_IN_ITEM = 3;
        public const SEARCH_IN_MOB = 4;
        public const SEARCH_IN_CLASSE = 5;
        public const SEARCH_IN_NPC = 6;
        public const SEARCH_IN_SHOP = 7;
        public const SEARCH_IN_CONSUMABLE = 8;
        public const SEARCH_IN_CAPABILITY = 9;
        public const SEARCH_IN_SOCIAL = 10;

        public const SELECT_CONTROLLER_FROM_SEARCH_IN = [
            self::SEARCH_IN_ALL => [
                "section",
                "item",
                "classe",
                "consumable",
                "mob",
                "npc",
                "shop",
                "spell",
                "capability",
                "social"
            ],
            self::SEARCH_IN_SECTION => [
                "section"
            ],
            self::SEARCH_IN_SPELL => [
                "spell"
            ],
            self::SEARCH_IN_CAPABILITY => [
                "capability"
            ],
            self::SEARCH_IN_ITEM => [
                    "item"
            ],
            self::SEARCH_IN_MOB => [
                "mob"
            ],
            self::SEARCH_IN_CLASSE => [
                "classe"
            ],
            self::SEARCH_IN_NPC => [
                "npc"
            ],
            self::SEARCH_IN_SHOP => [
                "shop"
            ],
            self::SEARCH_IN_CONSUMABLE => [
                "consumable"
            ],
            self::SEARCH_IN_CAPABILITY => [
                "capability"
            ],
            self::SEARCH_IN_SOCIAL => [
                "social"
            ]
        ];
    
        public const SEARCH_DONE_REDIRECT = 0;
        public const SEARCH_DONE_ADD_CONSUMABLE_TO_SHOP = 1;
        public const SEARCH_DONE_ADD_ITEM_TO_SHOP = 2;
        public const SEARCH_DONE_ADD_MOB_TO_SPELL = 3;
        public const SEARCH_DONE_ADD_SPELL_TO_MOB = 4;
        public const SEARCH_DONE_ADD_SPELL_TO_CLASSE = 5;
        public const SEARCH_DONE_GET_SPELL = 6;
        public const SEARCH_DONE_ADD_SPELL_TO_NPC = 7;
        public const SEARCH_DONE_ADD_ITEM_TO_NPC = 8;
        public const SEARCH_DONE_ADD_CONSUMABLE_TO_NPC = 9;
        public const SEARCH_DONE_ADD_CAPABILITY_TO_CLASSE = 10;
        public const SEARCH_DONE_ADD_CAPABILITY_TO_NPC = 11;
        public const SEARCH_DONE_ADD_CAPABILITY_TO_MOB = 12;
        public const SEARCH_DONE_GET_CAPABILITY = 13;
        public const SEARCH_DONE_ADD_TO_BOOKMARK = 14;

  // Constantes de calcul des stats et d'équilibrages - DEVRA PASSER DANS UN FICHIER JSON
    const BALANCE_PA = [
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
            "bonus" => 10
        ]
    ];
    const BALANCE_PM = [
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
    ];
    const BALANCE_PO = [
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
    ];
    const BALANCE_TOUCH = [
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
    ];
    const BALANCE_INVOCATION = [
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
    ];
    const BALANCE_INI = [
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
    ];
    const BALANCE_TACLE = [
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
    ];
    const BALANCE_SPEFICIFIC_MAIN = [
        "classe" => [
            "max_item" => 10,
            "max_base" => 10,
            "min" => -2,
            "base" => -1,
            "expression_item" => "1 + (level-3)/2 + 1/level",
            "expression_base" => "1 + (level-3)/2 + 1/level",
            "bonus_item_max" => 5
        ],
        "mob" => [
            "max" => 20,
            "min" => -2,
            "base" => -1,
            "expression" => "-1 + 0,578947368 * (level + 2)",
            "bonus" => 10
        ]
    ];
    const BALANCE_SKILL = [
        "classe" => [
            "max_item" => 18,
            "max_base" => 10,
            "min" => -2,
            "base" => -1,
            "expression_item" => "-1 + 0,9 * (level - 1)",
            "expression_base" => "-1 + 0,421052632 * ((level - 1) - (0,4 * level / 24))",
            "bonus_item_max" => 8
        ]
    ];
    const BALANCE_RECHARGE_WAKFU = [
        "classe" => [
            "max_item" => 20,
            "max_base" => 10,
            "min" => 0,
            "base" => 1,
            "expression_item" => "1 + 1 * (level - 1)",
            "expression_base" => "1 + (level-3)/2 + 1/level",
            "bonus_item_max" => 10
        ]
    ];
    const BALANCE_RES = [
        "classe" => [
            "max_item" => 5,
            "max_base" => 0,
            "min" => 0,
            "base" => 0,
            "expression_item" => "0,263157895 * (level - 1)",
            "expression_base" => "0",
            "bonus_item_max" => 5
        ],
        "mob" => [
            "max" => 10,
            "min" => 0,
            "base" => 0,
            "expression" => "0,263157895 * (level - 1)",
            "bonus" => 10
        ]
    ];
    const BALANCE_CA = [
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
    ];
    const BALANCE_DODGE = [
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
    ];
    const BALANCE_LIFE = [
        "classe" => [
            "max_item" => 500,
            "max_base" => 450,
            "min" => 9,
            "base" => 16,
            "expression_item" => "10 + level + level * dice / 1.6",
            "expression_base" => "10 + level * dice / 2",
            "bonus_item_max" => 50
        ],
        "mob" => [
            "max" => 600,
            "min" => 1,
            "base" => 10,
            "expression" => "10 + 12,63157895 * (level - 1)",
            "bonus" => 100
        ]
    ];
  // END
}
