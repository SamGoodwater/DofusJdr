<?php
class ControllerOpenai
{
    public function call(){
        $currentUser = ControllerConnect::getCurrentUser();
        $return = [
          'state' => false,
          'value' => "",
          'error' => 'erreur inconnue',
          'script' => "",
          "uniqid" => ""
        ];
        if($currentUser->getRight('ArtificialIntelligence', User::RIGHT_READ)){
            $ai = new ArtificialIntelligence();
            if(isset($_REQUEST['template'])){
                
                $data = [];
                if(isset($_REQUEST['data'])){
                    if(is_array($_REQUEST['data']) && !empty($_REQUEST['data'])){
                        $data = $_REQUEST['data'];
                    }
                }

                $state = $ai->dispatch(
                    name_file : $_REQUEST['template'],
                    data : $data
                );
                if($state['state'] !== true){
                    $return['error'] = $state['value'];
                } else {
                    $return['value'] = $state['value'];
                    $return['state'] = true;
                }
            } else {
                $return['error'] = "Le template n'est pas défini";
            }

        } else {
          $return['error'] = "Vous devez être connecter pour interroger l'IA";
        }
        
        echo json_encode($return);
        flush();
      }

}
