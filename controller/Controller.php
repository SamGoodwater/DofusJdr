<?php

abstract class Controller{

  use CheckingFunctions, SecurityFct; 

  const IS_INPUT = 0;
  const IS_VALUE = 1;
  const IS_CHECKBOX = 2;
  const IS_CKEDITOR = 3;
  const IS_FILE = 4;

  const SIZE_SM = 576;
  const SIZE_MD = 768;
  const SIZE_LG = 993;
  const SIZE_XL = 1200;
  const SIZE_XXL = 1400;
  const SIZE_FL = -1;
  const RESPONSIVE = "responsive";


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

  static function convertLevel($val){
    $val = trim($val);
    if(is_numeric($val)){
      if($val <= 0){
        return 0;
      } else {
        return round($val / 10);
      }
    } else {
      return -666;
    }  
  }

  static function convertStat($val){
    $val = trim($val);
    if(is_numeric($val)){
      if($val <= 0){
        return 0;
      } else {
        return round($val / 10);
      }
    } else {
      return -666;
    }  
  }

  // Rapporte la valeur d'initiative sur 20 en partant d'un max de 2000
  static function convertIni($val){
    $val = trim($val);
    if(is_numeric($val)){
      if($val <= 0){
        return 0;
      } else {
        return round($val / 100);
      }
    } else {
      return -666;
    }  
  }

  static function convertVitality($val){
    $val = trim($val);
    if(is_numeric($val)){
      if($val <= 0){
        return 1;
      }elseif($val > 0 && $val <= 100) {
        return round($val * 2 / 10 + 10 ); // 10 à 20
      }elseif($val > 100 && $val <= 500) {
        return round($val / 10 + 10); // 20 à 50
      }elseif($val > 500 && $val <= 1000) {
        return round($val / 10); // 50 à 100
      }elseif($val > 1000 && $val <= 5000) {
        return round($val / 20 + 50); // 100 à 300
      }elseif($val > 5000 && $val <= 10000) {
        return round($val / 16); // 312 à 625
      }elseif($val > 10000) {
        return round($val / 15); // 666
      }else{
        return -666;
      }

    } else {
      return -666;
    }  
  }

  // Met un pourcentage d'esquive sur un échelle de 20
  static function convertDodge($val){
    $val = trim($val);
    if(is_numeric($val)){
      if($val <= 0){
        return 0;
      } else {
        return round($val * 20 / 100);
      }
    } else {
      return -666;
    }  
  }

  // Met un pourcentage de résistance sur un échelle de 5
  static function convertRes($val){
    $val = trim($val);
    if(is_numeric($val)){
      if($val <= 0){
        return 0;
      } else {
        return round($val * 5 / 100);
      }
    } else {
      return -666;
    }  
  }

  // Permet de calculer une expression (exp) en changement les termes par des valeurs [name1 => value1, ...]
  static function calcExp($exp, $vars = []){
      if(!empty($vars)){
          foreach ($vars as $name => $value) {
              if(is_numeric($value)){
                  $exp = str_replace($name, $value, $exp);
              }
          }
      }
      $exp = str_replace(',', ".", $exp);
      $exp = str_replace(' ', "", $exp);
      try {
          $exp = eval("return ".$exp .";");
      } catch (\Throwable $th) {
      }

      if(is_numeric($exp)){
          return round($exp);
      } else {
          return "error";
      }
  }
}
