<?php

class ControllerFile extends Controller{

  public function cleanTemp(){
    FileManager::clearTemp();
  }

  public function remove(){
    $return = [
      'state' => false,
      'value' => "",
      'error' => 'erreur inconnue',
      'script' => ""
    ];
    $currentUser = ControllerConnect::getCurrentUser();
    if(!$currentUser->getRight('item', User::RIGHT_WRITE)){
      $return['error'] = "Vous n'avez pas les droits pour écrire cet objet";}else{

      if(!isset($_REQUEST['path'])){
        $return['error'] = 'Impossible de récupérer les données';
      } else {
        $path = FileManager::formatPath($_REQUEST['path'], false, false);
        if (strpos($path, "..") !== false || strpos($path, "/.") !== false || strpos($path, "./") !== false  || strpos($path, ".\\") !== false  || strpos($path, "\\.") !== false) {
          $return['error'] = "Par sécuirté, il n'est pas possible de manipuler le chemin lors de la suppression.";
        } else {
          $file = new File($path);
          if(FileManager::inMedias($file)){
            if(file_exists($path)){
              if($file->existThumbnail()){
                $thumb = $file->getThumbnail();
                FileManager::remove($thumb->getPath());
              }
              if(FileManager::remove($path)){
                $return['script'] = "$('#".$file->getName(Content::FORMAT_BRUT, false)."').parent().remove();";
                $return['state'] = true;
              }
            }
            $return['error'] = "Impossible de supprimer le fichier";
          } else {
            $return['error'] = "Le fichier ne se trouve pas dans un répertoire autorisé à être manipulé";
          }
        }
      }

    }

    echo json_encode($return);
    flush();
  }

}
 