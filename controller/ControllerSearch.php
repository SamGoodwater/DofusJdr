<?php
class ControllerSearch extends Controller{

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


  public function search(){
    $array = array();
    if(isset($_REQUEST['search_in'])){
      $search_in = $_REQUEST['search_in'];
    } else {
      $search_in = self::SEARCH_IN_ALL;
    }

    if(isset($_REQUEST['term'])){
      $term = $_REQUEST['term']; $limit = null; $parameter = ""; $action = ControllerSearch::SEARCH_DONE_REDIRECT; $only_usable = true;
      if(isset($_REQUEST["parameter"])){if(!empty($_REQUEST["parameter"])){$parameter = $_REQUEST["parameter"];}}
      if(isset($_REQUEST["limit"])){if(!empty($_REQUEST["limit"])){$limit = $_REQUEST["limit"];}}
      if(isset($_REQUEST["action"])){if(!empty($_REQUEST["action"])){$action = $_REQUEST["action"];}}
      if(isset($_REQUEST["only_usable"])){if(!empty($_REQUEST["only_usable"])){$only_usable = $this->returnBool($_REQUEST["only_usable"]);}}

      switch ($search_in) {
        case $this::SEARCH_IN_SECTION:
          $controllers = [
            new ControllerSection
          ];
        break;
        case $this::SEARCH_IN_SPELL:
          $controllers = [
            new ControllerSpell
          ];
        break;
        case $this::SEARCH_IN_ITEM:
          $controllers = [
            new ControllerItem
          ];
        break;
        case $this::SEARCH_IN_MOB:
          $controllers = [
            new ControllerMob
          ];
        break;
        case $this::SEARCH_IN_CLASSE:
          $controllers = [
            new ControllerClasse
          ];
        break;
        case $this::SEARCH_IN_NPC:
          $controllers = [
            new ControllerNpc
          ];
        break;
        case $this::SEARCH_IN_SHOP:
          $controllers = [
            new ControllerShop
          ];
        break;
        case $this::SEARCH_IN_CONSUMABLE:
          $controllers = [
            new ControllerConsumable
          ];
        break;
        
        default:
          $controllers = [
            new ControllerSection,
            new ControllerItem,
            new ControllerClasse,
            new ControllerConsumable,
            new ControllerMob,
            new ControllerNpc,
            new ControllerShop,
            new ControllerSpell
          ];
        break;
      }

      foreach ($controllers as $controller) {
        $new_array = $controller->search($term, $action, $parameter, $limit, $only_usable);
        if(!empty($new_array)){
          $array = array_merge($array, $new_array);
        }
      }
      if(empty($array)){
        $array[] = [
          'visual' =>"Aucun résultats trouvés",
          'label' => "Aucun résultats"
        ];
      }
    } else {
      $array[] = [
        'error' => true,
        'visual' =>"Erreur, aucun therme spécifié pour la recherche.",
        'label' => "Erreur"
      ];
    }

    echo json_encode($array);
    flush();
  }
}
 