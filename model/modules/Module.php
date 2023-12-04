<?php
class Module extends Content
{
    // Permet de modifier le contenu des sections avant d'être affiché. La fonction se trouve dans la classe Section et les paramètre sont ici.
    // Doit être déclarer. Si aucune modification doit être faites, alors laisser la variable vide.
      const SOLVE_ADD_CLASS_TO_KEYWORD = [
        "text-vitality-d-3" => [
          "vitality",
          "vita"
        ],
        "text-sagesse-d-3" => [
          "sagesse"
        ],
        "text-intel-d-3" => [
          "intelligence",
          "intel"
        ],
        "text-chance-d-3" => [
          "chance"
        ],
        "text-agi-d-3" => [
          "aigilité",
          "agi"
        ],
        "text-force-d-3" => [
          "force"
        ]
      ];

    // DROITS DES UTILISATEURS
      const USER_RIGHT = [
          "capability" => User::RIGHT_READ,
          "condition" => User::RIGHT_READ,
          "classe" => User::RIGHT_READ,
          "consumable" => User::RIGHT_READ,
          "item" => User::RIGHT_READ,
          "mob" => User::RIGHT_READ,
          "npc" => User::RIGHT_READ,
          "page" => User::RIGHT_READ,
          "section" => User::RIGHT_READ,
          "shop" => User::RIGHT_READ,
          "spell" => User::RIGHT_READ,
          "user" => User::RIGHT_NO,
          "social" => User::RIGHT_READ,
          "ArtificialIntelligence" => User::RIGHT_NO,
      ];

    // Style couleur
      const COLOR_CUSTOM =  [
        "main" => "blue-grey",
        "secondary" => "teal",
        "tertiary" => "deep-purple",
        "force" => "brown",
        "strong" => "brown",
        "terre" => "brown",
        "intel" => "red",
        "feu" => "red",
        "agi" => "green",
        "air" => "green",
        "chance" => "blue",
        "eau" => "blue",
        "vitality" => "amber",
        "sagesse" => "purple",
        "life" => "deep-orange",
        "level" => "teal",
        "master_bonus" => "orange",
        "time_before_use_again" => "blue-grey",
        "casting_time" => "blue",
        "dodge_pa" => "yellow",
        "dodge_pm" => "lime",
        "po" => "teal",
        "po-editable" => "blue-grey",
        "pa" => "deep-orange",
        "pm" => "cyan",
        "cast-per-turn" => "purple",
        "cast-per-target" => "indigo",
        "sight-line" => "indigo",
        "number-between-two-cast" => "pink",
        "ini" => "deep-purple",
        "invocation" => "amber",
        "touch" => "lime",
        "actif" => "amber",
        "twohands" => "grey",
        "kamas" => "yellow",
        "ca" => "grey",
        "fuite" => "light-green",
        "tacle" => "cyan",
        "neutre" => "grey",
        "shield" => "blue-grey",
        "mastery" => "bleu-grey",
        "expertise" => "bleu",
        "terre-feu" => ["brown", "red"],
        "terre-air" => ["brown", "green"],
        "terre-eau" => ["brown", "blue"],
        "feu-air" => ["red", "green"],
        "feu-eau" => ["red", "blue"],
        "air-eau" => ["green", "blue"],
        "terre-feu-air" => ["brown","red", "green"],
        "terre-feu-eau" => ["brown","red", "blue"],
        "terre-air-eau" => ["brown","green", "blue"],
        "feu-air-eau" => ["red","green", "blue"],
        "terre-feu-air-eau" => ["brown","red","green", "blue"]
      ];

}