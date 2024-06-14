<?php
class ControllerView extends Controller{

    const PATH_ROOT = "view/display/";

    const VIEW_PANEL = 0;
    const VIEW_DETAILED_CARD = 1;
    const VIEW_MINIMAL_CARD = 2;
    const VIEW_TABLE = 3;
    const VIEW_TEXT = 4;
    const VIEW_LIST = 5;

    const VIEWS = [
        self::VIEW_PANEL => "Panneau",
        self::VIEW_DETAILED_CARD => "Carte détaillée",
        self::VIEW_MINIMAL_CARD => "Carte simplifiée",
        self::VIEW_TABLE => "Tableau",
        self::VIEW_TEXT => "Texte",
        self::VIEW_LIST => "Liste"
    ];

    protected $_view = self::VIEW_PANEL;
    protected $_obj_type = null;

    public function getTemplate() {
        $return = [
            'state' => false,
            'value' => "",
            'error' => 'erreur inconnue'
        ];

        if(isset($_REQUEST['view']) && isset($_REQUEST['obj_type'])){
            if(!$this->setObj_type($_REQUEST['obj_type'])){
                $return['error'] = "Type d'objet inconnu";
            } else if(!$this->setView($_REQUEST['view'])) {
                $return['error'] = "Vue inconnue";
            } else {
                $return['state'] = true;
                $return['value'] = file_get_contents($this->getPath());
                $return['error'] = "";
            }
        } else {
            $return['error'] = "Vue inconnue";
        }

        echo json_encode($return);
        flush();
    }

  // GETTERS ET SETTERS
    public function getView(): int {
        return $this->_view;
    }
    public function setView(int $view): bool {
        if(array_key_exists($view, self::VIEWS)){
            $this->_view = $view;
            return true;
        }
        return false;
    }

    public function getObj_type(): string {
        return $this->_obj_type;
    }
    public function setObj_type(string $obj_type): bool {
        if(file_exists($path = self::PATH_ROOT . $obj_type)){
            $this->_obj_type = $obj_type;
            return true;
        } 
        return false;
    }

    protected function getPath(): string {
        $filename = "panel.php";
        switch ($this->getView()) {
            case self::VIEW_DETAILED_CARD:
                $filename = "detailed_card.php";
            break;
            case self::VIEW_MINIMAL_CARD:
                $filename = "minimal_card.php";
            break;
            case self::VIEW_TABLE:
                $filename = "table.php";
            break;
            case self::VIEW_TEXT:
                $filename = "text.php";
            break;
            case self::VIEW_LIST:
                $filename = "list.php";
            break;
        }
        return self::PATH_ROOT . $this->_obj_type . "/view/" . $filename;
    }

}