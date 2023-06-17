<?php
class ControllerSearch extends Controller{

  public function search(){
    $array = array();
    if(isset($_REQUEST['search_in'])){
      $search_in = $_REQUEST['search_in'];
    } else {
      $search_in = ControllerModule::SEARCH_IN_ALL;
    }

    if(isset($_REQUEST['term'])){
      $term = $_REQUEST['term']; $limit = null; $parameter = ""; $action = ControllerModule::SEARCH_DONE_REDIRECT; $only_usable = true;
      if(isset($_REQUEST["parameter"])){if(!empty($_REQUEST["parameter"])){$parameter = $_REQUEST["parameter"];}}
      if(isset($_REQUEST["limit"])){if(!empty($_REQUEST["limit"])){$limit = $_REQUEST["limit"];}}
      if(isset($_REQUEST["action"])){if(!empty($_REQUEST["action"])){$action = $_REQUEST["action"];}}
      if(isset($_REQUEST["only_usable"])){if(!empty($_REQUEST["only_usable"])){$only_usable = $this->returnBool($_REQUEST["only_usable"]);}}

      if(isset(ControllerModule::SELECT_CONTROLLER_FROM_SEARCH_IN[$search_in])){
        foreach (ControllerModule::SELECT_CONTROLLER_FROM_SEARCH_IN[$search_in] as $name) {
          $controllerName = "Controller".ucfirst($name);
          $controllers[] = new $controllerName;
        }
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
 