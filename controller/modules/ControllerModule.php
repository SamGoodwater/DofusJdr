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
        public const SEARCH_IN_MOB_RACE = 11;
        public const SEARCH_IN_RESSOURCE = 12;

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
                // "social",
                "mob_race",
                "ressource"
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
            ],
            self::SEARCH_IN_MOB_RACE => [
                "mob_race"
            ],
            self::SEARCH_IN_RESSOURCE => [
                "ressource"
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
        public const SEARCH_DONE_ADD_RESSOURCE_TO_ITEM = 15;
}
